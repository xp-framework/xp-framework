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

    /**
     * Creates a new fixture
     *
     * @return webservices.rest.srv.RestScriptlet
     */
    protected function newFixture() {
      return new RestScriptlet('net.xp_framework.unittest.webservices.rest.srv.fixture');
    }

    /**
     * Test constructor
     * 
     */
    #[@test]
    public function router_accessors() {
      $fixture= $this->newFixture();
      $router= new RestDefaultRouter();
      $fixture->setRouter($router);
      $this->assertEquals($router, $fixture->getRouter());
    }

    /**
     * Test constructor
     * 
     */
    #[@test]
    public function context_accessors() {
      $fixture= $this->newFixture();
      $context= new RestContext();
      $fixture->setContext($context);
      $this->assertEquals($context, $fixture->getContext());
    }
  }
?>
