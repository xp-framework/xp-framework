<?php
/* Diese Klasse ist Teil des XP-Frameworks
 * 
 * $Id$
 */
 
  uses('lang.ClassNotFoundException');
  
  /** 
   * Lädt eine Klasse, instanziiert sie aber nicht
   * Beispiel: 
   * <pre>
   *   try(); {
   *     $name= ClassLoader::loadClass($argv[1]);
   *   } if (catch('ClassNotFoundException', $e)) {
   *     die($e->printStackTrace());
   *   }
   * </pre>
   *
   * @access    static
   */
  class ClassLoader extends Object {
  
    /**
     * Lädt eine Klasse
     *
     * @call    static
     * @param   string className Klassename, bspw. net.http.HTTPConnection
     * @return  mixed Eine Instanz der Klasse
     */
    function loadClass($className) {
      if ('php.' != substr($className, 0, 4)) {
        uses($className);
	$phpName= reflect($className);
      } else {
        $phpName= substr($className, 4);
      }
      if (class_exists($phpName)) return $phpName;
      
      // Fehler!
      return throw(new ClassNotFoundException(sprintf(
        'class %s [%s] not found',
        $className,
        $phpName
      )));
    }
  }
?>
