<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'scriptlet.HttpScriptletRequest',
    'scriptlet.HttpScriptletResponse',
    'webservices.rest.srv.RestContext'
  );
  
  /**
   * Test default router
   *
   * @see  xp://webservices.rest.srv.RestDefaultRouter
   */
  class RestContextTest extends TestCase {
    protected $fixture= NULL;
    protected static $package= NULL;

    /**
     * Sets up fixture package
     *
     */
    #[@beforeClass]
    public static function fixturePackage() {
      self::$package= Package::forName('net.xp_framework.unittest.webservices.rest.srv.fixture');
    }

    /**
     * Setup
     * 
     */
    public function setUp() {
      $this->fixture= new RestContext();
    }

    /**
     * Returns a method from given fixture class
     *
     * @param  string class
     * @param  string method
     * @return lang.reflect.Method
     */
    protected function fixtureMethod($class, $method) {
      return self::$package->loadClass($class)->getMethod($method);
    }

    /**
     * Test marshalling
     * 
     */
    #[@test]
    public function marshal_this_generically() {
      $this->assertEquals(
        new Payload($this),
        $this->fixture->marshal(new Payload($this))
      );
    }

    /**
     * Test marshalling
     * 
     */
    #[@test]
    public function marshal_this_with_typemarshaller() {
      $this->fixture->addMarshaller('unittest.TestCase', newinstance('webservices.rest.TypeMarshaller', array(), '{
        public function marshal($t) {
          return $t->getClassName()."::".$t->getName();
        }
        public function unmarshal($name) {
          // Not needed
        }
      }'));
      $this->assertEquals(
        new Payload($this->getClassName().'::'.$this->getName()),
        $this->fixture->marshal(new Payload($this))
      );
    }

    /**
     * Test marshalling
     * 
     */
    #[@test]
    public function unmarshal_this_with_typemarshaller() {
      $this->fixture->addMarshaller('unittest.TestCase', newinstance('webservices.rest.TypeMarshaller', array(), '{
        public function marshal($t) {
          // Not needed
        }
        public function unmarshal($name) {
          sscanf($name, "%[^:]::%s", $class, $test);
          return XPClass::forName($class)->newInstance($test);
        }
      }'));
      $this->assertEquals(
        $this,
        $this->fixture->unmarshal($this->getClass(), $this->getClassName().'::'.$this->getName())
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
        Response::status(200)->withPayload(new Payload('Hello World')),
        $this->fixture->handle($this, $this->getClass()->getMethod('helloWorld'), array())
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
        $this->fixture->handle($this, $this->getClass()->getMethod('createIt'), array())
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
        $this->fixture->handle($this, $this->getClass()->getMethod('fireAndForget'), array())
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
        Response::error(500)->withPayload(new Payload(array('message' => 'Test'), array('name' => 'exception'))),
        $this->fixture->handle($this, $this->getClass()->getMethod('raiseAnError'), array($t))
      );
    }

    /**
     * Test handle()
     * 
     */
    #[@test]
    public function handle_illegal_argument_exception() {
      $t= new IllegalArgumentException('Test');
      $this->assertEquals(
        Response::error(400)->withPayload(new Payload(array('message' => 'Test'), array('name' => 'exception'))),
        $this->fixture->handle($this, $this->getClass()->getMethod('raiseAnError'), array($t))
      );
    }

    /**
     * Test handle()
     * 
     */
    #[@test]
    public function handle_illegal_state_exception() {
      $t= new IllegalStateException('Test');
      $this->assertEquals(
        Response::error(409)->withPayload(new Payload(array('message' => 'Test'), array('name' => 'exception'))),
        $this->fixture->handle($this, $this->getClass()->getMethod('raiseAnError'), array($t))
      );
    }

    /**
     * Test handle()
     * 
     */
    #[@test]
    public function handle_illegal_access_exception() {
      $t= new IllegalAccessException('Test');
      $this->assertEquals(
        Response::error(403)->withPayload(new Payload(array('message' => 'Test'), array('name' => 'exception'))),
        $this->fixture->handle($this, $this->getClass()->getMethod('raiseAnError'), array($t))
      );
    }

    /**
     * Test handle()
     * 
     */
    #[@test]
    public function handle_element_not_found_exception() {
      $t= new ElementNotFoundException('Test');
      $this->assertEquals(
        Response::error(404)->withPayload(new Payload(array('message' => 'Test'), array('name' => 'exception'))),
        $this->fixture->handle($this, $this->getClass()->getMethod('raiseAnError'), array($t))
      );
    }

    /**
     * Test handle()
     * 
     */
    #[@test]
    public function handle_method_not_implemented_exception() {
      $t= new MethodNotImplementedException('Test', $this->name);
      $this->assertEquals(
        Response::error(501)->withPayload(new Payload(array('message' => 'Test'), array('name' => 'exception'))),
        $this->fixture->handle($this, $this->getClass()->getMethod('raiseAnError'), array($t))
      );
    }

    /**
     * Test handle()
     * 
     */
    #[@test]
    public function handle_format_exception() {
      $t= new FormatException('Test');
      $this->assertEquals(
        Response::error(422)->withPayload(new Payload(array('message' => 'Test'), array('name' => 'exception'))),
        $this->fixture->handle($this, $this->getClass()->getMethod('raiseAnError'), array($t))
      );
    }

    /**
     * Test handle()
     * 
     */
    #[@test]
    public function handle_xmlfactory_annotated_method() {
      $handler= newinstance('lang.Object', array(), '{
        /** @return var **/
        #[@webmethod, @xmlfactory(element = "book")]
        public function getBook() {
          return array("isbn" => "978-3-16-148410-0", "author" => "Test");
        }
      }');
      $this->assertEquals(
        Response::error(200)->withPayload(new Payload(array('isbn' => '978-3-16-148410-0', 'author' => 'Test'), array('name' => 'book'))),
        $this->fixture->handle($handler, $handler->getClass()->getMethod('getBook'), array())
      );
    }

    /**
     * Test handle()
     * 
     */
    #[@test]
    public function handle_xmlfactory_annotated_class() {
      $handler= self::$package->loadClass('GreetingHandler')->newInstance();
      $this->assertEquals(
        Response::error(200)->withPayload(new Payload('Hello Test', array('name' => 'greeting'))),
        $this->fixture->handle($handler, $handler->getClass()->getMethod('greet'), array('Test'))
      );
    }

    /**
     * Test handle()
     * 
     */
    #[@test]
    public function handle_exception_with_mapper() {
      $t= new Throwable('Test');
      $this->fixture->addExceptionMapping('lang.Throwable', newinstance('webservices.rest.srv.ExceptionMapper', array(), '{
        public function asResponse($t) {
          return Response::error(500)->withPayload(array("message" => $t->getMessage()));
        }
      }'));
      $this->assertEquals(
        Response::status(500)->withPayload(new Payload(array('message' => 'Test'), array('name' => 'exception'))),
        $this->fixture->handle($this, $this->getClass()->getMethod('raiseAnError'), array($t))
      );
    }

    /**
     * Test handlerInstanceFor() injection
     * 
     */
    #[@test]
    public function constructor_injection() {
      $class= ClassLoader::defineClass('AbstractRestRouterTest_ConstructorInjection', 'lang.Object', array(), '{
        protected $context;
        #[@inject(type = "webservices.rest.srv.RestContext")]
        public function __construct($context) { $this->context= $context; }
        public function equals($cmp) { return $cmp instanceof self && $this->context->equals($cmp->context); }
      }');
      $this->assertEquals(
        $class->newInstance($this->fixture),
        $this->fixture->handlerInstanceFor($class)
      );
    }

    /**
     * Test handlerInstanceFor() injection
     * 
     */
    #[@test]
    public function setter_injection() {
      $prop= new Properties('service.ini');
      PropertyManager::getInstance()->register('service', $prop);
      $class= ClassLoader::defineClass('AbstractRestRouterTest_SetterInjection', 'lang.Object', array(), '{
        public $prop;
        #[@inject(type = "util.Properties", name = "service")]
        public function setServiceConfig($prop) { $this->prop= $prop; }
      }');
      $this->assertEquals(
        $prop,
        $this->fixture->handlerInstanceFor($class)->prop
      );
    }

    /**
     * Test handlerInstanceFor() injection
     * 
     */
    #[@test, @expect(class = 'lang.reflect.TargetInvocationException', withMessage= '/InjectionError::setTrace/')]
    public function injection_error() {
      $class= ClassLoader::defineClass('AbstractRestRouterTest_InjectionError', 'lang.Object', array(), '{
        #[@inject(type = "util.log.LogCategory")]
        public function setTrace($cat) { throw new IllegalStateException("Test"); }
      }');
      $this->fixture->handlerInstanceFor($class);
    }

    /**
     * Test handlerInstanceFor() injection
     * 
     */
    #[@test, @expect(class = 'lang.reflect.TargetInvocationException', withMessage= '/InstantiationError::<init>/')]
    public function instantiation_error() {
      $class= ClassLoader::defineClass('AbstractRestRouterTest_InstantiationError', 'lang.Object', array(), '{
        public function __construct() { throw new IllegalStateException("Test"); }
      }');
      $this->fixture->handlerInstanceFor($class);
    }


    /**
     * Creates a new request with a given parameter map
     *
     * @param  [:string] params
     * @return scriptlet.Request
     */
    protected function newRequest($params= array(), $payload= NULL, $headers= array()) {
      $r= newinstance('scriptlet.HttpScriptletRequest', array($payload), '{
        public function __construct($payload) {
          if (NULL !== $payload) {
            $this->inputStream= new MemoryInputStream($payload);
          }
        }
      }');
      foreach ($params as $name => $value) {
        if ('Cookie' === $name) {
        } else {
          $r->setParam($name, $value);
        }
      }
      if (isset($headers['Cookie'])) {
        foreach (explode(';', $headers['Cookie']) as $cookie) {
          sscanf(trim($cookie), '%[^=]=%s', $name, $value);
          $_COOKIE[$name]= $value;
        }
        unset($headers['Cookie']);
      }
      $r->setHeaders($headers);
      return $r;
    }

    /**
     * Test argumentsFor()
     * 
     */
    #[@test]
    public function greet_implicit_segment_and_param() {
      $route= array(
        'target'   => $this->fixtureMethod('ImplicitGreetingHandler', 'greet'),
        'params'   => array(),
        'segments' => array(0 => '/implicit/greet/test', 'name' => 'test', 1 => 'test'),
        'input'    => NULL,
        'output'   => 'text/json'
      );
      $this->assertEquals(
        array('test', 'Servus'),
        $this->fixture->argumentsFor($route, $this->newRequest(array('greeting' => 'Servus')), RestFormat::$FORM)
      );
    }

    /**
     * Test argumentsFor()
     * 
     */
    #[@test]
    public function greet_implicit_segment_and_missing_param() {
      $route= array(
        'target'   => $this->fixtureMethod('ImplicitGreetingHandler', 'greet'),
        'params'   => array(),
        'segments' => array(0 => '/implicit/greet/test', 'name' => 'test', 1 => 'test'),
        'input'    => NULL,
        'output'   => 'text/json'
      );
      $this->assertEquals(
        array('test', 'Hello'),
        $this->fixture->argumentsFor($route, $this->newRequest(), RestFormat::$FORM)
      );
    }

    /**
     * Test argumentsFor()
     * 
     */
    #[@test]
    public function greet_implicit_payload() {
      $route= array(
        'target'   => $this->fixtureMethod('ImplicitGreetingHandler', 'greet_posted'),
        'params'   => array(),
        'segments' => array(0 => '/greet'),
        'input'    => 'application/json',
        'output'   => 'text/json'
      );
      $this->assertEquals(
        array('Hello World'),
        $this->fixture->argumentsFor($route, $this->newRequest(array(), '"Hello World"'), RestFormat::$JSON)
      );
    }

    /**
     * Test argumentsFor()
     * 
     */
    #[@test]
    public function greet_intl() {
      $route= array(
        'target'   => $this->fixtureMethod('GreetingHandler', 'greet_intl'),
        'params'   => array('language' => new RestParamSource('Accept-Language', ParamReader::$HEADER)),
        'segments' => array(0 => '/intl/greet/test', 'name' => 'test', 1 => 'test'),
        'input'    => NULL,
        'output'   => 'text/json'
      );
      $this->assertEquals(
        array('test', new Preference('de')),
        $this->fixture->argumentsFor($route, $this->newRequest(array(), NULL, array('Accept-Language' => 'de')), RestFormat::$FORM)
      );
    }

    /**
     * Test argumentsFor()
     * 
     */
    #[@test]
    public function greet_user() {
      $route= array(
        'target'   => $this->fixtureMethod('GreetingHandler', 'greet_user'),
        'params'   => array('name' => new RestParamSource('user', ParamReader::$COOKIE)),
        'segments' => array(0 => '/user/greet'),
        'input'    => NULL,
        'output'   => 'text/json'
      );
      $this->assertEquals(
        array('Test'),
        $this->fixture->argumentsFor($route, $this->newRequest(array(), NULL, array('Cookie' => 'user=Test')), RestFormat::$FORM)
      );
    }


    /**
     * Assertion helper
     * 
     * @param  int $status Expected status
     * @param  string[] $headers Expected headers
     * @param  string $content Expected content
     * @param  [:var] $route Route
     * @param  scriptlet.Request $request HTTP request
     * @throws unittest.AssertionFailedError
     */
    protected function assertProcess($status, $headers, $content, $route, $request) {
      $response= new HttpScriptletResponse();
      $this->fixture->process($route, $request, $response);
      $this->assertEquals($status, $response->statusCode, 'Status code');
      $this->assertEquals($headers, $response->headers, 'Headers');
      $this->assertEquals($content, $response->content, 'Content');
    }

    /**
     * Test process()
     * 
     */
    #[@test]
    public function process_greet_successfully() {
      $route= array(
        'target'   => $this->fixtureMethod('GreetingHandler', 'greet'),
        'params'   => array('name' => new RestParamSource('name', ParamReader::$PATH)),
        'segments' => array(0 => '/greet/Test', 'name' => 'Test', 1 => 'Test'),
        'input'    => NULL,
        'output'   => 'text/json'
      );
      $this->assertProcess(
        200, array('Content-Type: text/json'), '"Hello Test"',
        $route, $this->newRequest()
      );
    }

    /**
     * Test process()
     * 
     */
    #[@test]
    public function process_greet_with_missing_parameter() {
      $route= array(
        'target'   => $this->fixtureMethod('GreetingHandler', 'greet'),
        'params'   => array('name' => new RestParamSource('name', ParamReader::$PATH)),
        'segments' => array(0 => '/greet/'),
        'input'    => NULL,
        'output'   => 'text/json'
      );
      $this->assertProcess(
        400, array('Content-Type: text/json'), '{ "message" : "Parameter \"name\" required but found in path(\'name\')" }',
        $route, $this->newRequest()
      );
    }

    /**
     * Test process()
     * 
     */
    #[@test]
    public function process_greet_and_go() {
      $route= array(
        'target'   => $this->fixtureMethod('GreetingHandler', 'greet_and_go'),
        'params'   => array('name' => new RestParamSource('name', ParamReader::$PATH)), 
        'segments' => array(0 => '/greet/and/go/test', 'name' => 'test', 1 => 'test'),
        'input'    => NULL,
        'output'   => 'text/json'
      );
      $this->assertProcess(
        204, array(), NULL,
        $route, $this->newRequest()
      );
    }
  }
?>
