<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_framework.unittest.webservices.rest.srv';

  uses(
    'unittest.TestCase',
    'webservices.rest.srv.Response'
  );
  
  /**
   * Test response class
   *
   * @see  xp://webservices.rest.srv.Response
   */
  class net·xp_framework·unittest·webservices·rest·srv·ResponseTest extends TestCase {

    /**
     * Test constructor
     * 
     */
    #[@test]
    public function create() {
      $this->assertEquals(NULL, create(new Response())->status);
    }

    /**
     * Test constructor
     * 
     */
    #[@test]
    public function create_with_status() {
      $this->assertEquals(200, create(new Response(200))->status);
    }

    /**
     * Test payload is initially null
     * 
     */
    #[@test]
    public function payload_initially_null() {
      $this->assertNull(create(new Response())->payload);
    }

    /**
     * Test headers are initially empty
     * 
     */
    #[@test]
    public function headers_initially_empty() {
      $this->assertEquals(array(), create(new Response())->headers);
    }

    /**
     * Test ok() method
     * 
     */
    #[@test]
    public function ok() {
      $r= Response::ok();
      $this->assertEquals(200, $r->status);
    }

    /**
     * Test created() method
     * 
     */
    #[@test]
    public function created() {
      $r= Response::created();
      $this->assertEquals(201, $r->status);
      $this->assertEquals(array(), $r->headers);
    }

    /**
     * Test created() method
     * 
     */
    #[@test]
    public function created_with_location() {
      $location= 'http://example.com/resource/4711';
      $r= Response::created($location);
      $this->assertEquals(201, $r->status);
      $this->assertEquals(array('Location' => $location), $r->headers);
    }

    /**
     * Test noContent() method
     * 
     */
    #[@test]
    public function no_content() {
      $r= Response::noContent();
      $this->assertEquals(204, $r->status);
    }

    /**
     * Test see() method
     * 
     */
    #[@test]
    public function see() {
      $location= 'http://example.com/resource/4711';
      $r= Response::see($location);
      $this->assertEquals(302, $r->status);
    }

    /**
     * Test notModified() method
     * 
     */
    #[@test]
    public function not_modified() {
      $r= Response::notModified();
      $this->assertEquals(304, $r->status);
    }

    /**
     * Test notFound() method
     * 
     */
    #[@test]
    public function not_found() {
      $r= Response::notFound();
      $this->assertEquals(404, $r->status);
    }

    /**
     * Test notAcceptable() method
     * 
     */
    #[@test]
    public function not_acceptable() {
      $r= Response::notAcceptable();
      $this->assertEquals(406, $r->status);
    }

    /**
     * Test error() method
     * 
     */
    #[@test]
    public function error() {
      $r= Response::error();
      $this->assertEquals(500, $r->status);
    }

    /**
     * Test error() method
     * 
     */
    #[@test]
    public function error_503() {
      $r= Response::error(503);
      $this->assertEquals(503, $r->status);
    }

    /**
     * Test status() method
     * 
     */
    #[@test]
    public function status_402() {
      $r= Response::status(402);
      $this->assertEquals(402, $r->status);
    }

    /**
     * Test withHeader() method
     * 
     */
    #[@test]
    public function with_extra_header() {
      $r= create(new Response())->withHeader('X-Exception', 'SQL');
      $this->assertEquals(array('X-Exception' => 'SQL'), $r->headers);
    }

    /**
     * Test withPayload() method
     * 
     */
    #[@test]
    public function with_payload() {
      $data= array('name' => 'example');
      $r= create(new Response())->withPayload($data);
      $this->assertEquals(new Payload($data), $r->payload);
    }

    /**
     * Test withPayload() method
     * 
     */
    #[@test]
    public function with_payload_instance() {
      $data= array('name' => 'example');
      $r= create(new Response())->withPayload(new Payload($data));
      $this->assertEquals(new Payload($data), $r->payload);
    }

    /**
     * Test equals() method
     * 
     */
    #[@test]
    public function equals_identical() {
      $r= Response::status(200);
      $this->assertEquals($r, $r);
    }

    /**
     * Test equals() method
     * 
     */
    #[@test]
    public function equals_same() {
      $this->assertEquals(Response::status(200), Response::status(200));
    }

    /**
     * Test equals() method
     * 
     */
    #[@test]
    public function equals_with_headers() {
      $this->assertEquals(
        Response::status(200)->withHeader('ETag', '4711'), 
        Response::status(200)->withHeader('ETag', '4711')
      );
    }

    /**
     * Test equals() method
     * 
     */
    #[@test]
    public function equals_different_status() {
      $this->assertNotEquals(Response::status(200), Response::status(201));
    }

    /**
     * Test equals() method
     * 
     */
    #[@test]
    public function equals_different_header_values() {
      $this->assertNotEquals(
        Response::status(200)->withHeader('ETag', '4711'), 
        Response::status(200)->withHeader('ETag', '4712')
      );
    }

    /**
     * Test equals() method
     * 
     */
    #[@test]
    public function equals_different_header_keys() {
      $this->assertNotEquals(
        Response::status(200)->withHeader('ETag', '4711'), 
        Response::status(200)->withHeader('X-Any-Number', '4711')
      );
    }

    /**
     * Test equals() method
     * 
     */
    #[@test]
    public function equals_different_header_sizes() {
      $this->assertNotEquals(
        Response::status(200), 
        Response::status(200)->withHeader('X-Any-Number', '4711')
      );
    }

    /**
     * Test equals() method
     * 
     */
    #[@test]
    public function equals_same_primitive_payloads() {
      $this->assertEquals(
        Response::status(200)->withPayload('4711'), 
        Response::status(200)->withPayload('4711')
      );
    }

    /**
     * Test equals() method
     * 
     */
    #[@test]
    public function equals_different_primitive_payloads() {
      $this->assertNotEquals(
        Response::status(200)->withPayload('4711'), 
        Response::status(200)->withPayload('4712')
      );
    }

    /**
     * Test equals() method
     * 
     */
    #[@test]
    public function equals_same_array_payloads() {
      $this->assertEquals(
        Response::status(200)->withPayload(array('4711', 4712, NULL)), 
        Response::status(200)->withPayload(array('4711', 4712, NULL))
      );
    }

    /**
     * Test equals() method
     * 
     */
    #[@test]
    public function equals_different_array_payloads() {
      $this->assertNotEquals(
        Response::status(200)->withPayload(array('4711', 4712, NULL)), 
        Response::status(200)->withPayload(array('4711', 4713, NULL))
      );
    }

    /**
     * Test equals() method
     * 
     */
    #[@test]
    public function equals_identical_object_payloads() {
      $this->assertEquals(
        Response::status(200)->withPayload($this), 
        Response::status(200)->withPayload($this)
      );
    }

    /**
     * Test equals() method
     * 
     */
    #[@test]
    public function equals_same_object_payloads() {
      $class= ClassLoader::defineClass('ResponseTest_SameObjectFixture', 'lang.Object', array(), '{
        public function equals($cmp) { return TRUE; }
      }');
      $this->assertEquals(
        Response::status(200)->withPayload($class->newInstance()), 
        Response::status(200)->withPayload($class->newInstance())
      );
    }

    /**
     * Test equals() method
     * 
     */
    #[@test]
    public function equals_different_object_payloads() {
      $this->assertNotEquals(
        Response::status(200)->withPayload($this), 
        Response::status(200)->withPayload(new Object())
      );
    }

    /**
     * Test equals() method
     * 
     */
    #[@test]
    public function equals_object_and_null_payloads() {
      $this->assertNotEquals(
        Response::status(200)->withPayload($this), 
        Response::status(200)->withPayload(NULL)
      );
    }

    /**
     * Test equals() method
     * 
     */
    #[@test]
    public function equals_null() {
      $this->assertEquals(
        Response::status(200)->withPayload(NULL), 
        Response::status(200)->withPayload(NULL)
      );
    }

    /**
     * Test cookies
     * 
     */
    #[@test]
    public function without_cookies() {
      $this->assertEquals(
        array(),
        Response::status(200)->cookies 
      );
    }

    /**
     * Test cookies
     * 
     */
    #[@test]
    public function with_one_cookie() {
      $user= new Cookie('user', 'Test');
      $this->assertEquals(
        array($user),
        Response::status(200)->withCookie($user)->cookies 
      );
    }

    /**
     * Test cookies
     * 
     */
    #[@test]
    public function with_two_cookies() {
      $user= new Cookie('user', 'Test');
      $lang= new Cookie('language', 'de');
      $this->assertEquals(
        array($user, $lang),
        Response::status(200)->withCookie($user)->withCookie($lang)->cookies 
      );
    }
  }
?>
