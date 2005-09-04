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

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function process(&$request, &$response, &$context) {
      parent::process($request, $response, $context);
      
      // Find out which still to show
      if (1 != sscanf($request->getQueryString(), '%d', $id)) {
        $id= $this->getLast_id();
      }
      
      // Prevent adding nonexistant or non-published pictures
      if (!$id || !$this->isPublished($id)) { return TRUE; }
      
      try(); {
        $picture= &$this->getPictureById($id);
        $description= &$this->getPictureDescriptionById($id);
        $comments= &$this->getPictureCommentsById($id);
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      
      $response->addFormResult(Node::fromObject($picture, 'picture'));
      $description && $response->addFormResult(new Node('description', new PCData($description)));
      $comments && $response->addFormResult(Node::fromArray($comments, 'comments'));
      
      $response->addFormResult(new Node('navigation', NULL, array(
        'current-id'  => $id,
        'previous-id' => ($id > 1 ? $id - 1 : ''),
        'next-id'     => ($this->isPublished($id + 1) ? $id + 1 : '')
      )));

      
      return TRUE;
    }
  }
?>
