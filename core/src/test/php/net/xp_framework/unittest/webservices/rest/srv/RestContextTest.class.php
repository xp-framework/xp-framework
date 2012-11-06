<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'webservices.rest.srv.RestContext'
  );
  
  /**
   * Test default router
   *
   * @see  xp://webservices.rest.srv.RestDefaultRouter
   */
  class RestContextTest extends TestCase {
    protected $fixture= NULL;

    /**
     * Setup
     * 
     */
    public function setUp() {
      $this->fixture= new RestContext();
    }

    /**
     * Test marshalling
     * 
     */
    #[@test]
    public function marshal_this_generically() {
      $this->assertEquals(
        $this,
        $this->fixture->marshal($this)
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
        $this->getClassName().'::'.$this->getName(),
        $this->fixture->marshal($this)
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
        Response::status(200)->withPayload('Hello World'),
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
        Response::error(400)->withPayload($t),
        $this->fixture->handle($this, $this->getClass()->getMethod('raiseAnError'), array($t))
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
        Response::status(500)->withPayload(array('message' => 'Test')),
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
  }
?>
