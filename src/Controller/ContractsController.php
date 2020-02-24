<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

use Doctrine\ODM\MongoDB\DocumentManager as DocumentManager;

use App\Document\Contrat;


class ContractsController
{
    // Fondtion qui récupère tous les contrats de la base de données
    /**
     * @Route("/contracts", name="get_all_contracts", methods={"GET"})
     */
    public function getAll(DocumentManager $dm): JsonResponse
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        // Récupérer le repo de la BD
        $repo = $dm->getRepository(Contrat::class);
        // Requete sur la BD
        $contracts = $repo->findAll();
        $data = $serializer->serialize($contracts,'json');
        
        return new JsonResponse($data, Response::HTTP_OK);
    }
}