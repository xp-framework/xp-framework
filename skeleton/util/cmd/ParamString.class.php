<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  /**
   * This class provides helpful functions for commandline applications
   * to parse the argument list
   * It supports short and long options, e.g. -h or --help
   *
   * @purpose Easy access to commandline arguments
   * @example usage.php
   */
  class ParamString extends Object {
    var $list= array();
    var $count= 0;
    var $string= '';
    
    /**
     * Constructor
     * 
     * @access  public
     * @param   array list default NULL the argv array. If omitted, $_SERVER['argv'] is used
     */
    function __construct($list= NULL) {
      $this->setParams(NULL === $list ? $_SERVER['argv'] : $list);
      parent::__construct();
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
     * Private helper function that iterates through array
     * 
     * @access  private
     * @param   string long long parameter (w/o --)
     * @param   string short default NULL Short parameter (w/o -), defaults to the first char of the long param
     * @return  mixed position on which the parameter is placed or FALSE if nonexistant
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
     * Checks whether a parameter is set
     * 
     * @access  public
     * @param   string long long parameter (w/o --)
     * @param   string short default NULL Short parameter (w/o -), defaults to the first char of the long param
     * @return  boolean Gesetzt
     */  
    function exists($long, $short= NULL) {
      if (is_int($long)) return isset($this->list[$long]);
      return ($this->_find($long, $short) !== FALSE);
    }
    
    /**
     * Gibt den Wert eines Parameters zurück
     * 
     * @access  public
     * @param   string long long parameter (w/o --)
     * @param   string short default NULL Short parameter (w/o -), defaults to the first char of the long param
     * @param   string default default NULL A default value if parameter does not exist
     * @return  string 
     * @throws IllegalArgumentException if parameter does not exist and no default value was supplied.
     */ 
    function value($long, $short= NULL, $default= NULL) {
      if (is_int($long)) {
        if (NULL === $default)
          return throw (new IllegalArgumentException ('Parameter #'.$long.' does not exist'));        

        return $this->list[$long];
      }        
  
      $pos= $this->_find($long, $short);
      if (FALSE === $pos && NULL === $default)
        return throw (new IllegalArgumentException ('Parameter --'.$long.' does not exist'));
        
      return ($pos !== FALSE
        ? str_replace("--{$long}=", '', $this->list[$pos])
        : $default
      );
    }
  }
?>
