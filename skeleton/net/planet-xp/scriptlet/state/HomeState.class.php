<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'scriptlet.xml.workflow.AbstractState',
    'io.FileUtil'
  );

  /**
   * Handles /xml/home
   *
   * @purpose  State
   */
  class HomeState extends AbstractState {

    /**
     * Process this state.
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   &scriptlet.xml.XMLScriptletResponse response
     */
    function process(&$request, &$response) {
    
      try(); {
        $entries= &FileUtil::getContents(new File('../cache/syndicate-1.xml'));
        $feeds= &FileUtil::getContents(new File('../cache/syndicated-feeds-1.xml'));
      } if (catch('XMLFormatException', $e)) {
      
        // Ignore
      } if (catch('IOException', $e)) {
      
        // Ignore
      }
      
      $response->addFormResult(new Node('offset', $request->getParam('offset', 0)));
      $response->addFormResult(new Node('syndicates', new PCData($entries)));
      $response->addFormResult(new Node('syndication', new PCData($feeds)));
    }
  }
?>
