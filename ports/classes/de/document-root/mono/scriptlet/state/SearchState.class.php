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
  class SearchState extends AbstractMonoState {

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
      return TRUE;
    }
  }
?>
