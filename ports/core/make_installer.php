<?php
/* This file is part of the XP framework
 *
 * $Id$ 
 */
  require('lang.base.php');
  uses(
    'io.File',
    'io.Folder',
    'util.cmd.ParamString'
  );

  // {{{ main
  $p= &new ParamString();
  $base= dirname($p->value(0)).'/build/';
  printf("===> Init <installer> in %s\n", $base);
  
  // Open CCA
  $d= &new Folder($base);
  while ($e= $d->getEntry()) {
    if ('.cca' != substr($e, -4)) continue;
    
    printf("---> Processing %s\n", $e);
    $file= &new File($d->uri.$e);
    try(); {
      $file->open(FILE_MODE_READ);
    } if (catch('Exception', $e)) {
      printf("*** Error: Cannot open CCA (%s)\n", $e->getStackTrace());
      continue;
    }

    // Create SFX
    $sfx= &new File($d->uri.'install_'.substr($e, 0, -4).'.php');
    try(); {
      $sfx->open(FILE_MODE_WRITE);
      $sfx->write('<?php
  if (!isset($_SERVER["argv"][1])) {
    exit(sprintf("Usage: %s <install_dir>\n", $_SERVER["argv"][0]));
  }
  if (!@is_dir($prefix= realpath($_SERVER["argv"][1]))) {
    exit(sprintf("*** Error: %s is not a directory\n", $prefix));
  }
  printf("---> Installing XP core to %s\n", $prefix);
  if (!$fd= @fopen($_SERVER["argv"][0], "r")) {
    exit(sprintf("*** Error: Cannot open %s for reading\n", $_SERVER["argv"][0]));
  }
  $p= 1118;
  fseek($fd, $p, SEEK_SET);
  $data= unpack("a3id/c1version/i1indexsize/a*reserved", fread($fd, 0x100));
  $entries= array();
  for ($i= 0; $i < $data["indexsize"]; $i++) {
    $entry= unpack(
      "a80id/a80filename/a80path/i1size/i1offset/a*reserved",
      fread($fd, 0x100)
    );
    $entries[$entry["id"]]= $entry;
  }
  $p+= 0x100 + $data["indexsize"] * 0x100;
  foreach ($entries as $id => $entry) {
    printf("---> %s\n", $entry["filename"]);
    $fo= fopen($prefix."/".$entry["filename"], "w");
    fseek($fd, $p + $entry["offset"], SEEK_SET);
    fputs($fo, fread($fd, $entry["size"]));
    fclose($fo);
  }
  fclose($fd);
  printf("===> Done\n");
/*
__DATA__
');
      $sfx->write($file->read($file->size()));
      $sfx->write('*/ ?>');

      $sfx->close();
    } if (catch('Exception', $e)) {
      printf("*** Error: SFX creation failed (%s)\n", $e->getStackTrace());
      @$file->unlink();
      $sfx->unlink();
      continue;
    }
  }
  printf("===> Finished <installer>\n");
  // }}}
?>
