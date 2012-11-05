<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'webservices.rest.srv.AbstractRestRouter',
    'webservices.rest.srv.RestContext',
    'scriptlet.HttpScriptletRequest',
    'scriptlet.HttpScriptletResponse'
  );
  
  /**
   * Test default router
   *
   * @see  xp://webservices.rest.srv.RestDefaultRouter
   */
  class AbstractRestRouterTest extends TestCase {
    protected $fixture= NULL;
    protected $target= NULL;

    /**
     * Setup
     * 
     */
    public function setUp() {
      $this->fixture= new AbstractRestRouter();
      $this->fixture->setInputFormats(array('*json'));
      $this->fixture->setOutputFormats(array('text/json'));
      $this->target= $this->getClass()->getMethod('target');
    }

    /**
     * Target method
     *
     */
    #[@webservice]
    public function target() {
      // Intentionally empty
    }

    /**
     * Test allRoutes()
     * 
     */
    #[@test]
    public function routes_initially_empty() {
      $this->assertEquals(array(), $this->fixture->allRoutes());
    }

    /**
     * Test addRoute()
     * 
     */
    #[@test]
    public function add_route_returns_added_route() {
      $route= new RestRoute('GET', '/hello', $this->target, NULL, NULL);
      $this->assertEquals($route, $this->fixture->addRoute($route));
    }

    /**
     * Test addRoute() and allRoutes()
     * 
     */
    #[@test]
    public function add_a_route() {
      $route= new RestRoute('GET', '/hello', $this->target, NULL, NULL);
      $this->fixture->addRoute($route);
      $this->assertEquals(array($route), $this->fixture->allRoutes());
    }

    /**
     * Test addRoute() and allRoutes()
     * 
     */
    #[@test]
    public function add_two_routes() {
      $route1= new RestRoute('GET', '/hello', $this->target, NULL, NULL);
      $route2= new RestRoute('GET', '/world', $this->target, NULL, NULL);
      $this->fixture->addRoute($route1);
      $this->fixture->addRoute($route2);
      $this->assertEquals(array($route1, $route2), $this->fixture->allRoutes());
    }

    /**
     * Test addRoute() and allRoutes()
     * 
     */
    #[@test]
    public function a_post_and_a_get_route() {
      $route1= new RestRoute('GET', '/resource', $this->target, NULL, NULL);
      $route2= new RestRoute('POST', '/resource', $this->target, NULL, NULL);
      $this->fixture->addRoute($route1);
      $this->fixture->addRoute($route2);
      $this->assertEquals(array($route1, $route2), $this->fixture->allRoutes());
    }

    /**
     * Test targetsFor()
     * 
     */
    #[@test]
    public function routes_for_empty_fixture() {
      $this->assertEquals(
        array(), 
        $this->fixture->targetsFor('GET', '/resource', NULL, new Preference('*/*'))
      );
    }

    /**
     * Test targetsFor()
     * 
     */
    #[@test]
    public function get_route_returned() {
      $route1= new RestRoute('GET', '/resource/{id}', $this->target, NULL, NULL);
      $route2= new RestRoute('POST', '/resource', $this->target, NULL, NULL);
      $this->fixture->addRoute($route1);
      $this->fixture->addRoute($route2);
      $this->assertEquals(
        array(array(
          'target'   => $route1->getTarget(),
          'segments' => array(0 => '/resource/1', 'id' => '1', 1 => '1'),
          'input'    => NULL,
          'output'   => 'text/json'
        )), 
        $this->fixture->targetsFor('GET', '/resource/1', NULL, new Preference('*/*'))
      );
    }

    /**
     * Test targetsFor()
     * 
     */
    #[@test]
    public function post_route_returned() {
      $route1= new RestRoute('GET', '/resource/{id}', NULL, NULL, NULL);
      $route2= new RestRoute('POST', '/resource', $this->target, NULL, NULL);
      $this->fixture->addRoute($route1);
      $this->fixture->addRoute($route2);
      $this->assertEquals(
        array(array(
          'target'   => $this->target,
          'segments' => array(0 => '/resource'),
          'input'    => NULL,
          'output'   => 'text/json'
        )), 
        $this->fixture->targetsFor('POST', '/resource', NULL, new Preference('*/*'))
      );
    }

    /**
     * Test targetsFor()
     * 
     */
    #[@test]
    public function route_with_custom_mimetype_preferred_according_to_accept() {
      $route1= new RestRoute('GET', '/resource/{id}', NULL, NULL, NULL);
      $route2= new RestRoute('GET', '/resource/{id}', $this->target, NULL, array('application/vnd.example.v2+json'));
      $this->fixture->addRoute($route1); 
      $this->fixture->addRoute($route2);
      $this->assertEquals(
        array(
          array(
            'target'   => $this->target,
            'segments' => array(0 => '/resource/1', 'id' => '1', 1 => '1'),
            'input'    => NULL,
            'output'   => 'application/vnd.example.v2+json'
          ),
          array(
            'target'   => NULL,
            'segments' => array(0 => '/resource/1', 'id' => '1', 1 => '1'),
            'input'    => NULL,
            'output'   => 'text/json'
          )
        ), 
        $this->fixture->targetsFor('GET', '/resource/1', NULL, new Preference('application/vnd.example.v2+json, text/json'))
      );
    }

    /**
     * Test targetsFor()
     * 
     */
    #[@test]
    public function route_with_custom_mimetype_preferred_according_to_type() {
      $route1= new RestRoute('POST', '/resource', NULL, NULL, NULL);
      $route2= new RestRoute('POST', '/resource', $this->target, array('application/vnd.example.v2+json'), NULL);
      $this->fixture->addRoute($route1); 
      $this->fixture->addRoute($route2);
      $this->assertEquals(
        array(
          array(
            'target'   => $this->target,
            'segments' => array(0 => '/resource'),
            'input'    => 'application/vnd.example.v2+json',
            'output'   => 'text/json'
          ),
          array(
            'target'   => NULL,
            'segments' => array(0 => '/resource'),
            'input'    => 'application/vnd.example.v2+json',
            'output'   => 'text/json'
          )
        ), 
        $this->fixture->targetsFor('POST', '/resource', 'application/vnd.example.v2+json', new Preference('*/*'))
      );
    }

    /**
     * Fixture for handle() tests
     *
     * @return  string
     */
    #[@webmethod]
    public function helloWorld() {
      return 'Hello World';
    }

    /**
     * Test handle()
     * 
     */
    #[@test]
    public function handle_primitive_return() {
      $this->assertEquals(
        Response::status(200)->withPayload('Hello World'),
        $this->fixture->handle($this, $this->getClass()->getMethod('helloWorld'), array(), new RestContext())
      );
    }

    /**
     * Fixture for handle() tests
     *
     * @return  string
     */
    #[@webmethod]
    public function createIt() {
      return Response::created('/resource/4711');
    }

    /**
     * Test handle()
     * 
     */
    #[@test]
    public function handle_response_instance_return() {
      $this->assertEquals(
        Response::status(201)->withHeader('Location', '/resource/4711'),
        $this->fixture->handle($this, $this->getClass()->getMethod('createIt'), array(), new RestContext())
      );
    }

    /**
     * Fixture for handle() tests
     *
     * @return  void
     */
    #[@webmethod]
    public function fireAndForget() {
      // Initially empty
    }

    /**
     * Test handle()
     * 
     */
    #[@test]
    public function handle_void() {
      $this->assertEquals(
        Response::status(204),
        $this->fixture->handle($this, $this->getClass()->getMethod('fireAndForget'), array(), new RestContext())
      );
    }

    /**
     * Fixture for handle() tests
     *
     * @param   lang.Throwable t
     * @throws  lang.Throwable
     * @return  void
     */
    #[@webmethod]
    public function raiseAnError($t) {
      throw $t;
    }

    /**
     * Test handle()
     * 
     */
    #[@test]
    public function handle_exception() {
      $t= new Throwable('Test');
      $this->assertEquals(
        Response::error(400)->withPayload($t),
        $this->fixture->handle($this, $this->getClass()->getMethod('raiseAnError'), array($t), new RestContext())
      );
    }

    /**
     * Test handle()
     * 
     */
    #[@test]
    public function handle_exception_with_mapper() {
      $t= new Throwable('Test');
      $ctx= new RestContext();
      $ctx->addExceptionMapping('lang.Throwable', newinstance('webservices.rest.srv.ExceptionMapper', array(), '{
        public function asResponse($t) {
          return Response::error(500)->withPayload(array("message" => $t->getMessage()));
        }
      }'));
      $this->assertEquals(
        Response::status(500)->withPayload(array('message' => 'Test')),
        $this->fixture->handle($this, $this->getClass()->getMethod('raiseAnError'), array($t), $ctx)
      );
    }


    /**
     * Test handlerInstanceFor() injection
     * 
     */
    #[@test]
    public function constructor_injection() {
      $ctx= new RestContext();
      $class= ClassLoader::defineClass('AbstractRestRouterTest_ConstructorInjection', 'lang.Object', array(), '{
        protected $context;
        #[@inject(type = "webservices.rest.srv.RestContext")]
        public function __construct($context) { $this->context= $context; }
        public function equals($cmp) { return $cmp instanceof self && $this->context->equals($cmp->context); }
      }');
      $this->assertEquals(
        $class->newInstance($ctx),
        $this->fixture->handlerInstanceFor($class, $ctx)
      );
    }
  }
?>
