<?php
  require('lang.base.php');
  uses(
    'util.Properties', 
    'util.cmd.ParamString',
    'org.fpdf.FPDF', 
    'io.File'
  );

  // Datei auslesen
  $p= new ParamString($_SERVER['argv']);
  if (!$p->exists('file')) {
    exit(sprintf(
      "Usage: php -q %s --file=<<filename>>\n", 
      basename($_SERVER['argv'][0])
    ));
  }
  $in= new File($p->value('file'));
  try(); {
    $in->open(FILE_MODE_READ);
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit;
  }
  
  $pdf= new FPDF();
  try(); {
    $pdf->loadFonts(new Properties(SKELETON_PATH.'/org/fpdf/core_fonts.ini'));
    $pdf->open();
    $pdf->setTitle($in->uri);
    $pdf->addPage();

    // Überschrift
    $pdf->setFont($pdf->getFontByName('Times', 'B'));
    $pdf->setFillColor(192, 192, 192);
    $pdf->Cell(0, 6, $in->uri, 1, 1, 'L', 1);
    $pdf->Ln(4);

    $pdf->setFont($pdf->getFontByName('Helvetica'));
    // Datei zeilenweise durchgehen und als Block hinzufügen
    while (!$in->eof()) {
      $line= $in->gets();
      
      // Alles zwischen <pre> und </pre> in Courier markieren
      if ('<pre>' == substr($line, 0, 5)) {
        $pdf->setFont($pdf->getFontByName('Courier'));
        continue;
      }
      if ('</pre>' == substr($line, 0, 6)) {
        $pdf->setFont($pdf->getFontByName('Helvetica'));
        continue;
      }

      $pdf->MultiCell(0, 5, $line);
    }
    $pdf->Ln();
    
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit;
  }
  
  //var_dump($pdf);
  $out= new File('test.pdf');
  try(); {
    $out->open(FILE_MODE_WRITE);
    $out->write($pdf->getBuffer());
    $out->close();
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit;
  }
  
  printf("===> %d bytes written to test.pdf\n", $out->size());
?>
