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
        $this->app['models.tagged.controller'] = $this->app->share(function () {
            return new Controllers\ModelsTaggedController($this->app['models.tagged.service'], $this->app['terms.service'], $this->app['models.service']);
        });
    }

    public function bindRoutesToControllers()
    {
        $api = $this->app["controllers_factory"];
        
         //GET Model Tags
        $api->get('/models/{model}/tags', "models.tagged.controller:getTags");
        //POST Model Tags
        $api->post('/models/{model}/tags', "models.tagged.controller:saveTags");
        //DELETE Model Tags
        $api->delete('/models/{model}/tags/{tags}', "models.tagged.controller:deleteTags");
        //GET Count Models
        $api->get('/models/count', "models.controller:count");
        //GET Model
        $api->get('/models', "models.controller:getAll");
        //POST Model
        $api->post('/models', "models.controller:save");
        //POST Prediction Model
        $api->post('/prediction-model', "models.controller:savePredictionModel");
        //DELETE Model
        $api->delete('/models/{id}', "models.controller:delete");
        //GET Paginate Models.
        $api->get('/models/{start}/{count}', "models.controller:get");
        
        //POST Term
        $api->post('/terms', "terms.controller:save");
        //GET Terms
        $api->get('/terms/{text}', "terms.controller:match");
        

        $this->app->mount('/', $api);
    }
}

