<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('de.document-root.mono.scriptlet.AbstractMonoState');

  /**
   * State to view a single picture.
   *
   * @purpose  View picture
   */
  class ViewState extends AbstractMonoState {
    var
      $date=    '',
      $id=      0;
    
    /**
     * Fetch shot information from request. Falls back to the
     * latest picture if no or invalid request data i
     * encountered.
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @throws  lang.IllegalArgumentException if a non well-formed date has been submitted
     */
    function fetchShotRequest(&$request) {
      $catalog= &$this->_getCatalog();
    
      // Initialize to latest shot by default
      $this->date= $catalog->getLatestDate();
      $this->id= $catalog->getCurrent_id();

      // Get date from environment
      strlen($request->getEnvValue('IMAGEDATE')) && $this->date= $request->getEnvValue('IMAGEDATE');
      if (3 != sscanf($this->date, '%4d/%2d/%2d', $y, $m, $d)) {
        return throw(new IllegalArgumentException(
          'Non well-formed date given.'
        ));
      }
      
      // Fetch ID for this date
      if ($catalog->dateExists($this->date)) $this->id= $catalog->idFor($this->date);
    }

    /**
     * Process this state.
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   &scriptlet.xml.XMLScriptletResponse response 
     * @param   &scriptlet.xml.Context context
     */
    function process(&$request, &$response, &$context) {
      parent::process($request, $response, $context);
      $this->fetchShotRequest($request);

      // Prevent adding nonexistant or non-published pictures
      if (!$this->id ) { return TRUE; }
      
      try(); {
        $picture= &$this->getPictureById($this->id);
        $comments= &$this->getPictureCommentsById($this->id);
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      
      $catalog= &$this->_getCatalog();
      
      // $response->addFormResult(Node::fromObject($picture, 'picture'));
      $response->addFormResult($picture->toXML());
      $comments && $response->addFormResult(Node::fromArray($comments, 'comments'));
      
      $n= &$response->addFormResult(new Node('navigation', NULL, array(
        'current'     => $this->date,
        'currentid'   => $this->id,
        'latestdate'  => $catalog->getLatestDate(),
        'nextdate'    => $catalog->getSuccessorDate($this->date),
        'prevdate'    => $catalog->getPredecessorDate($this->date)
      )));
      $n->addChild(Node::fromObject(new Date($this->date), 'date'));

      return TRUE;
    }
  }
?>
