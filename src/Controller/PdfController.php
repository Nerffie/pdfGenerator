<?php
// src/Controller/Pdf.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Spipu\Html2Pdf\Html2Pdf;
use nadar\quill\Lexer;
use App\Document\Contrat;
use App\Document\Variable;
use Doctrine\ODM\MongoDB\DocumentManager as DocumentManager;

class PdfController
{
    public function generatePdf(DocumentManager $dm)
    {
        /* RECUPERER JsonVar DU FRONT*/
        $repoVariable = $dm->getRepository(Variable::class);
        $jsonVarBD = $repoVariable->findOneBy(['idContrat' => '5e418532576000009b0052f4']);
        $jsonVar = $jsonVarBD->getVar();

        $repoContrat = $dm->getRepository(Contrat::class);
        $contratBD = $repoContrat->find($jsonVarBD->GetIdContrat());
        $contrat = $contratBD->getOps();
        $parsed_json = json_decode($contrat);

        // Parcours du contrat remplacé les variables*/

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

                        if($posFin != NULL)
                        {
                            // Recherche de la variable dans $jsonVar
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
        
        // $html2pdf = new Html2Pdf('P', 'A4', 'fr');

        /* A modifier selon le chemin */
        /* !! chemins des images !! */
        //$content = file_get_contents('C:\Users\edith\Documents\projet\pdfgenerator\src\Controller\ContenuDraft.html');
        //$content = file_get_contents('C:\Users\edith\Documents\projet\pdfgenerator\src\Controller\ContenuQuill.html');
        
        //$html2pdf->writeHTML($content);
        //$html2pdf->output('Exemple.pdf');

        return new Response($contrat);
        
    }
}