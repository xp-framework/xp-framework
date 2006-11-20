<?php
/* Migriert die Font-Settings in ein Property-File
 *
 * Beispiele:
 * 1) Die Core-Fonts migrieren 
 *   php -q migrate_fonts.php
 *   ===> Start for ./src/font [allfiles .*]
 *   ---> Saving fontdef for courier.php to core_fonts.ini
 *   ---> Saving fontdef for helvetica.php to core_fonts.ini
 *   ---> Saving fontdef for helveticab.php to core_fonts.ini
 *   ---> Saving fontdef for helveticabi.php to core_fonts.ini
 *   ---> Saving fontdef for helveticai.php to core_fonts.ini
 *   ---> Saving fontdef for symbol.php to core_fonts.ini
 *   ---> Saving fontdef for times.php to core_fonts.ini
 *   ---> Saving fontdef for timesb.php to core_fonts.ini
 *   ---> Saving fontdef for timesbi.php to core_fonts.ini
 *   ---> Saving fontdef for timesi.php to core_fonts.ini
 *   ---> Saving fontdef for zapfdingbats.php to core_fonts.ini
 *
 * 2) Eine spezielle Font migrieren
 *   php -q migrate_fonts.php --search=src/tutorial/calligra.php --conf=calligra.ini
 *   ===> Start for src/tutorial [allfiles calligra.php]
 *   ---> Saving fontdef for calligra.php to calligra.ini
 *
 * Das Property-File wird angelegt, falls es nicht existiert
 *
 * $Id$
 */
 
  require('lang.base.php');
  uses('io.Folder', 'util.Properties', 'util.cmd.ParamString');

  function writeFontDef($fromFile, &$p) {
    global $core_names;

    include($fromFile);
    $key= isset($fpdf_charwidths) ? key($fpdf_charwidths) : substr(basename($fromFile), 0, -4);
    
    // Family, Style getrennt durch .
    if (preg_match('/([a-z]+)([BI])+$/', $key, $regs)) {
      list(, $family, $style)= $regs;
    }
    $p->writeString($key, 'family', isset($family) ? $family : $key);
    $p->writeString($key, 'style', isset($style) ? $style : '');
    
    // Font-Name ["Calligrapher-Regular"]
    $p->writeString($key, 'name', isset($name) ? $name : $core_names[$key]);
    
    // Font-Charwidths [array(800, 800, 508, 200, ...)"]
    $p->writeArray($key, 'cw', array_values(isset($cw) ? $cw : $fpdf_charwidths[$key]));
    
    // Font-Typ
    $p->writeString($key, 'type', @$type);
    
    // Encoding
    $p->writeString($key, 'enc', @$enc);
    
    // Diff
    $p->writeString($key, 'diff', @$diff);
    
    // Filename
    $p->writeString($key, 'file', @$file);
    
    // ???
    $p->writeInteger($key, 'originalsize', @$originalsize);
    
    // ???
    $p->writeInteger($key, 'up', @$up);
    
    // ???
    $p->writeInteger($key, 'ut', @$ut);
    
    // Desc??? array('Ascent'=>899,'Descent'=>-234,'CapHeight'=>731,'Flags'=>32,'FontBBox'=>'[-50 -234 1328 899]','ItalicAngle'=>0,'StemV'=>70,'MissingWidth'=>800);
    if (isset($desc)) foreach ($desc as $k => $v) {
      if (is_int($v)) {
        $p->writeInteger($key.'.desc', $k, $v);
      } else {
        $p->writeString($key.'.desc', $k, $v);
      }
    }
  }

  $core_names= array(    
    'courier'          => 'Courier',
    'courierB'         => 'Courier-Bold',
    'courierI'         => 'Courier-Oblique',
    'courierBI'        => 'Courier-BoldOblique',
    'helvetica'        => 'Helvetica',
    'helveticaB'       => 'Helvetica-Bold',
    'helveticaI'       => 'Helvetica-Oblique',
    'helveticaBI'      => 'Helvetica-BoldOblique',
    'times'            => 'Times-Roman',
    'timesB'           => 'Times-Bold',
    'timesI'           => 'Times-Italic',
    'timesBI'          => 'Times-BoldItalic',
    'symbol'           => 'Symbol',
    'zapfdingbats'     => 'ZapfDingbats'
  );

  // Kommandozeilen-Parameter
  $param= new ParamString($_SERVER['argv']);
  $search= $param->exists('search') ? $param->value('search') : './src/font/.*';
  $propertyFile= $param->exists('conf') ? $param->value('conf') : 'core_fonts.ini';
  $pattern= basename($search);
  $base= dirname($search);
  
  printf("===> Start for %s [allfiles %s]\n", $base, $pattern);
  
  $prop= new Properties($propertyFile);
  if (!$prop->exists()) { try(); {
    printf("---> Creating configfile %s\n", $propertyFile);
    $prop->create();
  } if (catch('IOException', $e)) {
    $e->printStackTrace();
    exit;
  }}
  
  // Verzeichnis durchsuchen
  $d= new Folder($base);
  while ($e= $d->getEntry()) {
    if (is_dir($d->uri.'/'.$e) || !preg_match('/'.$pattern.'/', $e)) continue;
    
    printf("---> Saving fontdef for %s to %s\n", $e, $propertyFile);
    writeFontDef($d->uri.'/'.$e, $prop);
  }
  $d->close();
  
  // Property-File schreiben
  try(); {
    $prop->save();
  } if (catch('IOException', $e)) {
    $e->printStackTrace();
  }
  delete($prop);
?>
