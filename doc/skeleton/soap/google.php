<?php
/* Benutzen der Google-SOAP-API
 * 
 * @see http://www.google.de/apis/
 * @see http://www.google.de/apis/api_faq.html
 * @see http://www.heise.de/ix/artikel/2002/07/118/04.shtml
 * @see http://www.heise.de/ix/artikel/2002/07/118/
 *
 * $Id$
 */
 
  require('lang.base.php');
  uses(
    'xml.soap.transport.SOAPHTTPTransport',
    'xml.soap.SOAPClient', 
    'util.cmd.ParamString'
  );
  
  define('GOOGLE_KEY',  'JM8DLMHOghWOwv4CB4y3Qejom6V8o6HE');
  
  // Parameter
  $p= new ParamString($_SERVER['argv']);
  if (!$p->exists('query')) {
    printf(
      "Usage: %s --query=<word> [--max=<max> [--start=<start>] [--xml]]]\n".
      "       <word>     Search word\n".
      "       <max>      Max results to return (defaults to 5)\n".
      "       <start>    Search start offset (defaults to 0)\n",
      basename($_SERVER['argv'][0])
    );
    exit;
  }
  $max= $p->exists('max') ? intval($p->value('max')) : 5;
  $start= $p->exists('start') ? intval($p->value('start')) : 0;
  
  $s= &new SOAPClient(
    new SOAPHTTPTransport('http://api.google.com/search/beta2'),
    'urn:GoogleSearch'
  );
  try(); {
    $return= $s->invoke('doGoogleSearch', 
      new SOAPNamedItem('key', GOOGLE_KEY),             // Google-KEY
      new SOAPNamedItem('q', $p->value('query')),       // Suchbegriff
      new SOAPNamedItem('start', $start),               // Start-Index
      new SOAPNamedItem('maxresults', $max),            // Maximale Anzahl Ergebnisse
      new SOAPNamedItem('filter', FALSE),               // Automatischer Filter, der ähnliche Seiten versteckt
      new SOAPNamedItem('restrict', ''),                // Begrenzung auf eine Google-Kategorie
      new SOAPNamedItem('safeSearch', FALSE),           // Filtert "Adult Content"
      new SOAPNamedItem('lr', ''),                      // "Language Restrict"
      new SOAPNamedItem('ie', ''),                      // "Input Encoding"
      new SOAPNamedItem('oe', '')                       // "Output Encoding"
    );
    
    // Show XML source?
    if ($p->exists('xml')) {
      echo '---> SEND: '.(is_a($s->message, 'SOAPMessage') ? 
        $s->message->getSource(0)
        : "n/a\n"
      );
      echo '---> RECV: '.(is_a($s->answer, 'SOAPMessage') ? 
        $s->answer->getSource(0) 
        : "n/a\n"
      );
    }
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit;
  }
  
  echo '$return:= '; var_dump($return);
?> 
