<?php
// src/Controller/Html.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class EditorController
{

    /**
     * @Route("/editor/id/{id}", name="get_one_contract", methods={"GET"})
     */
    public function getContractById($id): JsonResponse
    {
        $data = [];

            $data[] = [
                'id' => $id,
                'firstName' => 'prenom',
                'trois'=>'soleil'
                
            ];
        

        return new JsonResponse($data, Response::HTTP_OK);
    }


    /**
     * @Route("/editor/id/{id}", name="post_one_contract", methods={"POST"})
     */
    public function postContractById(Request $request,$id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        //$firstName = $data['ops'];
        //$lastName = $data['lastName'];
        var_dump($data);
        return new JsonResponse(['status' => 'Contract  '.$id. ' Created'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/editor/id/{id}", name="update_one_contract", methods={"PUT"})
     */
    public function updateContractById(Request $request,$id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $firstName = $data['firstName'];
        $lastName = $data['lastName'];
        //var_dump($data); console log ?
        return new JsonResponse(['status' => 'Contract '.$id. ' Updated'], Response::HTTP_CREATED);
    }
    
}