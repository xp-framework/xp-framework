<?php
  require('lang.base.php');
  uses('xml.soap.SOAPClient', 'util.cmd.ParamString');
  
  define('GOOGLE_KEY',  'JM8DLMHOghWOwv4CB4y3Qejom6V8o6HE');
  
  $p= new ParamString($_SERVER['argv']);
  
  $s= new SOAPClient(array(
    'url'       => 'http://api.google.com/search/beta2',
    'action'    => 'urn:GoogleSearch',
    'method'    => 'doGoogleSearch'
  ));
  
  try(); {
    $return= $s->call(
      new SOAPNamedItem('key', GOOGLE_KEY),
      new SOAPNamedItem('q', '1&1 Webhosting'),
      new SOAPNamedItem('start', 0, 'int'),
      new SOAPNamedItem('maxresults', 5),
      new SOAPNamedItem('filter', FALSE),
      new SOAPNamedItem('restrict', ''),
      new SOAPNamedItem('safeSearch', FALSE),
      new SOAPNamedItem('lr', ''),
      new SOAPNamedItem('ie', ''),
      new SOAPNamedItem('oe', '')
    );
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
  }
  
  var_dump($return);
  
  if ($p->exists('xml')) echo (
    "\nSEND ===>\n".
    $s->call->getSource(0).
    "\nRECV <=== \n".
    (isset($s->answer) ? $s->answer->getSource(0) : 'n/a')
  );
?>
