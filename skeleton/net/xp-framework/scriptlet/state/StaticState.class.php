<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'scriptlet.xml.workflow.AbstractState',
    'net.xp-framework.db.caffeine.XPNews'
  );

  /**
   * Handles /xml/static
   *
   * @purpose  State
   */
  class StaticState extends AbstractState {

    /**
     * Process this state.
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   &scriptlet.xml.XMLScriptletResponse response
     */
    function process(&$request, &$response) {
    
      // Retrieve top 5 news from database - TBD: caching
      try(); {
        $news= XPNews::getByDateOrdered(5);
      } if (catch('SQLException', $e)) {
        $news= array();
        // Fall through
      }
      
      $n= &$response->addFormResult(new Node('news'));
      $n->addChild(Node::fromArray($news, 'items'));
    }
  }
?>
