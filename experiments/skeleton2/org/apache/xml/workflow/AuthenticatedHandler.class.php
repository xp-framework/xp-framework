<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('org.apache.xml.workflow.Handler');

  /**
   * Handler whose prerequisites are not met until the user
   * is logged on.
   *
   * @see      xp://org.apache.xml.workflow.Handler
   * @purpose  Handler
   */
  class AuthenticatedHandler extends Handler {

    /**
     * Return whether prerequisites for this handler have been met
     *
     * @access  public
     * @param   &org.apache.xml.workflow.Context context
     * @return  bool
     */
    public function prerequisitesMet(&$context) {
      return $context->user->isLoggedOn();
    }

  }
?>
