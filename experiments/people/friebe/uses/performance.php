<?php
  require($argv[1].'/lang.base.php');
  $s= microtime(TRUE);
  uses(
    'util.Hashmap',
    'rdbms.DriverManager',
    'rdbms.DataSet',
    'remote.Remote',
    'util.Date',
    'util.collections.HashTable',
    'io.collections.FileCollection',
    'scriptlet.xml.workflow.AbstractXMLScriptlet',
    'img.Image',
    'img.io.JpegStreamWriter',
    'img.io.JpegStreamReader',
    'io.collections.iterate.FilteredIOCollectionIterator'
  );
  
  printf(
    "%s: %d classes, %.3f seconds\n",
    $argv[1], 
    sizeof(get_declared_classes()),
    microtime(TRUE)- $s
  );
?>
