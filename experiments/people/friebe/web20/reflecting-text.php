<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'io.File',
    'img.Image',
    'img.shapes.Line',
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
$ php reflecting-text.php "Text" [-f font] [-s size] [-c color] [-b bgcolor] [-o output-file]

Parameters:
  * Font is a TrueType[TM] font file (default: c:\\windows\\fonts\\trebuc.ttf)
  * Size is the font size (default: 30)
  * Color (font color) is a hex color - as in HTML (default: #990000)
  * BgColor (background color) is a hex color - as in HTML (default: #ffffff)
  * Output-file is the filename to output to (default: out.png)
__
    );
    exit(1);
  }
  
  $text= $p->value(1);
  $name= $p->value('font', 'f', 'c:\\windows\\fonts\\trebuc.ttf');
  $size= $p->value('size', 's', 30);
  $color= $p->value('color', 'c', '#990000');
  $bgcolor= $p->value('bg', 'b', '#ffffff');
  
  // First, calculate boundaries
  // Note: There should probably be a TrueTypeFont::calculateDimensions()
  // method or something - we'll need an RFC for this:)
  $font= &new TrueTypeFont($name, $size, 0);
  $boundaries= imagettfbbox($font->size, $font->angle, $font->name, $text);
  $padding= 10;
  $width= abs($boundaries[4] - $boundaries[0]);
  $height= abs($boundaries[5] - $boundaries[1]);
  
  // Create an image
  $img= &Image::create($width+ $padding * 2, $height * 2 + $padding * 2, IMG_TRUECOLOR);
  $bg= &new Color($bgcolor);
  $img->fill($img->allocate($bg), 0, 0);
  
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
  for (
    $i= 0, $transparent= 0, $step= 100 / $font->size; 
    $i <= $font->size; 
    $i++
  ) {
    $y= $baseline + $font->size - $i+ 1;
    $img->copyFrom(
      $img, 
      $padding,                      // dst_x
      $y,                            // dst_y
      $padding,                      // src_x
      $padding + $i,                 // src_y
      $width,                        // src_w
      1                              // src_h
    );
    
    // Overlay fading
    $img->draw(new Line($img->allocate($bg, floor($transparent)), $padding, $y, $padding+ $width, $y));
    $transparent= min($transparent+ $step, 127);
  }

  // Save
  $out= &new File($p->value('out', 'o', 'out.png'));
  $img->saveTo(new PngStreamWriter($out));
  Console::writeLine('Results written to ', $out->getURI());
  // }}}
?>
