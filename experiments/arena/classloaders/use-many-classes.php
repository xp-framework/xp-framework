<?php
  require('lang.base.php');
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
    "- %d classes, %.3f seconds\n",
    sizeof(get_declared_classes()),
    microtime(TRUE)- $s
  );

  echo '! ';
  var_dump(xp::$registry['errors']);
?>
