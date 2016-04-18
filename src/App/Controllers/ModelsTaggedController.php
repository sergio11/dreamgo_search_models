<?php

namespace App\Controllers;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ModelsTaggedController
{

    protected $modelsTaggedService;
    protected $termsService;
    protected $modelsService;

    public function __construct($modelsTaggedService, $termsService, $modelsService)
    {
        $this->modelsTaggedService = $modelsTaggedService;
        $this->termsService = $termsService;
        $this->modelsService = $modelsService;
    }

    public function saveTags(Request $request, $model){
        $tags = $request->request->get("tags");
        $this->modelsTaggedService->saveModelTags($tags, $model);
        return new JsonResponse(array('ids' => $tags));
    }

    public function deleteTags(Request $request, $model, $tags){
        if($tags){
            $tags = explode(",", $tags);
            for($i = 0, $len = count($tags); $i < $len; $i++){
                $this->modelsTaggedService->deleteModelTag(array('idmodel' => $model, 'idterm' => $tags[$i]));
            }
        }
        
        return new JsonResponse(array('error' => false));
    }

    public function getTags($model){
        $tags = $this->modelsTaggedService->getModelTags($model);
        return new JsonResponse(array('tags' => $tags));
    }



}