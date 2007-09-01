<?php
/* This class is part of the XP framework
 *
 * $Id: AbstractAuthenticatedState.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace scriptlet::xml::workflow;

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
