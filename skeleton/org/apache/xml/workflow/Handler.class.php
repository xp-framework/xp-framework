<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Handler
   *
   * @see      xp://org.apache.xml.workflow.State#addHandler
   * @purpose  Abstract base class
   */
  class Handler extends Object {
    var
      $form     = '',
      $errors   = array();
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string form
     */
    function __construct($form) {
      $this->form= $form;
      parent::__construct();
    }
     
    /**
     * Add an error
     *
     * @access  public
     * @param   string statuscode
     * @return  bool FALSE
     */  
    function addError($statuscode) {
      $this->errors[]= $statuscode;
      return FALSE;
    }
    
    /**
     * Handle submitted data
     *
     * @access  public
     * @param   &org.apache.xml.workflow.Context context
     * @param   &org.apache.xml.HttpScriptletRequest request
     * @return  bool
     */
    function handleSubmittedData(&$context, &$request) {
      return TRUE;
    }
    
    /**
     * Return whether prerequisites for this handler have been met
     *
     * @access  public
     * @param   &org.apache.xml.workflow.Context context
     * @return  bool
     */
    function prerequisitesMet(&$context) {
      return TRUE;
    }
    
    /**
     * Return whether this handler is active
     *
     * @access  public
     * @param   &org.apache.xml.workflow.Context context
     * @param   string st submit trigger name
     * @return  bool
     */
    function isActive(&$context, $st) {
      return ($st == $this->form);
    }
    
    /**
     * Return whether this handler needs data
     *
     * @access  public
     * @param   &org.apache.xml.workflow.Context context
     * @return  bool
     */
    function needsData(&$context) {
      return FALSE;
    }

  }
?>
