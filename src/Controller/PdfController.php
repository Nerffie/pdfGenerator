<?php
// src/Controller/Pdf.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Spipu\Html2Pdf\Html2Pdf;

class PdfController
{
    public function generate()
    {
        $html2pdf = new Html2Pdf('P', 'A4', 'fr');

        /* A modifier selon le chemin */
        /* !! chemins des images !! */
        $content = file_get_contents('C:\Users\edith\Documents\projet\pdfgenerator\src\Controller\contenuDraft.html');
        //$content = file_get_contents('C:\Users\edith\Documents\projet\pdfgenerator\src\Controller\contenuQuill.html');
        
        $html2pdf->writeHTML($content);
        $html2pdf->output('example.pdf');
    }
}