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
    public
      $form     = '',
      $errors   = array(),
      $wrappers = array();
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string form
     */
    public function __construct($form) {
      $this->form= $form;
      
    }
    
    /**
     * Set a wrapper for a field
     *
     * @access  public
     * @param   string field
     * @param   &org.apache.xml.workflow.Wrapper wrapper  
     */
    public function setWrapper($field, &$wrapper) {
      $this->wrappers[$field]= $wrapper;
    }
    
    /**
     * Get a wrapper for a field
     *
     * @access  public
     * @param   string field
     * @return  &org.apache.xml.workflow.Wrapper wrapper, if existant or NULL
     */
    public function getWrapper($field) {
      if (isset($this->wrappers[$field])) return $this->wrappers[$field]; else return NULL;
    }

    /**
     * Check whether a wrapper exists for a given field
     *
     * @access  public
     * @param   string field
     * @return  bool
     */
    public function hasWrapper($field) {
      return isset($this->wrappers[$field]);
    }
    
    /**
     * Add an error
     *
     * @access  public
     * @param   string statuscode
     * @return  bool FALSE
     */  
    public function addError($statuscode, $field= '*') {
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
    public function handleSubmittedData(&$context, &$request) {
      return TRUE;
    }
    
    /**
     * Return whether prerequisites for this handler have been met
     *
     * @access  public
     * @param   &org.apache.xml.workflow.Context context
     * @return  bool
     */
    public function prerequisitesMet(&$context) {
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
    public function isActive(&$context) {
      return TRUE;
    }
    
    /**
     * Return whether this handler needs data
     *
     * @access  public
     * @param   &org.apache.xml.workflow.Context context
     * @return  bool
     */
    public function needsData(&$context) {
      return FALSE;
    }

  }
?>
