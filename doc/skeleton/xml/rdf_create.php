<?php
/* Demonstriert die Benutzung RDFNewsFeed-Klasse
 * Das Skript erstellt einen Newsfeed
 *
 * Zum Testen kann das andere Beispielskript folgendermaßen benutzt werden:
 * <pre>
 * php -q rdf_create.php | php -q rdf.php php://stdin
 * </pre>
 * 
 * $Id$
 */
 
  require('lang.base.php');
  uses(
    'xml.rdf.RDFNewsFeed', 
    'io.File'
  );
  
  $rdf= &new RDFNewsFeed();
  $rdf->setChannel(
    'XP-News',                                                  // Titel
    'http://doc.edit.schlund.de/',                              // URL
    '"*X*ML *P*HP" - Objektorientiertes PHP-Framework',         // Beschreibung
    NULL,                                                       // Datum (NULL := jetzt)
    'de_DE',                                                    // Sprache
    'Schlund+Partner AG, Interne Anwendungen',                  // Creator
    'Timm Friebe',                                              // Publisher
    'Copyright © 2002'                                          // Copyright
  );
  $rdf->setImage(
    'XP-News Logo',                                             // Titel
    'http://doc.edit.schlund.de/image/logo.png',                // Bild-URL
    'http://doc.edit.schlund.de/'                               // Link
  );
  $rdf->addItem(
    '[New] RDFNewsFeed [v 1.1 2002/07/15 17:49:11 friebe]',     // Titel
    'http://doc.edit.schlund.de/xml.rdf.rdfnewsfeed',           // URL
    'Kapselt RDF- und RSS-Newsfeeds',                           // Beschreibung
    new Date('2002/07/15 17:49:11')                             // Datum
  );
  $rdf->addItem(
    '[Updated] GTKApplication [v 1.4 2002/07/15 16:32:11 friebe]',
    'http://doc.edit.schlund.de/gui.gtk.gtkapplication',
    'CVS Log: Dokumentation',
    new Date('2002/07/15 16:32:11')
  );
  
  // XML-Source anzeigen
  echo $rdf->getSource($indent= FALSE);
  echo "\n";
?>
