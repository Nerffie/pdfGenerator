<?php
// src/Controller/Html.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Document\Contrat;
use App\Document\Variable;
use Doctrine\ODM\MongoDB\DocumentManager as DocumentManager;
use Psr\Log\LoggerInterface;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


class FormController
{

    /**
     * @Route("/form/{id}", name="get_form", methods={"GET"})
     */
    public function getFormById(loggerInterface $logger,DocumentManager $dm, $id): JsonResponse
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $repo = $dm->getRepository(Variable::class);
        $variable = $repo->findOneBy(['idContrat' => $id]);
        $logger->info($variable->getVar());
        $data = $serializer->serialize($variable,'json');

        

        

        return new JsonResponse($data, Response::HTTP_OK);
    }


    /**
     * @Route("/form/{id}", name="post_form", methods={"POST"})
     */
    public function postFormById(loggerInterface $logger,Request $request,$id): JsonResponse
    {
      
        $logger->info($request->getContent());
        $logger->warning($id);
        //$data = json_decode($request->getContent(), true);
        //$logger->warning($data);
       
        //$firstName = $data['firstName'];
        //$lastName = $data['lastName'];
        //var_dump($data); console log ?

        return new JsonResponse(['status' => 'Form  '.$id. ' Submitted'], Response::HTTP_CREATED);
    }    
}