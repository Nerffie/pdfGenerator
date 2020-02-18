<?php
// src/Controller/Json.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use App\Document\Contrat;
use App\Document\Variable;
use Doctrine\ODM\MongoDB\DocumentManager as DocumentManager;

class JsonController    
{
    public function receptionContrat(DocumentManager $dm/*, $nom*/)
    {
        /* A FAIRE RECUPERER JSON FRONT */
        $repo = $dm->getRepository(Contrat::class);
        $json = $repo->find("5e418532576000009b0052f4");

        //Enregistrer Contrat dans BD
        /*$contrat = new Contrat();
        $contrat->setOps($json);
        $contrat->setNom($nom);
        $dm->persist($contrat); 
        $dm->flush();*/

        //Parser Contrat pour créer le json des variables
        $parsed_json = json_decode($json->getOps());
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
        $variableBD->setIdContrat($json->getId());
        $dm->persist($variableBD); 
        $dm->flush();           

        return new Response($jsonVar);
              
    }

    public function envoiVar(DocumentManager $dm, $id)
    {
        $repo = $dm->getRepository(Variable::class);
        $jsonVar = $repo->findOneBy(['idContrat' => $id]);
        /* ENVOIE FRONT RESULTAT */
        return new Response($jsonVar->getVar());
    }

            /*$options = array(
                'http' => array(
                    'method'  => 'POST',
                    'header'  => "Content-Type: application/json",
                    'ignore_errors' => true,
                    'timeout' =>  10,
                    'content' => json_encode($jsonVar),
                ),
            );
    
            $context  = stream_context_create($options);
            file_get_contents('http://localhost:3000', false, $context);*/
            
}