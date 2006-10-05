<?php
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'io.File',
    'img.Image',
    'img.io.PngStreamReader',
    'img.io.PngStreamWriter',
    'img.fonts.TrueTypeFont'
  );
  
  // {{{ main
  $p= &new ParamString();
  if (!$p->exists(1) || $p->exists('help', '?')) {
    Console::writeLine(<<<__
Creates "Web 2.0"-style reflecting text

Usage:
$ php reflecting-text.php "Text" [-f font] [-s size] [-c color] [-o output-file]

Parameters:
  * Font is a TrueType[TM] font file (default: c:\\windows\\fonts\\trebuc.ttf)
  * Size is the font size (default: 30)
  * Color is a hex color - as in HTML (default: #990000)
  * Output-file is the filename to output to (default: out.png)
__
    );
    exit(1);
  }
  
  $text= $p->value(1);
  $name= $p->value('font', 'f', 'c:\\windows\\fonts\\trebuc.ttf');
  $size= max($p->value('size', 's', 30), 30);
  $color= $p->value('color', 'c', '#990000');
  
  // First, calculate boundaries
  $font= &new TrueTypeFont($name, $size, 0);
  $boundaries= imagettfbbox($font->size, $font->angle, $font->name, $text);
  $padding= 10;
  $width= abs($boundaries[4] - $boundaries[0]);
  $height= abs($boundaries[5] - $boundaries[1]);
  
  // Create an image
  $img= &Image::create($width+ $padding * 2, $height * 2 + $padding * 2, IMG_TRUECOLOR);
  $img->fill($img->allocate(new Color('#ffffff')), 0, 0);
  
  // Draw text onto it
  $baseline= $padding + $font->size;
  $font->drawtext(
    $img->handle, 
    $img->allocate(new Color($color)), 
    $text, 
    $padding, 
    $baseline
  );

  // Flip text
  for ($i= 0; $i <= $font->size; $i++) {
    $img->copyFrom(
      $img, 
      $padding,                      // dst_x
      $baseline + $font->size - $i,  // dst_y
      $padding,                      // src_x
      $padding + $i,                 // src_y
      $width,                        // src_w
      1                              // src_h
    );
  }

  // Overlay fading
  $img->copyFrom(
    Image::loadFrom(new PngStreamReader(new File(dirname(__FILE__).DIRECTORY_SEPARATOR.'fade.png'))),
    $padding,
    $baseline,
    0,
    0,
    $width,
    30        // height of fade.png
  );

  // Save
  $img->saveTo(new PngStreamWriter(new File($p->value('out', 'o', 'out.png'))));
  // }}}
?>
  
