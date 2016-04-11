<?php


namespace App\Controllers;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class TermsController
{

    protected $termsService;

    public function __construct($service)
    {
        $this->termsService = $service;
    }

    public function getAll()
    {
        return new JsonResponse($this->termsService->getAll());
    }

    public function save(Request $request)
    {
         $ids = [];
         $tags = $this->getDataFromRequest($request);
         for($i = 0, $len = count($tags); $i < $len; $i++){
             $ids[] = $this->termsService->save($tags[$i]);
         }

          return new JsonResponse(array('error' => false, 'ids' => $ids));
    }

    public function delete($id)
    {
        return new JsonResponse($this->termsService->delete($id));
    }

    public function getDataFromRequest(Request $request)
    {
        return $request->request->get("tags");
    }

}
