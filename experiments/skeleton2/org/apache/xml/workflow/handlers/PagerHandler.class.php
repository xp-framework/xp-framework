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
    public
      $pager    = '';

    /**
     * Constructor
     *
     * @access  public
     * @param   string form
     * @param   string pager
     */
    public function __construct($form, $pager) {
      $this->pager= $pager;
      parent::__construct($form);
    }

    /**
     * Return whether this handler needs data
     *
     * @access  public
     * @param   &org.apache.xml.workflow.Context context
     * @return  bool
     */
    public function needsData(&$context) {
      $pagercontextresource= $context->getContextResource($this->form, $this->pager);
      
      // Update pager if necessary
      if (!$pagercontextresource->hasItems()) {
        $pagercontextresource->update($this);
      }
      
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
    public function handleSubmittedData(&$context, &$request) {
      $pagercontextresource= $context->getContextResource($this->form, $this->pager);
      
      // Update pager if necessary
      if ($request->hasParam('update')) {
        $pagercontextresource->update($this);
      }
      
      // Switch on pager commands
      $show= ($request->hasParam('pager') 
        ? $request->getParam('pager') 
        : 'first'
      );
      switch ($show) {
        case 'first': 
          $pagercontextresource->showFirst(); break;

        case 'last':
          $pagercontextresource->showLast(); break;

        case 'prev':
          $pagercontextresource->showPrevious(); break;

        case 'next':
          $pagercontextresource->showNext(); break;

        default: 
          $pagercontextresource->setShowFrom(intval($show));
      }
      
      return TRUE;
    }

  }
?>
