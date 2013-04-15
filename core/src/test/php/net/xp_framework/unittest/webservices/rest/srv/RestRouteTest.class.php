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
    protected $handler= NULL;
    protected $target= NULL;

    /**
     * Setup
     * 
     */
    public function setUp() {
      $this->handler= $this->getClass();
      $this->target= $this->handler->getMethod('fixtureTarget');
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
      $r= new RestRoute('GET', '/resource', $this->handler, $this->target, NULL, NULL);
      $this->assertEquals('GET', $r->getVerb());
    }

    /**
     * Test getVerb()
     * 
     */
    #[@test]
    public function verb_is_uppercased() {
      $r= new RestRoute('Get', '/resource', $this->handler, $this->target, NULL, NULL);
      $this->assertEquals('GET', $r->getVerb());
    }

    /**
     * Test getPath()
     * 
     */
    #[@test]
    public function path() {
      $r= new RestRoute('GET', '/resource', $this->handler, $this->target, NULL, NULL);
      $this->assertEquals('/resource', $r->getPath());
    }

    /**
     * Test getHandler()
     * 
     */
    #[@test]
    public function handler() {
      $r= new RestRoute('GET', '/resource', $this->handler, $this->target, NULL, NULL);
      $this->assertEquals($this->handler, $r->getHandler());
    }

    /**
     * Test getTarget()
     * 
     */
    #[@test]
    public function target() {
      $r= new RestRoute('GET', '/resource', $this->handler, $this->target, NULL, NULL);
      $this->assertEquals($this->target, $r->getTarget());
    }

    /**
     * Test getAccepts()
     * 
     */
    #[@test]
    public function accepts() {
      $r= new RestRoute('GET', '/resource', $this->handler, $this->target, array('text/json'), NULL);
      $this->assertEquals(array('text/json'), $r->getAccepts());
    }

    /**
     * Test getAccepts()
     * 
     */
    #[@test]
    public function accepts_default() {
      $r= new RestRoute('GET', '/resource', $this->handler, $this->target, NULL, NULL);
      $this->assertEquals(array('text/json'), $r->getAccepts((array)'text/json'));
    }

    /**
     * Test getProduces()
     * 
     */
    #[@test]
    public function produces() {
      $r= new RestRoute('GET', '/resource', $this->handler, $this->target, NULL, array('text/json'));
      $this->assertEquals(array('text/json'), $r->getProduces());
    }

    /**
     * Test getProduces()
     * 
     */
    #[@test]
    public function produces_default() {
      $r= new RestRoute('GET', '/resource', $this->handler, $this->target, NULL, NULL);
      $this->assertEquals(array('text/json'), $r->getProduces((array)'text/json'));
    }

    /**
     * Test getPattern()
     * 
     */
    #[@test]
    public function pattern() {
      $r= new RestRoute('GET', '/resource', $this->handler, $this->target, NULL, NULL);
      $this->assertEquals('#^/resource$#', $r->getPattern());
    }

    /**
     * Test getPattern()
     * 
     */
    #[@test]
    public function pattern_with_placeholder() {
      $r= new RestRoute('GET', '/resource/{id}', $this->handler, $this->target, NULL, NULL);
      $this->assertEquals('#^/resource/(?P<id>[^/]+)$#', $r->getPattern());
    }

    /**
     * Test getPattern()
     * 
     */
    #[@test]
    public function pattern_with_two_placeholders() {
      $r= new RestRoute('GET', '/resource/{id}/{sub}', $this->handler, $this->target, NULL, NULL);
      $this->assertEquals('#^/resource/(?P<id>[^/]+)/(?P<sub>[^/]+)$#', $r->getPattern());
    }

    /**
     * Test toString()
     *
     */
    #[@test]
    public function string_representation() {
      $r= new RestRoute('GET', '/resource/{id}/{sub}', $this->handler, $this->target, NULL, NULL);
      $this->assertEquals(
        'webservices.rest.srv.RestRoute(GET /resource/{id}/{sub} -> void net.xp_framework.unittest.webservices.rest.srv.RestRouteTest::fixtureTarget())', 
        $r->toString()
      );
    }

    /**
     * Test toString()
     *
     */
    #[@test]
    public function string_representation_with_produces() {
      $r= new RestRoute('GET', '/resource/{id}/{sub}', $this->handler, $this->target, NULL, array('text/json'));
      $this->assertEquals(
        'webservices.rest.srv.RestRoute(GET /resource/{id}/{sub} -> void net.xp_framework.unittest.webservices.rest.srv.RestRouteTest::fixtureTarget() @ text/json)', 
        $r->toString()
      );
    }

    /**
     * Test toString()
     *
     */
    #[@test]
    public function string_representation_with_accepts_and_produces() {
      $r= new RestRoute('GET', '/resource/{id}/{sub}', $this->handler, $this->target, array('text/xml'), array('text/json'));
      $this->assertEquals(
        'webservices.rest.srv.RestRoute(GET /resource/{id}/{sub} @ text/xml -> void net.xp_framework.unittest.webservices.rest.srv.RestRouteTest::fixtureTarget() @ text/json)', 
        $r->toString()
      );
    }

    /**
     * Test toString()
     *
     */
    #[@test]
    public function string_representation_with_param() {
      $r= new RestRoute('GET', '/resource/{id}', $this->handler, $this->target, NULL, NULL);
      $r->addParam('id', new RestParamSource('id', ParamReader::forName('path')));
      $this->assertEquals(
        'webservices.rest.srv.RestRoute(GET /resource/{id} -> void net.xp_framework.unittest.webservices.rest.srv.RestRouteTest::fixtureTarget(@$id: path(\'id\')))', 
        $r->toString()
      );
    }

    /**
     * Test toString()
     *
     */
    #[@test]
    public function string_representation_with_params() {
      $r= new RestRoute('GET', '/resource/{id}/{sub}', $this->handler, $this->target, NULL, NULL);
      $r->addParam('id', new RestParamSource('id', ParamReader::forName('path')));
      $r->addParam('sub', new RestParamSource('sub', ParamReader::forName('path')));
      $this->assertEquals(
        'webservices.rest.srv.RestRoute(GET /resource/{id}/{sub} -> void net.xp_framework.unittest.webservices.rest.srv.RestRouteTest::fixtureTarget(@$id: path(\'id\'), @$sub: path(\'sub\')))', 
        $r->toString()
      );
    }

    /**
     * Test appliesTo()
     *
     */
    #[@test]
    public function applies_to_matches_resource_and_subresource() {
      $r= new RestRoute('GET', '/binford/{id}/{name}', NULL, NULL, NULL, NULL);
      $this->assertEquals(
        array(0 => '/binford/6100/chainsaw', 'id' => '6100', 1 => '6100', 'name' => 'chainsaw', 2 => 'chainsaw'),
        $r->appliesTo('/binford/6100/chainsaw')
      );
    }

    /**
     * Test appliesTo()
     *
     */
    #[@test]
    public function applies_to_matches_resource_and_subresource_with_star() {
      $r= new RestRoute('GET', '/binford/{id}/{name}', NULL, NULL, NULL, NULL);
      $this->assertEquals(
        array(0 => '/binford/61/*', 'id' => '61', 1 => '61', 'name' => '*', 2 => '*'),
        $r->appliesTo('/binford/61/*')
      );
    }

    /**
     * Test appliesTo()
     *
     */
    #[@test]
    public function applies_to_matches_resource_and_subresource_with_dot() {
      $r= new RestRoute('GET', '/binford/{id}/{name}', NULL, NULL, NULL, NULL);
      $this->assertEquals(
        array(0 => '/binford/61/.', 'id' => '61', 1 => '61', 'name' => '.', 2 => '.'),
        $r->appliesTo('/binford/61/.')
      );
    }

    /**
     * Test appliesTo()
     *
     */
    #[@test]
    public function applies_to_matches_resource_and_subresource_with_urlencoded() {
      $r= new RestRoute('GET', '/binford/{id}/{name}', NULL, NULL, NULL, NULL);
      $this->assertEquals(
        array(0 => '/binford/61/%40', 'id' => '61', 1 => '61', 'name' => '%40', 2 => '%40'),
        $r->appliesTo('/binford/61/%40')
      );
    }

    /**
     * Test appliesTo()
     *
     */
    #[@test]
    public function applies_to_matches_resource_with_dash() {
      $r= new RestRoute('GET', '/binford/{id}-{name}', NULL, NULL, NULL, NULL);
      $this->assertEquals(
        array(0 => '/binford/610-scissors', 'id' => '610', 1 => '610', 'name' => 'scissors', 2 => 'scissors'),
        $r->appliesTo('/binford/610-scissors')
      );
    }

    /**
     * Test appliesTo()
     *
     */
    #[@test]
    public function applies_to_matches_resource_with_prefix() {
      $r= new RestRoute('GET', '/binford/power{name}', NULL, NULL, NULL, NULL);
      $this->assertEquals(
        array(0 => '/binford/powercar', 'name' => 'car', 1 => 'car'),
        $r->appliesTo('/binford/powercar')
      );
    }

    /**
     * Test appliesTo()
     *
     */
    #[@test]
    public function applies_to_matches_resource_with_postfix() {
      $r= new RestRoute('GET', '/binford/{name}power', NULL, NULL, NULL, NULL);
      $this->assertEquals(
        array(0 => '/binford/morepower', 'name' => 'more', 1 => 'more'),
        $r->appliesTo('/binford/morepower')
      );
    }

    /**
     * Test appliesTo()
     *
     */
    #[@test]
    public function applies_to_does_not_match_empty_path_segment() {
      $r= new RestRoute('GET', '/binford/{id}/{name}', NULL, NULL, NULL, NULL);
      $this->assertEquals(NULL, $r->appliesTo('/binford//chainsaw'));
    }

    /**
     * Test appliesTo()
     *
     */
    #[@test]
    public function applies_to_does_not_match_base() {
      $r= new RestRoute('GET', '/binford/{id}/{name}', NULL, NULL, NULL, NULL);
      $this->assertNull($r->appliesTo('/binford'));
    }

    /**
     * Test appliesTo()
     *
     */
    #[@test]
    public function applies_to_does_not_match_partial() {
      $r= new RestRoute('GET', '/binford/{id}/{name}', NULL, NULL, NULL, NULL);
      $this->assertNull($r->appliesTo('/binford/6100'));
    }

    /**
     * Test appliesTo()
     *
     */
    #[@test]
    public function applies_to_does_not_match_partial_with_trailing_slash() {
      $r= new RestRoute('GET', '/binford/{id}/{name}', NULL, NULL, NULL, NULL);
      $this->assertNull($r->appliesTo('/binford/6100/'));
    }

    /**
     * Test appliesTo()
     *
     */
    #[@test]
    public function applies_to_does_not_match_partial_with_trailing_slashes() {
      $r= new RestRoute('GET', '/binford/{id}/{name}', NULL, NULL, NULL, NULL);
      $this->assertNull($r->appliesTo('/binford/6100//'));
    }
  }
?>
