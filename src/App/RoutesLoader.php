<?php

namespace App;

use Silex\Application;

class RoutesLoader
{
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->instantiateControllers();

    }

    private function instantiateControllers()
    {
        $this->app['models.controller'] = $this->app->share(function () {
            return new Controllers\ModelsController($this->app['models.service']);
        });
        $this->app['terms.controller'] = $this->app->share(function () {
            return new Controllers\TermsController($this->app['terms.service']);
        });
    }

    public function bindRoutesToControllers()
    {
        $api = $this->app["controllers_factory"];

        //GET Model
        $api->get('/models', "models.controller:getAll");
        $api->get('/models/{start}/{count}', "models.controller:get");
        //POST Model
        $api->post('/models', "models.controller:save");
        //GET Model Tags
        $api->get('/models/{model}/tags', "models.controller:getTags");
        //POST Model Tags
        $api->post('/models/{model}/tags', "models.controller:saveTags");
        //POST Term
        $api->post('/terms', "terms.controller:save");

        $this->app->mount($this->app["api.endpoint"].'/'.$this->app["api.version"], $api);
    }
}

