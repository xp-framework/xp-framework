<?php
/* Diese Klasse ist Bestandteil des XP-Frameworks
 *
 * $Id$
 */

  uses(
    'io.File'
  );
    
  /**
   * Temporäre Datei. Das Verzeichnis, in dem die Datei landet, defaultet nach /tmp -
   * ist eine der beiden Umgebungsvariablen "TMP" oder "TEMP" deklariert
   * und nicht leer, so wird deren Wert stattdessen genommen 
   *
   * @see io.File
   */
  class TempFile extends File {
  
    /**
     * Constructor
     *
     * @param  string prefix default "tmp" Das Präfix
     * see     php://tempnam
     */
    function __construct($prefix= 'tmp') {
      $dir= '/tmp';
      if ('' != getenv('TEMP')) $dir= getenv('TEMP');
      if ('' != getenv('TMP')) $dir= getenv('TMP');
      parent::__construct(tempnam($dir, $prefix));
    }
  }
?>
