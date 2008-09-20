<?php 
  require('lang.base.php'); 
  uses('util.Properties');

  $cl= ClassLoader::registerLoader(new ArchiveClassLoader($argv[1]));
  $pr= Properties::fromString($cl->getResource('META-INF/manifest.ini'));
  exit(XPClass::forName($pr->readString('archive', 'main-class'), $cl)->getMethod('main')->invoke(NULL, array(array_slice($argv, 2)))); 
?>
