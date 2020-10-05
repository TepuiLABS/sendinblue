<?php

namespace Tepuilabs\Sendinblue;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Tepuilabs\Sendinblue\Sendinblue
 */
class SendinblueFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'sendinblue';
    }
}
