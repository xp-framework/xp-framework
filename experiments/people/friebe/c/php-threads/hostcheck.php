<?php
  function check($spec) {
    sscanf($spec, '%[^:]:%d', $host, $port);
    $fd= @fsockopen($host, $port, $errno, $errstr);
    if ($fd) fclose($fd);
    return array($spec => $errno ? $errno.': '.$errstr : TRUE);
  }
 
  $t= thread_new('#1');
  var_dump($t);
  $r= thread_new('#2');
  var_dump($r);
  $x= thread_new('#3');
  var_dump($x);

  thread_start($t, 'check', array('php3.de:80'));
  thread_start($r, 'check', array('php3.de:25'));
  thread_start($x, 'check', array('localhost:6448'));
  
  var_dump(thread_join($t));
  var_dump(thread_join($r));
  var_dump(thread_join($x));
?>
