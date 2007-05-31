<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'peer.http.HttpRequest'
  );

  /**
   * TestCase for HTTP request construction
   *
   * @see      xp://peer.http.HttpRequest
   * @purpose  Unittest
   */
  class HttpRequestTest extends TestCase {
  
    /**
     * Test HTTP GET
     *
     */
    #[@test]
    public function getSimpleUrl() {
      $r= new HttpRequest(new URL('http://example.com'));
      $r->setMethod(HTTP_GET);
      $this->assertEquals(
        "GET / HTTP/1.1\r\nConnection: close\r\nHost: example.com:80\r\n\r\n",
        $r->getRequestString()
      );
    }

    /**
     * Test HTTP GET
     *
     */
    #[@test]
    public function getUrlWithPath() {
      $r= new HttpRequest(new URL('http://example.com/path/to/images/index.html'));
      $r->setMethod(HTTP_GET);
      $this->assertEquals(
        "GET /path/to/images/index.html HTTP/1.1\r\nConnection: close\r\nHost: example.com:80\r\n\r\n",
        $r->getRequestString()
      );
    }

    /**
     * Test HTTP GET
     *
     */
    #[@test]
    public function getSupportsBasicAuth() {
      $r= new HttpRequest(new URL('http://user:pass@example.com/'));
      $r->setMethod(HTTP_GET);
      $this->assertEquals(
        "GET / HTTP/1.1\r\nConnection: close\r\nAuthorization: Basic dXNlcjpwYXNz\r\nHost: example.com:80\r\n\r\n",
        $r->getRequestString()
      );
    }

    /**
     * Test HTTP GET
     *
     */
    #[@test]
    public function getUrlWithFileOnly() {
      $r= new HttpRequest(new URL('http://example.com/index.html'));
      $r->setMethod(HTTP_GET);
      $this->assertEquals(
        "GET /index.html HTTP/1.1\r\nConnection: close\r\nHost: example.com:80\r\n\r\n",
        $r->getRequestString()
      );
    }

    /**
     * Test HTTP GET - parameters via URL
     *
     */
    #[@test]
    public function getUrlWithUrlParams() {
      $r= new HttpRequest(new URL('http://example.com/?a=b'));
      $r->setMethod(HTTP_GET);
      $this->assertEquals(
        "GET /?a=b HTTP/1.1\r\nConnection: close\r\nHost: example.com:80\r\n\r\n",
        $r->getRequestString()
      );
    }

    /**
     * Test HTTP GET - parameters via setParameters(array<string, string>)
     *
     */
    #[@test]
    public function getUrlWithArrayParams() {
      $r= new HttpRequest(new URL('http://example.com/'));
      $r->setMethod(HTTP_GET);
      $r->setParameters(array('a' => 'b'));
      $this->assertEquals(
        "GET /?a=b HTTP/1.1\r\nConnection: close\r\nHost: example.com:80\r\n\r\n",
        $r->getRequestString()
      );
    }

    /**
     * Test HTTP GET - parameters via setParameters(string)
     *
     */
    #[@test]
    public function getUrlWithStringParams() {
      $r= new HttpRequest(new URL('http://example.com/'));
      $r->setMethod(HTTP_GET);
      $r->setParameters('a=b');
      $this->assertEquals(
        "GET /?a=b HTTP/1.1\r\nConnection: close\r\nHost: example.com:80\r\n\r\n",
        $r->getRequestString()
      );
    }

    /**
     * Test HTTP GET - parameters via setParameters(array<string, string>)
     * in combination with parameters passed in the constructor.
     *
     */
    #[@test]
    public function getUrlWithArrayAndUrlParams() {
      $r= new HttpRequest(new URL('http://example.com/?a=b'));
      $r->setMethod(HTTP_GET);
      $r->setParameters(array('a' => 'b'));
      $this->assertEquals(
        "GET /?a=b HTTP/1.1\r\nConnection: close\r\nHost: example.com:80\r\n\r\n",
        $r->getRequestString()
      );
    }


    /**
     * Test HTTP GET - parameters via setParameters(string)
     * in combination with parameters passed in the constructor.
     *
     */
    #[@test]
    public function getUrlWithStringAndUrlParams() {
      $r= new HttpRequest(new URL('http://example.com/?a=b'));
      $r->setMethod(HTTP_GET);
      $r->setParameters('a=b');
      $this->assertEquals(
        "GET /?a=b HTTP/1.1\r\nConnection: close\r\nHost: example.com:80\r\n\r\n",
        $r->getRequestString()
      );
    }
  }
?>
