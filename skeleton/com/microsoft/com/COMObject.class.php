<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * COM object
   * 
   * <quote>
   * COM is a technology which allows the reuse of code written in any language 
   * (by any language) using a standard calling convention and hiding behind 
   * APIs the implementation details such as what machine the Component is 
   * stored on and the executable which houses it. It can be thought of as a 
   * super Remote Procedure Call (RPC) mechanism with some basic object roots. 
   * It separates implementation from interface.
   * 
   * COM encourages versioning, separation of implementation from interface and 
   * hiding the implementation details such as executable location and the 
   * language it was written in.
   * </quote>
   *
   * @see      http://www.microsoft.com/Com/resources/comdocs.asp COM specification
   * @see      http://www.developmentor.com/dbox/yacl.htm Yet Another COM Library (YACL) 
   * @ext      com
   * @ext      overload
   * @purpose  Base class
   * @platform Windows
   */
  class COMObject extends Object {
    var
      $h   = NULL;
  
    /**
     * Constructor
     *
     * @access  public
     * @param   string identifier
     * @param   string server default NULL
     */    
    function __construct($identifier, $server= NULL) {
      
      $this->h= com_load($identifier, $server);
    }
    
    /**
     * Magic interceptor for member read access
     *
     * @access  protected
     * @param   string name
     * @param   &mixed value
     * @return  bool success
     */
    function __get($name, &$value) {
      $value= &com_get($this->h, $name);
      return TRUE;
    }
    
    /**
     * Magic interceptor for member write access
     *
     * @access  protected
     * @param   string name
     * @param   &mixed value
     * @return  bool success
     */
    function __set($name, &$value) {
      com_set($this->h, $name, $value);
      return TRUE;
    }
    
    /**
     * Magic interceptor for member method access
     *
     * @access  protected
     * @param   string name
     * @param   array args
     * @param   &mixed return
     * @return  bool success
     */
    function __call($name, $args, $return) {
      $return= call_user_func_array(
        'com_invoke', 
        array_merge(array(&$this->h, $name), $args)
      );
      return TRUE;
    }
    
    /**
     * Destructor
     *
     * @access  public
     */
    function __destruct() {
      com_release($this->h);
      $this->h= NULL;
      parent::__destruct();
    }
  } overload('COMObject');
?>
