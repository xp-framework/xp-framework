<?php
  require('../../../skeleton/lang.base.php');
  import('de.schlund.domain.IsFree');
  
  $check= new IsFree();

  // Erst die Statusabfrage, da die Verbindung nach dem Abrufen der Domainergebnisse
  // sofort geschlossen wird.
  try(); {
    $status= &$check->status();
  } if ($e= catch(E_ANY_EXCEPTION)) {
    var_dump($e);
    exit;
  }

  echo "\n--- Status ------------------------------------------------------\n";
  foreach ($status as $tld=> $info) {
    printf(".%-8s: %10d Domains, Stand %s\n", $tld, $info->numDoms, date('d.m.Y, H:i', $info->dataTime));
  }
  
  // Ein paar Domains abfragen
  $check->query= array(
    'thekid.de',
    'puretec.blupp',
    'foo%bar',
    'dieser-domainname-ist-noch-frei.org'
  );
  try(); {
    $results= &$check->query();
  } if ($e= catch(E_ANY_EXCEPTION)) {
    var_dump($e);
    exit;
  }
  
  // Schöne beschreibende Texte
  $description= array(
    ISFREE_ERROR        => 'Fehler',
    ISFREE_FREE         => 'Verfügbar',
    ISFREE_UNAVAIL      => 'Nicht verfügbar',
    ISFREE_UNSUPPORTED  => 'Nicht unterstützt'
  );
  
  echo "\n--- Abfrageergebnis ---------------------------------------------\n";
  foreach ($results as $domainname=> $status) {
    printf("%-40s: %-20s\n", $domainname, $description[$status]);
  }
  
  echo "\n\n";
?>
