<?php
/* This file is part of the XP framework's examples
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'io.File',
    'img.Image', 
    'img.io.JpegStreamReader',
    'img.io.JpegStreamWriter'
  );
  
  // {{{ main
  $p= new ParamString();
  if (!$p->exists(1) || $p->exists('help', '?')) {
    Console::writeLinef(
      'Usage: %s %s <infile> [ -w <width> = 320 [ -h <height> = 240 ]] [ -o <outfile> ]',
      $p->value(-1),
      $p->value(0)
    );
    exit(1);
  }
  
  // Load original
  Console::write('===> Loading ', $p->value(1), ': ');
  try {
    $img= Image::loadFrom(new JpegStreamReader(new File($p->value(1))));
  } catch (ImagingException $e) {
    $e->printStackTrace();
    exit(-1);
  }
  Console::writeLine($img->toString());
  
  // Create thumbnail
  Console::write('---> Creating thumbnail: ');
  $thumb= Image::create(
    $p->value('width', NULL, 320), 
    $p->value('height', NULL, 240), 
    IMG_TRUECOLOR
  );
  $thumb->resampleFrom($img);
  Console::writeLine('>>> ', $thumb->toString());
  
  // Save thumbnail
  $out= new File($p->value('outfile', NULL, 'thumbof_'.basename($p->value(1))));
  try {
    $thumb->saveTo(new JpegStreamWriter($out));
  } catch (ImagingException $e) {
    $e->printStackTrace();
    exit(-1);
  }
  
  Console::writeLinef('===> Thumbnail %s created', $out->getURI());
  // }}}
?>
