<?php

namespace Tepuilabs\Sendinblue;

use GuzzleHttp\Client as GuzzleClient;

use Illuminate\Mail\MailManager;
use Illuminate\Support\ServiceProvider;
use SendinBlue\Client\Api\SMTPApi;
use SendinBlue\Client\Configuration;

class SendinblueServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app[MailManager::class]->extend('sendinblue', function ($app) {
            return new SendinBlueTransport($this->app->make(SMTPApi::class));
        });
    }

    public function register()
    {
        $this->app->singleton(SMTPApi::class, function ($app) {
            $config = Configuration::getDefaultConfiguration()
            ->setApiKey('api-key', env('SENDINBLUE_API_KEY'));

            return new SMTPApi(
                new GuzzleClient,
                $config
            );
        });
    }
}
