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
  class Rss2State extends AbstractMonoState {

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function process(&$request, &$response, &$context) {
      parent::process($request, $response, $context);

      $uri= $request->getUri();
      $response->addFormResult(Node::fromArray($uri, 'uri'));

      // Load latest 5 pictures
      $catalog= &$this->_getCatalog();
      
      $pictures= &$response->addFormResult(new Node('pictures'));
      $id= $catalog->getCurrent_id();
      for ($i= 0; $id && $i < 5; $i++) {
        $picture= $this->getPictureById($id);
        $date= $catalog->dateFor($id);
        
        $pictures->addChild(Node::fromArray(array(
          'id'      => $id,
          'date'    => $date,
          'pubDate' => new Date($date),
          'picture' => $picture
        ), 'picture'));
        
        // Find previous picture
        $pdate= $catalog->getPredecessorDate($date);
        if ($pdate) {
          $id= $catalog->idFor($pdate);
        } else {
          $id= FALSE;
        }
      }
      
      
      return TRUE;
    }
  }
?>
