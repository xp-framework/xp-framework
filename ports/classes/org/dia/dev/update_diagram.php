<?php
/* This script updates all classes found in a diagram
 *
 * $Id$
 */
 
  require('lang.base.php');
  
  uses(
    'util.cmd.ParamString',
    'org.dia.DiaUnmarshaller',
    'org.dia.UpdateVisitor'
  );

  $P= new ParamString();
  $diagram= $P->value(1);
  if (!file_exists($diagram)) {
    Console::writeLine("You need to specify an existing dia diagram file as first parameter!");
    exit(0);
  }

  // parse diagram
  try {
    $Dia= DiaUnmarshaller::unmarshal($diagram);
  } catch (Exception $e) {
    $e->printStackTrace();
    exit(-1);
  }

  // visitor that updates all existing classes in the diagram
  try {
    $V= new UpdateVisitor(array(), FALSE, TRUE);
  } catch (Exception $e) {
    $e->printStackTrace();
    exit(-1);
  }
  $Dia->accept($V);
  $V->finalize(); // only needed when adding classes...

  // save back to diagram file (uncompressed)
  $Dia->saveTo($diagram, FALSE);

  Console::writeLine("Successfully updated the diagram: '$diagram' :)");
?>
