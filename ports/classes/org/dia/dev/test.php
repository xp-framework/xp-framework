<?php
/* TEST script: 
 * - unmarshal diagram
 * 
 *
 */


  require('lang.base.php');
  uses(
    'util.cmd.Console', 
    'io.File',
    'io.ZipFile',
    'xml.dom.Document',
    'org.dia.DiaDiagram',
    'org.dia.DiaText',
    'org.dia.DiaMarshaller',
    'org.dia.DiaUnmarshaller'
  );

  // create new empty diagram:
  $Dia= new DiaDiagram();
  $BgLayer= $Dia->getChild('Background');
  $BgLayer->addObject(new DiaText());

  Console::writeLine($Dia->getSource());
  exit(0);

  
  //$Dia= &DiaMarshaller::marshal(array('object.TdocVertrag'), $recurse= 1, $depend= FALSE);
  //print($Dia->getSource());

  // read an existing diagram
  $file= getcwd().'/test-read.dia';
  Console::writeLine("Using file: $file");
  DiaUnmarshaller::unmarshal($file, array('org.dia.DiaDiagram', 'lang.Object'));


  // try opening (zipped) file
  // hint: ZipFile also handles unzipped files...
/*  try (); {
    $dia_file= &new ZipFile($file) &&
    $dia_file->open(FILE_MODE_READ);
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }

  // get the encoding from file
  //$xml_encoding= $dia_file->readLine();
  $dia_xml= $dia_file->read($dia_file->size());
  
  // parse the dia file
  try (); {
    $Doc= &Document::fromString($dia_xml);
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }


  //Console::writeLine($Doc->getDeclaration()."\n".$Doc->getSource());
*/

?>
