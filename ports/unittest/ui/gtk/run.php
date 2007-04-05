<?php
  require('lang.base.php');
  xp::sapi('gui.gtk');
  uses('net.xp_framework.unittest.runner.gtk.UnitTestUI');
  
  try(); {
    $app= &new UnitTestUI(new ParamString());
    $app->init();
  } if (catch('GuiException', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  $app->run();
  $app->done();
?>
