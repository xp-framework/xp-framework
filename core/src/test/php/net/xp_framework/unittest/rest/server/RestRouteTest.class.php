<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'webservices.rest.server.routing.RestRoute'
  );
  
  /**
   * Test default router
   *
   * @see  xp://webservices.rest.server.routing.RestRoute
   */
  class RestRouteTest extends TestCase {
    protected $target= NULL;

    /**
     * Setup
     * 
     */
    public function setUp() {
      $this->target= $this->getClass()->getMethod('fixtureTarget');
    }

    /**
     * Target method
     */
    #[@webservice]
    public function fixtureTarget() {
      // Intentionally empty
    }


    /**
     * Test getVerb()
     * 
     */
    #[@test]
    public function verb() {
      $r= new RestRoute('GET', '/resource', $this->target, NULL, NULL);
      $this->assertEquals('GET', $r->getVerb());
    }

    /**
     * Test getVerb()
     * 
     */
    #[@test]
    public function verb_is_uppercased() {
      $r= new RestRoute('Get', '/resource', $this->target, NULL, NULL);
      $this->assertEquals('GET', $r->getVerb());
    }

    /**
     * Test getPath()
     * 
     */
    #[@test]
    public function path() {
      $r= new RestRoute('GET', '/resource', $this->target, NULL, NULL);
      $this->assertEquals('/resource', $r->getPath());
    }

    /**
     * Test getTarget()
     * 
     */
    #[@test]
    public function target() {
      $r= new RestRoute('GET', '/resource', $this->target, NULL, NULL);
      $this->assertEquals($this->target, $r->getTarget());
    }

    /**
     * Test getAccepts()
     * 
     */
    #[@test]
    public function accepts() {
      $r= new RestRoute('GET', '/resource', $this->target, array('text/json'), NULL);
      $this->assertEquals(array('text/json'), $r->getAccepts());
    }

    /**
     * Test getAccepts()
     * 
     */
    #[@test]
    public function accepts_default() {
      $r= new RestRoute('GET', '/resource', $this->target, NULL, NULL);
      $this->assertEquals(array('text/json'), $r->getAccepts((array)'text/json'));
    }

    /**
     * Test getProduces()
     * 
     */
    #[@test]
    public function produces() {
      $r= new RestRoute('GET', '/resource', $this->target, NULL, array('text/json'));
      $this->assertEquals(array('text/json'), $r->getProduces());
    }

    /**
     * Test getProduces()
     * 
     */
    #[@test]
    public function produces_default() {
      $r= new RestRoute('GET', '/resource', $this->target, NULL, NULL);
      $this->assertEquals(array('text/json'), $r->getProduces((array)'text/json'));
    }

    /**
     * Test getPattern()
     * 
     */
    #[@test]
    public function pattern() {
      $r= new RestRoute('GET', '/resource', $this->target, NULL, NULL);
      $this->assertEquals('#^/resource$#', $r->getPattern());
    }

    /**
     * Test getPattern()
     * 
     */
    #[@test]
    public function pattern_with_placeholder() {
      $r= new RestRoute('GET', '/resource/{id}', $this->target, NULL, NULL);
      $this->assertEquals('#^/resource/(?P<id>[%\w:\+\-\.]*)$#', $r->getPattern());
    }

    /**
     * Test getPattern()
     * 
     */
    #[@test]
    public function pattern_with_two_placeholders() {
      $r= new RestRoute('GET', '/resource/{id}/{sub}', $this->target, NULL, NULL);
      $this->assertEquals('#^/resource/(?P<id>[%\w:\+\-\.]*)/(?P<sub>[%\w:\+\-\.]*)$#', $r->getPattern());
    }

  }
?>
