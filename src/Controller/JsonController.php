<?php
// src/Controller/Json.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class JsonController
{
    public function generateJsonVar()
    {
        $json = file_get_contents("php://input");
        //$json = file_get_contents('C:\Users\edith\Documents\projet\pdfgenerator\src\Controller\quill.json');

        $parsed_json = json_decode($json);
        echo $json;

        $jsonVar = '{';

        $jsonVar .= '"id" : "1",';
        $jsonVar .= '"vars" : [ ';

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

        $jsonVar .= ']}';

        $options = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => "Content-Type: application/json",
                'ignore_errors' => true,
                'timeout' =>  10,
                'content' => json_encode($jsonVar),
            ),
        );

        $context  = stream_context_create($options);
        file_get_contents('http://localhost:3000', false, $context);
        
        return new Response($jsonVar);
        
    }
}