<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'session.RemoteSession',
    'net.xp_framework.unittest.scriptlet.HttpSessionTest'
  );

  /**
   * TestCase
   *
   * @see      reference
   * @purpose  purpose
   */
  class RemoteSessionTest extends HttpSessionTest {
  
    /**
     * Helper method to create the testing session object.
     *
     * @return  scriptlet.HttpSession
     */
    protected function _session() {
      return new RemoteSession();
    }
  }
?>
