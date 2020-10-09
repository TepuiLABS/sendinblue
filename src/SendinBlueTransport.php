<?php

namespace Tepuilabs\Sendinblue;

use Illuminate\Mail\Transport\Transport;
use Swift_Attachment;
use Swift_Mime_Headers_UnstructuredHeader;
use Swift_Mime_SimpleMessage;
use Swift_MimePart;

class SendinBlueTransport extends Transport
{
    /**
     * The SendinBlue instance.
     *
     * @var \SendinBlue\Client\Api\TransactionalEmailsApi
     */
    protected $api;

    /**
     * Create a new SendinBlue transport instance.
     *
     * @param  \SendinBlue\Client\Api\TransactionalEmailsApi  $mailin
     * @return void
     */
    public function __construct(\SendinBlue\Client\Api\TransactionalEmailsApi $api)
    {
        $this->api = $api;
    }

    /**
     * {@inheritdoc}
     */
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        $this->beforeSendPerformed($message);

        $this->api->sendTransacEmail($this->buildData($message));

        return 0;
    }

    /**
     * Transforms Swift_Message into SendinBlue's email
     * cf. https://github.com/sendinblue/APIv3-php-library/blob/master/docs/Model/SendSmtpEmail.md
     *
     * @param  Swift_Mime_SimpleMessage $message
     * @return \SendinBlue\Client\Model\SendSmtpEmail
     * @psalm-suppress InvalidIterator
     * @psalm-suppress InvalidArgument
     */
    protected function buildData(Swift_Mime_SimpleMessage $message)
    {
        $smtpEmail = new \SendinBlue\Client\Model\SendSmtpEmail();

        if ($message->getFrom()) {
            $from = $message->getFrom();
            reset($from);
            $key = key($from);
            $smtpEmail->setSender(new \SendinBlue\Client\Model\SendSmtpEmailSender([
                'email' => $key,
                'name' => $from[$key],
            ]));
        }

        if ($message->getTo()) {
            $to = [];
            foreach ($message->getTo() as $email => $name) {
                $to[] = new \SendinBlue\Client\Model\SendSmtpEmailTo([
                    'email' => $email,
                    'name' => $name,
                ]);
            }
            $smtpEmail->setTo($to);
        }

        if ($message->getCc()) {
            $cc = [];
            foreach ($message->getCc() as $email => $name) {
                $cc[] = new \SendinBlue\Client\Model\SendSmtpEmailCc([
                    'email' => $email,
                    'name' => $name,
                ]);
            }
            $smtpEmail->setCC($cc);
        }

        if ($message->getBcc()) {
            $bcc = [];
            foreach ($message->getBcc() as $email => $name) {
                $bcc[] = new \SendinBlue\Client\Model\SendSmtpEmailBcc([
                    'email' => $email,
                    'name' => $name,
                ]);
            }
            $smtpEmail->setBcc($bcc);
        }

        // set content
        $html = null;
        $text = null;

        switch ($message->getContentType()) {
            case 'text/plain':
                $text = $message->getBody();

                break;

            default:
                $html = $message->getBody();

                break;
        }


        $children = $message->getChildren();
        foreach ($children as $child) {
            if ($child instanceof Swift_MimePart && $child->getContentType() == 'text/plain') {
                $text = $child->getBody();
            }
        }

        if ($text === null) {
            $text = strip_tags($message->getBody());
        }

        if ($html !== null) {
            $smtpEmail->setHtmlContent($html);
        }
        $smtpEmail->setTextContent($text);
        // end set content

        if ($message->getSubject()) {
            $smtpEmail->setSubject($message->getSubject());
        }

        if ($message->getReplyTo()) {
            $replyTo = [];
            foreach ($message->getReplyTo() as $email => $name) {
                $replyTo[] = new \SendinBlue\Client\Model\SendSmtpEmailReplyTo([
                    'email' => $email,
                    'name' => $name,
                ]);
            }
            $smtpEmail->setReplyTo(end($replyTo));
        }

        $attachment = [];
        foreach ($message->getChildren() as $child) {
            if ($child instanceof Swift_Attachment) {
                $attachment[] = new \SendinBlue\Client\Model\SendSmtpEmailAttachment([
                    'name' => $child->getFilename(),
                    'content' => chunk_split(base64_encode($child->getBody())),
                ]);
            }
        }
        if (count($attachment)) {
            $smtpEmail->setAttachment($attachment);
        }

        if ($message->getHeaders()) {
            $headers = [];

            foreach ($message->getHeaders()->getAll() as $header) {
                if ($header instanceof Swift_Mime_Headers_UnstructuredHeader) {
                    // remove content type because it creates conflict with content type sets by sendinblue api
                    if ($header->getFieldName() != 'Content-Type') {
                        $headers[$header->getFieldName()] = $header->getValue();
                    }
                }
            }
            $smtpEmail->setHeaders($headers);
        }

        return $smtpEmail;
    }
}
