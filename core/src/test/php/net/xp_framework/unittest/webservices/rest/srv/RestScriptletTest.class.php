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

    #[@test]
    public function can_create_with_custom_router() {
      new RestScriptlet('net.xp_framework.unittest.webservices.rest.srv.fixture', '', '', 'webservices.rest.srv.RestDefaultRouter');
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

    /**
     * Test "cannot route" message
     *
     * @see  https://github.com/xp-framework/xp-framework/issues/258
     */
    #[@test]
    public function cannot_route() {
      $fixture= $this->newFixture();
      $req= new HttpScriptletRequest();
      $req->setURI(new URL('http://localhost/'));
      $res= new HttpScriptletResponse();
      $fixture->doProcess($req, $res);

      $this->assertEquals(404, $res->statusCode);
      $this->assertEquals('{ "message" : "Could not route request to http:\/\/localhost\/" }', $res->getContent());
    }

    /**
     * Test content type for request
     *
     * @see  https://github.com/xp-framework/xp-framework/issues/319
     */
    #[@test, @values(array(array(array('Content-Type' => 'application/json')), array(array())))]
    public function contentTypeOf_request_without_content_length_or_te_is_null($headers) {
      $req= new HttpScriptletRequest();
      $req->setHeaders($headers);
      $this->assertEquals(NULL, $this->newFixture()->contentTypeOf($req));
    }

    /**
     * Test content type for request
     *
     * @see  https://github.com/xp-framework/xp-framework/issues/319
     */
    #[@test]
    public function contentTypeOf_request_with_content_length() {
      $req= new HttpScriptletRequest();
      $req->setHeaders(array('Content-Type' => 'application/json', 'Content-Length' => 6100));
      $this->assertEquals('application/json', $this->newFixture()->contentTypeOf($req));
    }

    /**
     * Test content type for request
     *
     * @see  https://github.com/xp-framework/xp-framework/issues/319
     */
    #[@test]
    public function contentTypeOf_request_with_transfer_encoding() {
      $req= new HttpScriptletRequest();
      $req->setHeaders(array('Content-Type' => 'application/json', 'Transfer-Encoding' => 'chunked'));
      $this->assertEquals('application/json', $this->newFixture()->contentTypeOf($req));
    }

    /**
     * Test default content type for request
     *
     * @see  https://github.com/xp-framework/xp-framework/issues/319
     * @see  http://www.w3.org/Protocols/rfc2616/rfc2616-sec7.html#sec7.2.1
     */
    #[@test, @values(array(
    #  array(array('Content-Length' => 6100)),
    #  array(array('Transfer-Encoding' => 'chunked'))
    #))]
    public function default_contentTypeOf_request_with_body() {
      $req= new HttpScriptletRequest();
      $req->setHeaders(array('Transfer-Encoding' => 'chunked'));
      $this->assertEquals('application/octet-stream', $this->newFixture()->contentTypeOf($req));
    }
  }
?>
