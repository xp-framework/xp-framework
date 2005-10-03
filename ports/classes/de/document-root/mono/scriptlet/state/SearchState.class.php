<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('de.document-root.mono.scriptlet.AbstractMonoState');

  /**
   * Search state.
   *
   * @purpose  Search the site.
   */
  class SearchState extends AbstractMonoState {

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
      return TRUE;
    }
  }
?>
