<?php
/* This script creates or updates diagrams
 *
 *
 */
  require('lang.base.php');
  uses(
    'util.cmd.Console', 
    'util.cmd.ParamString',
    'org.dia.DiaMarshaller',
    'org.dia.DiaUnmarshaller',
    'org.dia.UpdateVisitor'
  );

  $P= new ParamString();
  if (!$P->exists('classes') or !$P->exists('diagram')) {
    die('Parameters "--classes=ful.qal.class[,oth.qual.class,...]" and "--diagram=file.dia" are required!');
  } else {
    if ($P->exists('recurse')) $recurse= $P->value('recurse');
    if ($P->exists('depend')) $depend= $P->value('depend');
    $classes= explode(',', $P->value('classes'));
    $file= $P->value('diagram');

    // if file does not exist: generate diagram
    if (!file_exists($file)) {
      Console::writeLine('File not found! Generating new diagram...');
      if (!isset($recurse)) {
        $recurse= 2;
        Console::writeLine('Using default "recurse=2"...');
      }
      if (!isset($depend)) {
        $depend= TRUE;
        Console::writeLine('Using default "depend=TRUE"...');
      }

      $Dia= DiaMarshaller::marshal($classes, $recurse, $depend);
      $Dia->saveTo($file, FALSE);
    } else { // else: update diagram
      Console::writeLine('Diagram file found: updating classes...');
      // initialize objects
      try {
        // Visitor checks if the given classes exist
        $Visitor= new UpdateVisitor($classes, TRUE, TRUE);
      } catch (Exception $e) {
        $e->printStackTrace();
        exit(-1);
      }

      Console::writeLine('Parsing XML diagram file...');
      try {
        $Dia= DiaUnmarshaller::unmarshal($file);
      } catch (Exception $e) {
        $e->printStackTrace();
        exit(-1);
      }

      // do the updates
      Console::writeLine('Running visitor...');
      $Dia->accept($Visitor);
      Console::writeLine('Finalize visitor...');
      $Visitor->finalize();

      // write changes back to file if the visitor has changed something
      if ($Visitor->changedClasses()) {
        Console::writeLine('Writing changes back to diagram file...');
        $Dia->saveTo($file, FALSE);
      }
    }
  }

?>
