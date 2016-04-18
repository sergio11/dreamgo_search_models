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
         $tags = $this->getDataFromRequest($request);
         $ids = $this->termsService->saveTags($tags);
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
