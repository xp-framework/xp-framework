<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('de.document-root.mono.scriptlet.AbstractMonoState');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class ViewState extends AbstractMonoState {
    var
      $date=    '',
      $id=      0;
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
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
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function process(&$request, &$response, &$context) {
      parent::process($request, $response, $context);
      $this->fetchShotRequest($request);

      // Prevent adding nonexistant or non-published pictures
      if (!$this->id ) { return TRUE; }
      
      try(); {
        $picture= &$this->getPictureById($this->id);
        $description= &$this->getPictureDescriptionById($this->id);
        $comments= &$this->getPictureCommentsById($this->id);
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      
      $catalog= &$this->_getCatalog();
      
      $response->addFormResult(Node::fromObject($picture, 'picture'));
      $description && $response->addFormResult(new Node('description', new PCData($description)));
      $comments && $response->addFormResult(Node::fromArray($comments, 'comments'));
      
      $response->addFormResult(new Node('navigation', NULL, array(
        'current'     => $this->date,
        'currentid'   => $this->id,
        'latestdate'  => $catalog->getLatestDate(),
        'nextdate'    => $catalog->getSuccessorDate($this->date),
        'prevdate'    => $catalog->getPredecessorDate($this->date)
      )));

      
      return TRUE;
    }
  }
?>
