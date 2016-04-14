<?php


namespace App\Controllers;
use Silex\Application;
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

    public function save(Application $app, Request $request)
    {
         $ids = [];
         $tags = $this->getDataFromRequest($request);
         $allTags = array_column($this->termsService->getAll(), 'text', 'id');
         for($i = 0, $len = count($tags); $i < $len; $i++){
             $id = array_search($tags[$i]['text'], $allTags);
             $ids[] = $id ? $id : $this->termsService->save($tags[$i]);
         }

          return new JsonResponse(array('error' => false, 'ids' => $ids));
    }

    public function match($text){
        $terms = $this->termsService->match($text);
        return new JsonResponse(array('error' => false, 'terms' => $terms));
    }

    public function getDataFromRequest(Request $request)
    {
        return $request->request->get("tags");
    }

}
