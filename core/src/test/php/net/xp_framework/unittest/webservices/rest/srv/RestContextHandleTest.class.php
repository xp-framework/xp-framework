<?php namespace net\xp_framework\unittest\webservices\rest\srv;

use unittest\TestCase;
use webservices\rest\srv\RestContext;
use webservices\rest\srv\Response;
use webservices\rest\Payload;

/**
 * Test RestContext::handle() 
 *
 * @see  xp://webservices.rest.srv.RestContext
 */
class RestContextHandleTest extends TestCase {

  /**
   * Convenience wrapper around RestContext::handle()
   *
   * @param  lang.Generic $instance
   * @param  var[] $args
   * @return webservices.rest.srv.Response
   */
  protected function handle($instance, $args= array()) {
    return create(new RestContext())->handle($instance, $instance->getClass()->getMethod('fixture'), $args);
  }

  #[@test]
  public function primitive_return() {
    $handler= newinstance('lang.Object', array(), '{
      #[@webmethod(verb= "GET")]
      public function fixture() { return "Hello World"; }
    }');
    $this->assertEquals(
      Response::status(200)->withPayload(new Payload('Hello World')),
      $this->handle($handler)
    );
  }

  #[@test]
  public function response_instance_return() {
    $handler= newinstance('lang.Object', array(), '{
      #[@webmethod(verb= "GET")]
      public function fixture() { return Response::created("/resource/4711"); }
    }');
    $this->assertEquals(
      Response::status(201)->withHeader('Location', '/resource/4711'),
      $this->handle($handler)
    );
  }

  #[@test]
  public function void_return() {
    $handler= newinstance('lang.Object', array(), '{
      /** @return void **/
      #[@webmethod(verb= "GET")]
      public function fixture() { /* Intentionally empty */ }
    }');
    $this->assertEquals(
      Response::status(204),
      $this->handle($handler)
    );
  }

  #[@test]
  public function void_return_ignores_return_value() {
    $handler= newinstance('lang.Object', array(), '{
      /** @return void **/
      #[@webmethod(verb= "GET")]
      public function fixture() { return "Something"; }
    }');
    $this->assertEquals(
      Response::status(204),
      $this->handle($handler)
    );
  }

  #[@test]
  public function null_return() {
    $handler= newinstance('lang.Object', array(), '{
      #[@webmethod(verb= "GET")]
      public function fixture() { return NULL; }
    }');
    $this->assertEquals(
      Response::status(200)->withPayload(null),
      $this->handle($handler)
    );
  }

  #[@test]
  public function no_return() {
    $handler= newinstance('lang.Object', array(), '{
      #[@webmethod(verb= "GET")]
      public function fixture() { return; }
    }');
    $this->assertEquals(
      Response::status(200)->withPayload(null),
      $this->handle($handler)
    );
  }

  #[@test]
  public function handle_string_class_in_parameters_and_return() {
    $handler= newinstance('lang.Object', array(), '{
      #[@webmethod(verb= "GET")]
      public function fixture(String $name) {
        if ($name->startsWith("www.")) {
          return array("name" => $name->substring(4));
        } else {
          return array("name" => $name);
        }
      }
    }');
    $this->assertEquals(
      Response::status(200)->withPayload(new Payload(array('name' => 'example.com'))),
      $this->handle($handler, array(new \lang\types\String('example.com')))
    );
  }

  #[@test, @values(array(
  #  array(400, "lang.IllegalArgumentException"),
  #  array(403, "lang.IllegalAccessException"),
  #  array(404, "lang.ElementNotFoundException"),
  #  array(409, "lang.IllegalStateException"),
  #  array(422, "lang.FormatException"),
  #  array(500, "lang.XPException"),
  #  array(501, "lang.MethodNotImplementedException")
  #))]
  public function raised_exception($status, $class) {
    $handler= newinstance('lang.Object', array($class), '{
      protected $class;

      public function __construct($class) {
        $this->class= $class;
      }

      #[@webmethod(verb= "GET")]
      public function fixture() {
        raise($this->class, "Test", NULL);
      }
    }');
    $this->assertEquals(
      Response::error($status)->withPayload(new Payload(array('message' => 'Test'), array('name' => 'exception'))),
      $this->handle($handler)
    );
  }
}
