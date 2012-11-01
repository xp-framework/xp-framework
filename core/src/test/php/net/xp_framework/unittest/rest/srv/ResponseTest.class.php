<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_framework.unittest.rest.srv';

  uses(
    'unittest.TestCase',
    'webservices.rest.srv.Response'
  );
  
  /**
   * Test response class
   *
   * @see  xp://webservices.rest.srv.Response
   */
  class net·xp_framework·unittest·rest·srv·ResponseTest extends TestCase {

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
      $this->assertEquals($data, $r->payload);
    }
  }
?>
