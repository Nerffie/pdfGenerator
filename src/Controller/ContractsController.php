<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Document\Contrat;



class ContractsController
{
    

    /**
     * @Route("/contracts", name="get_all_contracts", methods={"GET"})
     */
    public function getAll(): JsonResponse
    {
        
            
        /*$contract1 = new Contrat();
        $contract1->setId(20230);
        $contract1->setOps("operations 1");

        $contract2 = new Contrat();
        $contract2->setId(20231);
        $contract2->setOps("operations 2");

        $contract3 = new Contrat();
        $contract3->setId(20232);
        $contract3->setOps("operations 3");*/

        $contract1 = array('id'=>20230,'ops'=>'operations 1');
        $contract2 = array('id'=>20231,'ops'=>'operations 2');
        $contract3 = array('id'=>20232,'ops'=>'operations 3');
        
        $contracts = [$contract1,$contract2,$contract3];
        $data = json_encode($contracts);
            /*$data[] = [
                'id' => 96549876541,
                'firstName' => 'toto',
                
            ];*/
        
        return new JsonResponse($data, Response::HTTP_OK);
    }
}