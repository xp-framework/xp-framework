<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('org.apache.xml.workflow.State');

  /**
   * Authenticated State
   *
   * @see      xp://org.apache.xml.workflow.AbstractXMLScriptlet
   * @purpose  Base class
   */
  class AuthenticatedState extends State {

    /**
     * Return whether this state should be accessible.
     *
     * @access  public
     * @param   &org.apache.xml.workflow.Context context
     * @param   &org.apache.xml.XMLScriptletRequest request
     * @return  bool
     */
    public function isAccessible(&$context, &$request) {
      if (!$context->user->isLoggedOn()) {
      
        // FIXME: TBD: Relocate to login page? 
        // This would NOT be a generic solution!!!
        return FALSE;
      }
      
      return parent::isAccessible($context, $request);
    }
  }
?>
