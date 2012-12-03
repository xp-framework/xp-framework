<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'webservices.rest.srv.RestRoute'
  );
  
  /**
   * Test default router
   *
   * @see  xp://webservices.rest.srv.RestRoute
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

    /**
     * Test toString()
     *
     */
    #[@test]
    public function string_representation() {
      $r= new RestRoute('GET', '/resource/{id}/{sub}', $this->target, NULL, NULL);
      $this->assertEquals(
        'webservices.rest.srv.RestRoute(GET /resource/{id}/{sub} -> void fixtureTarget())', 
        $r->toString()
      );
    }

    /**
     * Test toString()
     *
     */
    #[@test]
    public function string_representation_with_produces() {
      $r= new RestRoute('GET', '/resource/{id}/{sub}', $this->target, NULL, array('text/json'));
      $this->assertEquals(
        'webservices.rest.srv.RestRoute(GET /resource/{id}/{sub} -> void fixtureTarget() @ text/json)', 
        $r->toString()
      );
    }

    /**
     * Test toString()
     *
     */
    #[@test]
    public function string_representation_with_accepts_and_produces() {
      $r= new RestRoute('GET', '/resource/{id}/{sub}', $this->target, array('text/xml'), array('text/json'));
      $this->assertEquals(
        'webservices.rest.srv.RestRoute(GET /resource/{id}/{sub} @ text/xml -> void fixtureTarget() @ text/json)', 
        $r->toString()
      );
    }

    /**
     * Test toString()
     *
     */
    #[@test]
    public function string_representation_with_param() {
      $r= new RestRoute('GET', '/resource/{id}', $this->target, NULL, NULL);
      $r->addParam('id', new RestParamSource('id', ParamReader::forName('path')));
      $this->assertEquals(
        'webservices.rest.srv.RestRoute(GET /resource/{id} -> void fixtureTarget(@$id: path(\'id\')))', 
        $r->toString()
      );
    }

    /**
     * Test toString()
     *
     */
    #[@test]
    public function string_representation_with_params() {
      $r= new RestRoute('GET', '/resource/{id}/{sub}', $this->target, NULL, NULL);
      $r->addParam('id', new RestParamSource('id', ParamReader::forName('path')));
      $r->addParam('sub', new RestParamSource('sub', ParamReader::forName('path')));
      $this->assertEquals(
        'webservices.rest.srv.RestRoute(GET /resource/{id}/{sub} -> void fixtureTarget(@$id: path(\'id\'), @$sub: path(\'sub\')))', 
        $r->toString()
      );
    }
  }
?>
