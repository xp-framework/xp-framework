<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('scriptlet.xml.workflow.AbstractState');

  /**
   * Handles /xml/release
   *
   * @purpose  State
   */
  class ReleaseState extends AbstractState {

    /**
     * Process this state.
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   scriptlet.xml.XMLScriptletResponse response
     */
    public function process($request, $response) {
      if (sscanf($request->getQueryString(), '%d.%d.%d%s', $major, $minor, $patch, $mod) < 3) {
        throw new HttpScriptletException('Malformed query string', HTTP_BAD_REQUEST);
      }
      
      $response->addFormResult(new Node('release', NULL, array(
        'version' => $major.'.'.$minor.'.'.$patch.$mod,
        'series'  => $major.'.'.$minor,
        'mod'     => $mod
      )));
    }
  }
?>
