<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.website.doc.scriptlet.state.AbstractApiState');

  /**
   * Handles /xml/api
   *
   * @purpose  State
   */
  class ApiState extends AbstractApiState {

    /**
     * Returns which entry to display
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @return  string entry name
     */
    protected function entryFor($request) {
      return '_overview';
    }
  }
?>
