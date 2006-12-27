<?php
  require('lang.base.php');
  uses(
    'util.cmd.Console',
    'util.cmd.ParamString',
    'org.dia.DiaUnmarshaller',
    'io.File'
  );

  // TODO: => unittest!

  $Param= new ParamString();
  $diagram= $Param->value(1);
  
  try {
    $Dia= DiaUnmarshaller::unmarshal($diagram);
  } catch (Exception $e) {
    $e->printStackTrace();
    exit(-1);
  }

  $Dia->saveTo('test_out.dia', FALSE);
?>
