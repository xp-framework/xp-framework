<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('scriptlet.xml.workflow.AbstractState');

  /**
   * Authenticated state
   *
   * @purpose  Base class for states needing an authentication
   */
  class AbstractAuthenticatedState extends AbstractState {
  
    /**
     * Returns whether we need an authentication. Always returns
     * TRUE in this implementation.
     *
     * @return  bool
     */
    public function requiresAuthentication() {
      return TRUE;
    }
  }
?>
