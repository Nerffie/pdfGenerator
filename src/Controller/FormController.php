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
use Spipu\Html2Pdf\Html2Pdf;
use nadar\quill\Lexer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
    public function postFormById(loggerInterface $logger,DocumentManager $dm, Request $request,$id): Response
    {
        $logger->info($request->getContent());
        $logger->warning($id);
        $resultat = explode(',', $request->getContent());
        $resultat = str_replace('"', '', $resultat);
        $resultat = str_replace(' ', '', $resultat);
        
        //Recuperer les variables de la BDD
        $repoVariable = $dm->getRepository(Variable::class);
        $jsonVarBD = $repoVariable->findOneBy(['idContrat' => $id]);
        $jsonVar = explode(',', $jsonVarBD->getVar());
        $jsonVar = str_replace('"', '', $jsonVar);
        $jsonVar = str_replace(' ', '', $jsonVar);

        //Recuperer le contrat de la BDD
        $repoContrat = $dm->getRepository(Contrat::class);
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
                $v->insert = str_replace($chaineARemplacer, $nouvelleChaine[1], $v->insert);
                $indiceResultat++;
            }
        }

        $contratUpdate = json_encode($parsed_json);
        
        $lexer = new Lexer($contratUpdate);
        $html = $lexer->render();

        $html2pdf = new Html2Pdf('P', 'A4', 'fr');
        $html2pdf->writeHTML($html);
        $filename = $html2pdf->output('document.pdf', 'S');
        
        return new Response($filename);
    }    
}