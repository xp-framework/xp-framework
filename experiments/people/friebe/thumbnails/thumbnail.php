<?php
/* This file is part of the XP framework's people experiments
 *
 * $Id$ 
 */
  require('lang.base.php');
  uses(
    'img.JpegImage',
    'io.File',
    'util.cmd.ParamString'
  );
  
  // {{{ main
  $p= &new ParamString();
  if (!$p->exists(1)) {
    printf("Usage: %s infile.jpg > thumbnail.jpg\n", $p->value(0));
    exit(-1);
  }
  
  try(); {
    $image= &new JpegImage();
    $image->fromFile($p->value(1));
    
    $thumb= &new JpegImage(33, 42);
    $thumb->create(TRUE);
    $thumb->copyFrom(
      $image, 
      0, 0, 
      0, 0, 
      $image->getWidth(), $image->getHeight(),
      $thumb->getWidth(), $thumb->getHeight()
    );
    
    $data= $thumb->toString();
  } if (catch('Exception', $e)) {
    $e->printStackTrace(STDERR);
    exit(-2);
  }
  
  echo $data;
  // }}}
?>
