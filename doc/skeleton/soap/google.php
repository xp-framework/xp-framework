<?php
/* Benutzen der Google-SOAP-API
 * 
 * @see http://www.google.de/apis/
 * @see http://www.heise.de/ix/artikel/2002/07/118/04.shtml
 * @see http://www.heise.de/ix/artikel/2002/07/118/
 *
 * $Id$
 */
 
  require('lang.base.php');
  uses('xml.soap.SOAPClient', 'util.cmd.ParamString');
  
  define('GOOGLE_KEY',  'JM8DLMHOghWOwv4CB4y3Qejom6V8o6HE');
  
  $p= new ParamString($_SERVER['argv']);
  if (!$p->exists('search')) {
    printf(
      "Usage: %s (-s <word>|--search=<word>) [(-m <max>|--max=<max>)\n".
      "       <word>     Search word\n".
      "       <max>      Max results to return\n",
      basename($_SERVER['argv'][0])
    );
    exit;
  }
  
  $s= new SOAPClient(array(
    'url'       => 'http://api.google.com/search/beta2',
    'action'    => 'urn:GoogleSearch',
    'method'    => 'doGoogleSearch'
  ));
  
  try(); {
    $return= $s->call(
      new SOAPNamedItem('key', GOOGLE_KEY),
      new SOAPNamedItem('q', $p->value('search')),
      new SOAPNamedItem('start', 0, 'int'),
      new SOAPNamedItem('maxresults', $p->exists('max') ? $p->value('max') : 5),
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
