<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * (Insert class' description here)
   *
   * @see      reference
   * @purpose  Abstract base class
   */
  class Handler extends Object {
    var
      $errors   = array();
      
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */  
    function addError($statuscode) {
      $this->errors[]= $statuscode;
      return FALSE;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function handleSubmittedData(&$context, &$request) {
      return TRUE;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function prerequisitesMet(&$context) {
      return TRUE;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function isActive(&$context) {
      return TRUE;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function needsData(&$context) {
      return FALSE;
    }
  }
?>
