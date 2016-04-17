<?php

namespace App\Controllers;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;


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
    
    public function savePredictionModel(Application $app, Request $request){
        $model = $request->request->get('model');
        $filename = preg_replace('/[^a-zA-Z0-9-_\.]/','', strtolower($model['name']));
        $file  = $app['upload_file_dir'] . $filename . ".xml";
        $modelXML = $app['serializer']->serialize(array('model' => $model), 'xml');
        try {
            $app['filesystem']->dumpFile($file, $modelXML);
            $response = array('error' => false, 'msg' => 'model created succesfully');
        } catch (IOExceptionInterface $e) {
            $response = array('error' => true, 'msg' => "There was an error creating the file ".$model['name']);
        }
        return new JsonResponse($response);
    }

    public function delete(Application $app, $id)
    {
        $model = $this->modelsService->getModelById($id);
        $result = $this->modelsService->delete($id);
        $result && @unlink($app['upload_file_dir'] . $model['name']);
        return new JsonResponse($result);
    }

}
