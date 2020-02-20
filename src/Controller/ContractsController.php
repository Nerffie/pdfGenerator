<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Document\Contrat;
use App\Document\Variable;
use Doctrine\ODM\MongoDB\DocumentManager as DocumentManager;
use Psr\Log\LoggerInterface;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;



class ContractsController
{
    

    /**
     * @Route("/contracts", name="get_all_contracts", methods={"GET"})
     */
    public function getAll(LoggerInterface $logger, DocumentManager $dm): JsonResponse
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $repo = $dm->getRepository(Contrat::class);
        $contracts = $repo->findAll();
        $data = $serializer->serialize($contracts,'json');
        
        return new JsonResponse($data, Response::HTTP_OK);
    }
}