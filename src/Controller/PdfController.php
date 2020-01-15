<?php
// src/Controller/Pdf.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Spipu\Html2Pdf\Html2Pdf;

class PdfController
{
    public function generate()
    {
        //var_dump(extension_loaded('curl'));
        $html2pdf = new Html2Pdf('P', 'A4', 'fr');

        /* A modifier selon le chemin */
        /* !! chemins des images !! */
        //$content = file_get_contents('D:\Users\moham\Documents\Projet Isima\pdfgenerator\src\Controller\ContenuDraft.html');
        $content = file_get_contents('D:\Users\moham\Documents\Projet Isima\pdfgenerator\src\Controller\ContenuQuill.html');
        
        $html2pdf->writeHTML($content);
        $html2pdf->output('resultatQuill.pdf');
    }
}