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
  
  // Parameter
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
  $max= $p->exists('max') ? $p->value('max') : 5;
  
  $s= new SOAPClient(array(
    'url'       => 'http://api.google.com/search/beta2',
    'action'    => 'urn:GoogleSearch',
    'method'    => 'doGoogleSearch'
  ));
  try(); {
    $return= $s->call(
      new SOAPNamedItem('key', GOOGLE_KEY),             // Google-KEY
      new SOAPNamedItem('q', $p->value('search')),      // Suchbegriff
      new SOAPNamedItem('start', 0, 'int'),             // Start-Index
      new SOAPNamedItem('maxresults', $max),            // Maximale Anzahl Ergebnisse
      new SOAPNamedItem('filter', FALSE),               // Automatischer Filter, der ähnliche Seiten versteckt
      new SOAPNamedItem('restrict', ''),                // Begrenzung auf eine Google-Kategorie
      new SOAPNamedItem('safeSearch', FALSE),           // Filtert "Adult Content"
      new SOAPNamedItem('lr', ''),                      // "Language Restrict"
      new SOAPNamedItem('ie', ''),                      // "Input Encoding"
      new SOAPNamedItem('oe', '')                       // "Output Encoding"
    );
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
  }
  
  var_export($return);
  
  if ($p->exists('xml')) echo (
    "\nSEND ===>\n".
    $s->call->getSource(0).
    "\nRECV <=== \n".
    (isset($s->answer) ? $s->answer->getSource(0) : 'n/a')
  );
?>
