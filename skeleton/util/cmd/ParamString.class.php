<?php
/**
 *
 * $Id$
 */
 
  /**
   * Stellt hilfreiche Funktionen für Kommadozeilen-Argumente zur Verfügung
   * Unterstützt kurze und lange Optionen, also -h bzw. --help
   *
   * @purpose Einfacher Zugriff auf Kommandozeilen-Argumente
   * @example usage.php
   */
  class ParamString extends Object {
    var $list= array();
    var $count= 0;
    var $string= '';
    
    /**
     * Constructor
     * 
     * @param mixed params default NULL Entweder Default-Aufruf mit Memberübergabe oder das $GLOBALS['argv']
     */
    function __construct($params= NULL) {
      if (is_array($params) && is_numeric(key($params))) {
        $this->setParams($params);
        $params= NULL;
      }
      Object::__construct($params);
    }
    
    /**
     * Setzt die Parameter
     * 
     * @access  public
     * @param   array params Der Parameter-Array
     */  
    function setParams($params) {
      $this->list   = $params;
      $this->count  = sizeof($params);
      $this->string = implode(' ', $params);
    }
   
    /**
     * Private Helper-Funktion, die durch den Param-Array iteriert
     * 
     * @access  private
     * @param   string long Der Lange Parameter (ohne --)
     * @param   string short default NULL Der kurze Parameter (ohne -), defaultet auf den ersten Buchstaben des langen Parameters
     * @return  mixed Position, an der der Wert des Parameters steht, oder, wenn nicht vorhanden, FALSE
     */ 
    function _find($long, $short= NULL) {
      if (is_null($short)) $short= $long{0};
      for ($i= 0; $i< sizeof($this->list); $i++) {
      
        // Kurze Schreibweise -f datei (der Wert ist der nächste Key)
        if ($this->list[$i] == '-'.$short) return $i+ 1;
        
        // Lange Schreibweise --help (kein Wert)
        if ($this->list[$i] == '--'.$long) return $i;
        
        // Lange Schreibweise --file=datei (der Wert ist im gleichen Key)
        if (substr($this->list[$i], 0, strlen($long)+ 3) == '--'.$long.'=') return $i;
      }
      
      return FALSE;
    }
   
    /**
     * Prüft, ob ein Parameter gesetzt ist
     * 
     * @access  public
     * @param   string long Der Lange Parameter (ohne --)
     * @param   string short default NULL Der kurze Parameter (ohne -), defaultet auf den ersten Buchstaben des langen Parameters
     * @return  boolean Gesetzt
     */  
    function exists($long, $short= NULL) {
      return ($this->_find($long, $short) !== FALSE);
    }
    
    /**
     * Gibt den Wert eines Parameters zurück
     * 
     * @access  public
     * @param   string long Der Lange Parameter (ohne --)
     * @param   string short default NULL Der kurze Parameter (ohne -), defaultet auf den ersten Buchstaben des langen Parameters
     * @return  string Parameter-Wert
     */ 
    function value($long, $short= NULL) {
      $pos= $this->_find($long, $short);
      return ($pos !== FALSE
        ? str_replace("--{$long}=", '', $this->list[$pos])
        : FALSE
      );
    }
  }
?>
