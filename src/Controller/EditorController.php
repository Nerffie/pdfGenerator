<?php
// src/Controller/Html.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

use Doctrine\ODM\MongoDB\DocumentManager as DocumentManager;
use Psr\Log\LoggerInterface;

use App\Document\Contrat;
use App\Document\Variable;


class EditorController
{
    // Fonction qui récupère les variables dans un contrat
    function parcoursVar($parsed_json)
    {
        $jsonVar = null;

        //Parcours du contrat
        foreach ($parsed_json->ops as $v) {
            $chaine = $v->insert;

            // Parcours de la chaine
            $taille = strlen($chaine);
            $indice = 0;
            while($indice < $taille)
            {
                // Supprimer ce qui a déja été parcouru
                $chaine = substr($chaine, $indice);
                $chaine = '\n'.$chaine;
                $taille = strlen($chaine);

                // Récupérer le début de la variable
                $posDeb = strpos($chaine, '{{');

                if($posDeb != NULL)
                {
                    // Récupérer le milieu de la variable
                    $posMil = strpos($chaine, '||');
                    if($posMil != NULL)
                    {
                        // Récupérer la variable
                        $variable = substr($chaine, $posDeb+2, $posMil-$taille);
                        // Récupérer la fin de la variable
                        $posFin = strpos($chaine, '}}');

                        // Vérifier que la variable ne se trouve pas déjà dans le JSON des variables 
                        $pos = strpos($jsonVar, $variable);

                        if($pos === false)
                        {
                            if($posFin != NULL)
                            {
                                // Récupérer le type de la variable
                                $type = substr($chaine, $posMil+2, $posFin-$taille);
                                // Ajouter la variable dans le JSON
                                $jsonVar .= '"'.$variable . '" : "' . $type . '", ';
                                $indice = $posFin + 3;
                            }
                            else
                            {
                                // Il n'y a pas }}
                                $indice = $taille;
                            }
                        }
                        else
                        {
                            // La variable se trouve déja dans le JSON
                            $indice = $posFin+3;
                        }
                    }
                    else
                    {
                        // Il n'y a pas ||
                        $indice = $taille;
                    }
                }
                else
                {
                    // Il n'y a pas {{
                    $indice = $taille;
                }
            }
        }

        return $jsonVar;
    }

    // Fonction qui récupère le contrat en fonction de l'identifiant
    /**
     * @Route("/editor/id/{id}", name="get_one_contract", methods={"GET"})
     */
    public function getContractById(DocumentManager $dm, $id): JsonResponse
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        // Récupérer le repo de la BD
        $repo = $dm->getRepository(Contrat::class);
        // Requete sur la BD
        $contracts = $repo->find($id);
        $data = $serializer->serialize($contracts,'json');

        return new JsonResponse($data, Response::HTTP_OK);
    }

    // Fonction qui enregistre un contrat dans la base de données et récupère les variable de ce contrat pour les enregistrer
    /**
     * @Route("/editor", name="post_one_contract", methods={"POST"})
     */
    public function postContractById(loggerInterface $logger, DocumentManager $dm,Request $request): JsonResponse
    {
        // Afficher les paramêtres
        $logger->info($request->getContent());

        // Créer un nouveau contrat
        $contrat = new Contrat();
        $contrat->setOps($request->getContent());
        //$contrat->setNom($nom);
        $dm->persist($contrat); 
       
        //Parser Contrat pour créer le json des variables
        $parsed_json = json_decode($contrat->getOps());
        $jsonVar = self::parcoursVar($parsed_json);
        $jsonVar = substr($jsonVar, 0, -2);

        // Créer l'objet contenant les variable
        $variableBD = new Variable();
        $variableBD->setVar($jsonVar);
        $variableBD->setIdContrat($contrat->getId());
        $dm->persist($variableBD); 

        // Enregistrer les variables et la contrat dans la BD
        $dm->flush(); 

        return new JsonResponse(['status' => 'Contract   Created'], Response::HTTP_CREATED);
    }

    // Fonction qui modifie un contrat déja enregistré 
    /**
     * @Route("/editor/id/{id}", name="update_one_contract", methods={"PUT"})
     */
    public function updateContractById(DocumentManager $dm, Request $request,$id): JsonResponse
    {
        // Récupérer le repo de la BD
        $repo = $dm->getRepository(Contrat::class);
        // Requete sur la BD
        $contrat = $repo->find($id);
        // Modifier le contrat
        $contrat->setOps($request->getContent());
        $dm->persist($contrat); 
        
        //Parser Contrat pour créer le json des variables
        $parsed_json = json_decode($contrat->getOps());
        $jsonVar = self::parcoursVar($parsed_json);
        $jsonVar = substr($jsonVar, 0, -2);

        // Récupérer le repo de la BD
        $repoVar = $dm->getRepository(Variable::class);
        // Requete sur la BD
        $variableBD = $repoVar->findOneBy(['idContrat' => $id]);
        // Modifier les variables
        $variableBD->setVar($jsonVar);
        $dm->persist($variableBD);
        
        // Enregistrer les variables et le contrat dans la BD
        $dm->flush();

        return new JsonResponse(['status' => 'Contract '.$id. ' Updated'], Response::HTTP_OK);
    }


    
    
}