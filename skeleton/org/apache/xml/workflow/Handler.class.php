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
      $errors   = array(),
      $wrappers = array();
    
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
     * Set a wrapper for a field
     *
     * @access  public
     * @param   string field
     * @param   &org.apache.xml.workflow.Wrapper wrapper  
     */
    function setWrapper($field, &$wrapper) {
      $this->wrappers[$field]= &$wrapper;
    }
    
    /**
     * Get a wrapper for a field
     *
     * @access  public
     * @param   string field
     * @return  &org.apache.xml.workflow.Wrapper wrapper, if existant or NULL
     */
    function getWrapper($field) {
      if (isset($this->wrappers[$field])) return $this->wrappers[$field]; else return NULL;
    }

    /**
     * Check whether a wrapper exists for a given field
     *
     * @access  public
     * @param   string field
     * @return  bool
     */
    function hasWrapper($field) {
      return isset($this->wrappers[$field]);
    }
    
    /**
     * Add an error
     *
     * @access  public
     * @param   string statuscode
     * @return  bool FALSE
     */  
    function addError($statuscode, $field= '*') {
      $this->errors[]= array($field, $statuscode);
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
    function isActive(&$context) {
      return TRUE;
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
