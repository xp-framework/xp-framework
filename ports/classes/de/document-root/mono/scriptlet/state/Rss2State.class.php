<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('de.document-root.mono.scriptlet.AbstractMonoState');

  /**
   * Output RSS2 feed
   *
   * @purpose  RSS feed
   */
  class Rss2State extends AbstractMonoState {

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

      $uri= $request->getUri();
      $response->addFormResult(Node::fromArray($uri, 'uri'));

      // Load latest 5 pictures
      $catalog= &$this->_getCatalog();
      
      $pictures= &$response->addFormResult(new Node('pictures'));
      $id= $catalog->getCurrent_id();
      for ($i= 0; $id && $i < 5; $i++) {
        $picture= $this->getPictureById($id);
        $date= $catalog->dateFor($id);
        
        $n= &$pictures->addChild(Node::fromArray(array(
          'id'      => $id,
          'date'    => $date,
          'pubDate' => new Date($date),
        ), 'picture'));
        $n->addChild($picture->toXML());
        
        // Find previous picture
        $pdate= $catalog->getPredecessorDate($date);
        if ($pdate) {
          $id= $catalog->idFor($pdate);
        } else {
          $id= FALSE;
        }
      }
      
      $response->setHeader('Content-Type', 'text/xml');
      return TRUE;
    }
  }
?>
