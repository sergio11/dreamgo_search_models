<?php

namespace App\Controllers;
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

    public function get($start,$count){
        return new JsonResponse($this->modelsService->get($start,$count));
    }

    public function count(){
        return new JsonResponse($this->modelsService->count());
    }

    public function save(Request $request)
    {
        $file = $request->files->get('file');
        //get filename
        $filename = $file->getClientOriginalName();
        //get size
        $size = $file->getClientSize();
        //save model
        $id = $this->modelsService->save(array('name' => $filename, 'size' => $size));
        $path = $_SERVER['DOCUMENT_ROOT'] . '/modelos/public/uploads/';
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


    public function delete($id)
    {

        return new JsonResponse($this->notesService->delete($id));

    }

}
