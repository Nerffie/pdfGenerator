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


class EditorController
{

    /**
     * @Route("/editor/id/{id}", name="get_one_contract", methods={"GET"})
     */
    public function getContractById(DocumentManager $dm, $id): JsonResponse
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $repo = $dm->getRepository(Contrat::class);
        $contracts = $repo->find($id);
        $data = $serializer->serialize($contracts,'json');
        

        return new JsonResponse($data, Response::HTTP_OK);
    }


    /**
     * @Route("/editor", name="post_one_contract", methods={"POST"})
     */
    public function postContractById(loggerInterface $logger, DocumentManager $dm,Request $request): JsonResponse
    {
        //$data = json_decode($request->getContent(), true);
        $logger->info($request->getContent());

        $contrat = new Contrat();
        $contrat->setOps($request->getContent());
        //$contrat->setNom($nom);
        $dm->persist($contrat); 
        $dm->flush();

        //Parser Contrat pour créer le json des variables
        $parsed_json = json_decode($contrat->getOps());
        $jsonVar = null;

        foreach ($parsed_json->ops as $v) {
            $chaine = $v->insert;

            $taille = strlen($chaine);
            $indice = 0;
            while($indice < $taille)
            {
                $chaine = substr($chaine, $indice);
                $chaine = '\n'.$chaine;
                $taille = strlen($chaine);

                $posDeb = strpos($chaine, '{{');

                if($posDeb != NULL)
                {
                    $posMil = strpos($chaine, '||');
                    if($posMil != NULL)
                    {
                        $variable = substr($chaine, $posDeb+2, $posMil-$taille);
                        $posFin = strpos($chaine, '}}');
                        /* VERIFIER QUE VAR N'EXISTE PAS DANS JSONVAR */
                        /* SI EXISTE $indice = $posFin+3 */

                        if($posFin != NULL)
                        {
                            $type = substr($chaine, $posMil+2, $posFin-$taille);
                            $jsonVar .= '"'.$variable . '" : "' . $type . '", ';
                            $indice = $posFin + 3;
                        }
                        else
                        {
                            $indice = $taille;
                        }
                    }
                    else
                    {
                        $indice = $taille;
                    }
                }
                else
                {
                    $indice = $taille;
                }
            }
        }

        $jsonVar = substr($jsonVar, 0, -2);

        //Enregistrer les Variables dans la BD
        $variableBD = new Variable();
        $variableBD->setVar($jsonVar);
        $variableBD->setIdContrat($contrat->getId());
        $dm->persist($variableBD); 
        $dm->flush(); 

        return new JsonResponse(['status' => 'Contract   Created'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/editor/id/{id}", name="update_one_contract", methods={"PUT"})
     */
    public function updateContractById(Request $request,$id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        //$firstName = $data['firstName'];
        //$lastName = $data['lastName'];
        //var_dump($data); console log ?
        return new JsonResponse(['status' => 'Contract '.$id. ' Updated'], Response::HTTP_OK);
    }
    
}