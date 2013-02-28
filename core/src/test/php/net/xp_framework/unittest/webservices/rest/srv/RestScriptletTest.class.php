<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'webservices.rest.srv.RestScriptlet'
  );
  
  /**
   * Test response class
   *
   * @see  xp://webservices.rest.srv.RestScriptlet
   */
  class RestScriptletTest extends TestCase {

    /**
     * Test constructor
     * 
     */
    #[@test]
    public function can_create() {
      new RestScriptlet('net.xp_framework.unittest.webservices.rest.srv.fixture');
    }
  }
?>
