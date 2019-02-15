<?php
    include 'lumpdf.php';

    $pdf = new lumpdf();
    $pdf->setPageSize(500,700);
    $pdf->addContent("Text 1", 50, 650);
    $pdf->addContent("Text 2", 300, 600);
    $pdf->addContent("Text 3", 200, 500);
    $pdf->addContent("Text 4", 100, 300);
    $pdf->generate();    
?>