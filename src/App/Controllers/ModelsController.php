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

    public function update($id, Request $request)
    {
        $note = $this->getDataFromRequest($request);
        $this->notesService->update($id, $note);
        return new JsonResponse($note);

    }

    public function delete($id)
    {

        return new JsonResponse($this->notesService->delete($id));

    }

}
