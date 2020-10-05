<?php

namespace Tepuilabs\Sendinblue\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Tepuilabs\Sendinblue\SendinblueServiceProvider;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        //
    }

    protected function getPackageProviders($app)
    {
        return [
            SendinblueServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        //
    }
}
