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
   * Handles /xml/news
   *
   * @purpose  State
   */
  class NewsState extends AbstractState {

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
        $news= XPNews::getByDateOrdered();
      } if (catch('SQLException', $e)) {
        $news= array();
        // Fall through
      }
      
      with ($n= &$response->addFormResult(new Node('news'))); {
        for ($i= 0, $s= sizeof($news); $i < $s; $i++) {
          $item= &$n->addChild(Node::fromObject($news[$i], 'item'));
          $item->addChild(new Node(
            'excerpt', 
            strtok(wordwrap($news[$i]->getBody(), 200, "\0"), "\0")
          ));
        }
      }
    }
  }
?>
