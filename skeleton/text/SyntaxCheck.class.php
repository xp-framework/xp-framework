<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Base syntax checker class
   *
   * 
   */
  class SyntaxCheck extends Object {
    var
      $_errors= NULL;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   array values
     */  
    function __construct($values) {
      foreach ($values as $key=> $val) {
        $this->$key= $val;
      }
      parent::__construct();
    }
    
    /**
     * Gets all values
     *
     * @access  
     * @param   
     * @return  
     */
    function getValues() {
      $ret= array();
      foreach (get_object_vars($this) as $key=> $val) {
        if ('_' != $key{0}) $ret[$key]= $val;
      }
      return $ret;
    }
    
    /**
     * Gets error
     *
     * @access  public
     * @return  mixed error
     */
    function getErrors() {
      return $this->_errors;
    }
    
    /**
     * Sets error
     *
     * @access  public
     * @param   mixed error
     */
    function addError($field, $error) {
      if (!is_array($this->_errors)) $this->_errors= array();
      $this->_errors[$field]= $error;
      return FALSE;
    }
  }
?>
