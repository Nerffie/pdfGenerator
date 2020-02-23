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
        $jsonVarBD = $repoVariable->findOneBy(['idContrat' => '5e4e9cc13b130000a60040e7']);
        $jsonVar = $jsonVarBD->getVar();

        $repoContrat = $dm->getRepository(Contrat::class);
        $contratBD = $repoContrat->find('5e4e9cc13b130000a60040e7');
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
                            $type = substr($chaine, $posMil+2, $posFin-$taille);
                            $jsonVar .= '"'.$variable . '" : "' . $type . '", ';
                            $indice = $posFin + 3;
                            // Recherche de la variable dans $jsonVar
                            $i = 0;
                            while($i < strlen($jsonVar))
                            {
                                if($jsonVar[i] == $variable)
                                {
                                   // $chaine = str_replace('{{'.$variable.'||'..'}}', $type, )
                                    $i = strlen($jsonVar);
                                }
                                $i++;
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
                else
                {
                    $indice = $taille;
                }
            }
        }

        $contratUpdate = json_encode($parsed_json);
        
        $lexer = new Lexer($contratUpdate);
        $html = $lexer->render();

        $html2pdf = new Html2Pdf('P', 'A4', 'fr');
        $html2pdf->writeHTML($html);
        $html2pdf->output('Exemple.pdf');

        return new Response($html);       
    }
}