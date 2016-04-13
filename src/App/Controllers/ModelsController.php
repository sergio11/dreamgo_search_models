<?php

namespace App\Controllers;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ModelsController
{

    protected $modelsService;

    public function __construct($service)
    {
        $this->modelsService = $service;
    }

    public function getAll()
    {
        return new JsonResponse($this->modelsService->getAll());
    }

    public function get( Request $request, $start,$count){
        $tags = $request->query->get('tags');
        $orderBy = $request->query->get('orderBy');
        return new JsonResponse($this->modelsService->get($start,$count,$tags,$orderBy));
    }
    

    public function count(){
        return new JsonResponse($this->modelsService->count());
    }

    public function save(Application $app, Request $request)
    {
        $file = $request->files->get('file');
        //get filename
        $filename = $file->getClientOriginalName();
        //get size
        $size = $file->getClientSize();
        //save model
        $id = $this->modelsService->save(array('name' => $filename, 'size' => $size));
        $path = $app['upload_file_dir'];
        $file->move($path,$filename);
        return new JsonResponse(array('id' => $id));

    }

    public function saveTags(Request $request, $model){
        $tags = $request->request->get("tags");
        $ids = [];
        for($i = 0, $len = count($tags); $i < $len; $i++){
            $ids[] = $this->modelsService->saveModelTag(array('idmodel' => $model, 'idterm' => $tags[$i]));
        }
         return new JsonResponse(array('ids' => $ids));

    }

    public function getTags($model){
        $tags = $this->modelsService->getModelTags($model);
        return new JsonResponse(array('tags' => $tags));
    }


    public function delete(Application $app, $id)
    {
        $model = $this->modelsService->getModelById($id);
        $result = $this->modelsService->delete($id);
        $result && @unlink($app['upload_file_dir'] . $model['name']);
        return new JsonResponse($result);
    }

}
