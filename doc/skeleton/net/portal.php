<?php
  /* Dokumentiert HTTP-Requests
   * 
   * $Id$
   */
   
  require_once('../../../skeleton/lang.base.php');
  import('net.http.HTTPRequest');
  import('net.session.ToolSession');

  // Usernamen und Passwort holen  
  $user= readline('Username: ');
  $pass= readline('Passwort: ');

  // Session erzeugen
  $sess= new ToolSession();
  $sess->tool_id= 0;
  $sess->connect();
  
  echo "Anmelden...\n";
  flush();
  
  // Portal zur Authentifizierung ansprechen
  $req= new HTTPRequest(array(
    'host'      => 'portal.elite.schlund.de',
    'target'    => '/sess/'.$sess->ID.'/check_user.ciml'
  ));
  $req->post(array(
    'jump[]'    => 'faq',
    'user'      => $user,
    'pass'      => $pass
  ));
  
  // OK? Bei erfolgreicher Auth bekommen wir einen Cookie und werden relocated
  if (!isset($req->response->SetCookie)) {
    preg_match('/&e=([0-9]+)/', $req->response->HTTPredirect['query'], $regs);
    echo "Fehler $regs[1]!\n";
    exit;
  }
  
  // Request auf die Sprungseite absetzen
  $req->target= $req->response->HTTPredirect['path'];
  $req->get($req->response->HTTPredirect['query']);
  
  // Wohin? Auf das Tool?
  if ($req->response->HTTPredirect['host']!= 'cms.kundenserver.de') {
    echo "Fehler -> keine Rechte!\n";
    exit;
  }
  
  // OK
  echo "Authentifiziert: \n".$req->response->Location."\n";
?>
