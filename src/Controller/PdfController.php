<?php
// src/Controller/Pdf.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Spipu\Html2Pdf\Html2Pdf;
use nadar\quill\Lexer;
use App\Document\Contrat;
use App\Document\Variable;
use Doctrine\ODM\MongoDB\DocumentManager as DocumentManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


class PdfController
{
    public function generatePdf(loggerInterface $logger,DocumentManager $dm)
    {
        /* RECUPERER JsonVar DU FRONT*/
        $repoVariable = $dm->getRepository(Variable::class);
        $jsonVarBD = $repoVariable->findOneBy(['idContrat' => '5e523c96cc16000022000a82']);
        $jsonVar = explode(',', $jsonVarBD->getVar());
        $jsonVar = str_replace('"', '', $jsonVar);
        $jsonVar = str_replace(' ', '', $jsonVar);

        $resultat = explode(',', $jsonVarBD->getVar());
        $resultat = str_replace('"', '', $resultat);
        $resultat = str_replace(' ', '', $resultat);

        $repoContrat = $dm->getRepository(Contrat::class);
        $contratBD = $repoContrat->find('5e523c96cc16000022000a82');
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
        //$html2pdf->output('Exemple.pdf');
        $filename = __DIR__.'/document'.$contratBD->getId().'.pdf';
        $html2pdf->Output($filename, 'F');

        //return new Response(file_get_contents(__DIR__.'/document'.$contratBD->getId().'.pdf'));
        return new BinaryFileResponse($filename);       
    }
}