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
   * Handles /xml/news/view
   *
   * @purpose  State
   */
  class ViewNewsState extends AbstractState {

    /**
     * Process this state.
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   &scriptlet.xml.XMLScriptletResponse response
     */
    function process(&$request, &$response) {
      
      // Retrieve news from database - TBD: caching
      try(); {
        $item= XPNews::getByNews_id($request->getData());
      } if (catch('SQLException', $e)) {
        $item= NULL;
        // Fall through
      }
      
      $response->addFormResult(Node::fromObject($item, 'item'));
    }
  }
?>
