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
    protected $modelsTaggedService;
    protected $termsService;

    public function __construct($modelsService, $modelsTaggedService, $termsService)
    {
        $this->modelsService = $modelsService;
        $this->modelsTaggedService = $modelsTaggedService;
        $this->termsService = $termsService;
    }

    public function getAll()
    {
        return new JsonResponse($this->modelsService->getAll());
    }

    public function get( Request $request, $start,$count){
        sleep(2);
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
        //generate file name sanitized
        $filenameSanitized = generateFilename($filename);
        //get size
        $size = $file->getClientSize();
        //save model
        $id = $this->modelsService->save(array('name' => $filename, 'filename' => $filenameSanitized, 'size' => $size));
        $path = $app['upload_file_dir'];
        $file->move($path,$filenameSanitized);
        return new JsonResponse(array('id' => $id));
    }
    
    public function savePredictionModel(Application $app, Request $request){

        $model = $request->request->get('model');
        //generate file name
        $filename = generateFilename($model['name']. ".xml");
        //save model
        $id = $this->modelsService->save(array('name' => $model['name'], 'filename' => $filename, 'size' => 0));
        //save new tags
        $tags = $this->termsService->saveTags($model['tags']['tag']);
        //save model tags
        $this->modelsTaggedService->saveModelTags($tags, $id);

        $file  = $app['upload_file_dir'] . $filename;
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
        
        try {
            $model = $this->modelsService->getModelById($id);
            if($model){
                $this->modelsService->delete($id);
                $filename = $app['upload_file_dir'] . $model['filename'];
                $app['filesystem']->remove($filename);
                $response = array('error' => false, 'msg' => 'model dropped succesfully');
            }else{
                $response = array('error' => true, 'msg' => 'The model was not found');
            }
            
        } catch (IOExceptionInterface $e) {
            $response = array('error' => true, 'msg' => "There was an error dropping the model ".$model['name']);
        }
        return new JsonResponse($response);
    }

}
