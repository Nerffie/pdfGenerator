<?php
// src/Controller/Pdf.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Spipu\Html2Pdf\Html2Pdf;
use nadar\quill\Lexer;

class PdfController
{
    public function generatePdf()
    {
        $html2pdf = new Html2Pdf('P', 'A4', 'fr');

        /* A modifier selon le chemin */
        /* !! chemins des images !! */
        $content = file_get_contents('C:\Users\edith\Documents\projet\pdfgenerator\src\Controller\ContenuDraft.html');
        //$content = file_get_contents('C:\Users\edith\Documents\projet\pdfgenerator\src\Controller\ContenuQuill.html');
        
        $html2pdf->writeHTML($content);
        $html2pdf->output('Exemple.pdf');
        
    }
}