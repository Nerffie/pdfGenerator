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
use Spipu\Html2Pdf\Html2Pdf;
use nadar\quill\Lexer;

use App\Document\Contrat;
use App\Document\Variable;


class FormController
{
    // Fonction qui retourne les variables d'un contrat en fonction de l'identifiant
    /**
     * @Route("/form/{id}", name="get_form", methods={"GET"})
     */
    public function getFormById(loggerInterface $logger,DocumentManager $dm, $id): JsonResponse
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        // Récupérer le repo de la BD
        $repo = $dm->getRepository(Variable::class);
        // Requete sur la BD
        $variable = $repo->findOneBy(['idContrat' => $id]);
        $data = $serializer->serialize($variable,'json');

        return new JsonResponse($data, Response::HTTP_OK);
    }

    // Fonction qui génére le document final avec les résultats du formulaire
    /**
     * @Route("/form/{id}", name="post_form", methods={"POST"})
     */
    public function postFormById(loggerInterface $logger,DocumentManager $dm, Request $request,$id): Response
    {
        // Afficher les paramêtres
        $logger->info($request->getContent());
        $logger->warning($id);

        // Mettre en forme les résultats
        $resultat = explode(',', $request->getContent());
        $resultat = str_replace('"', '', $resultat);
        $resultat = str_replace(' ', '', $resultat);
        
        // Récupérer le repo de la BD
        $repoVariable = $dm->getRepository(Variable::class);
        // Requete sur la BD
        $jsonVarBD = $repoVariable->findOneBy(['idContrat' => $id]);

        // Mettre en forme les variables
        $jsonVar = explode(',', $jsonVarBD->getVar());
        $jsonVar = str_replace('"', '', $jsonVar);
        $jsonVar = str_replace(' ', '', $jsonVar);

        // Récupérer le repo de la BD
        $repoContrat = $dm->getRepository(Contrat::class);
        // Requete sur la BD
        $contratBD = $repoContrat->find($id);
        $contrat = $contratBD->getOps();

        //Parcours du contrat
        $parsed_json = json_decode($contrat);
        $tailleResultat = count($resultat);
        foreach ($parsed_json->ops as $v) 
        {
            //Parcours des résultats
            $indiceResultat = 0;
            while($indiceResultat < $tailleResultat)
            {
                $chaineARemplacer = '{{'.explode(':', $jsonVar[$indiceResultat])[0].'||'.explode(':', $jsonVar[$indiceResultat])[1].'}}';
                $nouvelleChaine = explode(':', $resultat[$indiceResultat]);

                // Remplacer les variables par les résultats
                $v->insert = str_replace($chaineARemplacer, $nouvelleChaine[1], $v->insert);
                $indiceResultat++;
            }
        }

        $contratUpdate = json_encode($parsed_json);
        
        // Convertir le JSON en HTML
        $lexer = new Lexer($contratUpdate);
        $html = $lexer->render();

        // Convertir le HTML en PDF
        $html2pdf = new Html2Pdf('P', 'A4', 'fr');
        $html2pdf->writeHTML($html);
        $filename = $html2pdf->output('document.pdf', 'S');
        
        return new Response($filename);
    }    
}