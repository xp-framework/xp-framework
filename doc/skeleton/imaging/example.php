<?php
  require('lang.base.php');
  uses(
    'img.PngImage', 
    'img.Color', 
    'img.ImgStyle',
    'img.ImgBrush',
    'img.shapes.Arc3D',
    'img.shapes.Polygon',
    'img.shapes.Line',
    'img.shapes.Rectangle',
    'img.shapes.Text',
    'img.fonts.TrueTypeFont',
    'io.File'
  );
  
  $i= &new PngImage(400, 400);
  $i->create();
  
  // Allocate some colors
  $colors= array(
    'white'   => $i->allocate(new Color('#ffffff')),
    'red'     => $i->allocate(new Color('#ff0000')),
    'darkred' => $i->allocate(new Color('#990000')),
    'blue'    => $i->allocate(new Color('#0000ff')),
    'darkblue'=> $i->allocate(new Color('#000099')),
  );
  $i->fill($colors['white']);
  
  // Merge in other image w/ 30% "transparency"
  $b= &new Image();
  try(); {
    $b->fromPng('box_start_people_neu.png');
  } if (catch('ImagingException', $e)) {
    $e->printStackTrace();
    exit;
  }
  $i->mergeFrom($b, 30);
  
  // Draw some shapes
  $i->draw(new Arc3D(array($colors['red'], $colors['darkred']), 200, 100, 200, 100, 0, 320));
  $i->draw(new Arc3D(array($colors['blue'], $colors['darkblue']), 224, 89, 200, 100, 320, 360));
  $i->draw(new Polygon($colors['blue'], array(
    40,    // x1
    50,    // y1
    20,    // x2
    240,   // y2
    60,    // x3
    60,    // y3
    240,   // x4
    20,    // y4
    50,    // x5
    40,    // y5
    10,    // x6
    10,    // y6    
  )));
  $i->draw(new Line($colors['red'], 50, 300, 350, 300));
  $i->draw(new Rectangle($colors['darkred'], 320, 140, 380, 200, TRUE));
  $i->draw(new Text(
    $colors['darkblue'],
    new TrueTypeFont('/usr/X11R6/lib/X11/fonts/truetype/verdana.ttf'),
    'Image created by '.$i->getName(),
    50,
    350
  ));
  
  // Create a style (two red pixels, to white)
  $style= &$i->setStyle(new ImgStyle(array(
    $colors['red'], 
    $colors['red'], 
    $colors['white'], 
    $colors['white']
  )));
  $i->draw(new Line($style, 50, 302, 350, 302));

  // Create a new brush
  $l= &new Image();
  try(); {
    $l->fromGif('icn_plink.gif');
  } if (catch('ImagingException', $e)) {
    $e->printStackTrace();
    exit;
  }
  $brush= &$i->setBrush(new ImgBrush($l, new ImgStyle(array(
    $colors['white'], 
    $colors['white'], 
    $colors['white'], 
    $colors['white'], 
    $colors['white'], 
    $colors['white'], 
    $colors['white'],
    $colors['white'],
    $colors['white'],
    $colors['white'],
    $colors['white'],
    $colors['white'],
    $colors['white'],
    $colors['darkblue'],
  ))));
  $i->draw(new Line($brush, 50, 320, 350, 320));
  
  // Correct gamma
  $i->correctGamma(1.0, 0.8);
  
  try(); {
    $f= &$i->toFile(new File('out.png'));
  } if (catch('ImagingException', $e)) {
    $e->printStackTrace();
    exit;
  }
  
  // Show picture
  `ElectricEyes $f->uri`;
  $f->unlink();
?>
