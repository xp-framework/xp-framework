<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'org.apache.xml.workflow.Handler',
    'org.apache.xml.workflow.contextresources.PagerContextResource'
  );

  /**
   * Login handler
   *
   * @see      xp://org.apache.xml.workflow.Handler
   * @purpose  Handler
   */
  class PagerHandler extends Handler {

    /**
     * Constructor
     *
     * @access  public
     * @param   string form
     * @param   &org.apache.xml.workflow.contextresources.PagerContextResource pcrs
     */
    function __construct($form, &$pcrs) {
      $this->pager= &$pcrs;
      parent::__construct($form);
    }

    /**
     * Return whether this handler needs data
     *
     * @access  public
     * @param   &org.apache.xml.workflow.Context context
     * @return  bool
     */
    function needsData(&$context) {
      return TRUE;
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
    
      // Update pager if necessary
      if (!$this->pager->hasItems()) {
        $this->pager->update();
      }
      
      // Switch on pager commands
      $show= ($request->hasParam($this->form.'_pager') 
        ? $request->getParam($this->form.'_pager') 
        : 'first'
      );
      switch ($show) {
        case 'first': 
          $this->pager->showFirst(); break;

        case 'last':
          $this->pager->showLast(); break;

        case 'prev':
          $this->pager->showPrevious(); break;

        case 'next':
          $this->pager->showNext(); break;

        default: 
          $this->pager->setShowFrom(intval($show));
      }
      
      return TRUE;
    }

  }
?>
