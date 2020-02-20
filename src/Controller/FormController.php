<?php
// src/Controller/Html.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class FormController
{

    /**
     * @Route("/form/{id}", name="get_form", methods={"GET"})
     */
    public function getFormById($id): JsonResponse
    {
        $data = [];

            $data[] = [
                'id' => $id,
                'variable 1' => 'text',
                'variable 2'=>'date'
                
            ];
        

        return new JsonResponse($data, Response::HTTP_OK);
    }


    /**
     * @Route("/form/{id}", name="post_form", methods={"POST"})
     */
    public function postFormById(Request $request,$id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $firstName = $data['firstName'];
        $lastName = $data['lastName'];
        //var_dump($data); console log ?
        return new JsonResponse(['status' => 'Form  '.$id. ' Submitted'], Response::HTTP_CREATED);
    }    
}