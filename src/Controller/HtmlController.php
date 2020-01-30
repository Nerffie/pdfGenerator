<?php
// src/Controller/Html.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use nadar\quill\Lexer;

class HtmlController
{
    public function generateHtml()
    {
        $json = file_get_contents('C:\Users\edith\Documents\projet\pdfgenerator\src\Controller\quill.json');
        $lexer = new Lexer($json);

        // echoing the html for the given json ops.
        $html = $lexer->render();
        
        return new Response($html);
    }
}