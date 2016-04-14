<?php

namespace App;

use Silex\Application;

class ServicesLoader
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function bindServicesIntoContainer()
    {
        //Models Service
        $this->app['models.service'] = $this->app->share(function () {
            return new Services\ModelsService($this->app["db"]);
        });
        //Terms service
        $this->app['terms.service'] = $this->app->share(function () {
            return new Services\TermsService($this->app["db"]);
        });

        //Models tagged Service
        $this->app['models.tagged.service'] = $this->app->share(function () {
            return new Services\ModelsTaggedService($this->app["db"]);
        });
    }
}

