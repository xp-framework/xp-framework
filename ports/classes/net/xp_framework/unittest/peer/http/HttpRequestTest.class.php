<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'peer.http.RequestData',
    'peer.http.FormRequestData',
    'peer.http.HttpRequest',
    'peer.http.HttpConstants'
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
      $r->setMethod(HttpConstants::GET);
      $this->assertEquals(
        "GET / HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n\r\n",
        $r->getRequestString()
      );
    }

    /**
     * Test HTTP GET
     *
     */
    #[@test]
    public function portIncluded() {
      $r= new HttpRequest(new URL('http://example.com:8080'));
      $r->setMethod(HttpConstants::GET);
      $this->assertEquals(
        "GET / HTTP/1.1\r\nConnection: close\r\nHost: example.com:8080\r\n\r\n",
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
      $r->setMethod(HttpConstants::GET);
      $this->assertEquals(
        "GET /path/to/images/index.html HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n\r\n",
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
      $r->setMethod(HttpConstants::GET);
      $this->assertEquals(
        "GET / HTTP/1.1\r\nConnection: close\r\nAuthorization: Basic dXNlcjpwYXNz\r\nHost: example.com\r\n\r\n",
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
      $r->setMethod(HttpConstants::GET);
      $this->assertEquals(
        "GET /index.html HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n\r\n",
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
      $r->setMethod(HttpConstants::GET);
      $this->assertEquals(
        "GET /?a=b HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n\r\n",
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
      $r->setMethod(HttpConstants::GET);
      $r->setParameters(array('a' => 'b'));
      $this->assertEquals(
        "GET /?a=b HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n\r\n",
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
      $r->setMethod(HttpConstants::GET);
      $r->setParameters('a=b');
      $this->assertEquals(
        "GET /?a=b HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n\r\n",
        $r->getRequestString()
      );
    }

    /**
     * Test HTTP GET - parameters via setParameters(string)
     *
     */
    #[@test]
    public function getUrlWithStringContainingArrayParams() {
      $r= new HttpRequest(new URL('http://example.com/'));
      $r->setParameters('data[color]=green&data[size]=S');
      $r->setMethod(HttpConstants::GET);
      $this->assertEquals(
        "GET /?data[color]=green&data[size]=S HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n\r\n",
        $r->getRequestString()
      );
    }

    /**
     * Test HTTP GET - parameters via setParameters(RequestData)
     *
     */
    #[@test]
    public function getUrlWithRequestDataParams() {
      $r= new HttpRequest(new URL('http://example.com/'));
      $r->setMethod(HttpConstants::GET);
      $r->setParameters(new RequestData('a=b&c=d'));
      $this->assertEquals(
        "GET /?a=b&c=d HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n\r\n",
        $r->getRequestString()
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function postUrlWithFormRequestDataParams() {
      $r= new HttpRequest(new URL('http://example.com/'));
      $r->setMethod(HttpConstants::POST);
      $r->setParameters(new FormRequestData(
        new FormData('key', 'value'),
        new FormData('xml', '<foo/>', 'text/xml')
      ));

      // HACK: Fetch randomly generated boundary
      $this->assertTrue($r->parameters instanceof FormRequestData);
      $boundary= $r->parameters->getBoundary();

      $this->assertEquals(
        "POST / HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n".
        "Content-Type: multipart/form-data; boundary=".$boundary."\r\nContent-Length: 335\r\n\r\n".
        "--".$boundary."\r\nContent-Disposition: form-data; name=\"key\"\r\n".
        "Content-Type: text/plain; charset=\"iso-8859-1\"\r\n".
        "\r\nvalue\r\n".
        "--".$boundary."\r\n".
        "Content-Disposition: form-data; name=\"xml\"\r\n".
        "Content-Type: text/xml; charset=\"iso-8859-1\"\r\n".
        "\r\n<foo/>\r\n".
        "--".$boundary."--\r\n",
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
      $r->setMethod(HttpConstants::GET);
      $r->setParameters(array('a' => 'b'));
      $this->assertEquals(
        "GET /?a=b HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n\r\n",
        $r->getRequestString()
      );
    }

    /**
     * Test HTTP GET - parameters via URL.
     *
     */
    #[@test]
    public function getUrlWithArrayFromUrlParams() {
      $r= new HttpRequest(new URL('http://example.com/?data[color]=green&data[size]=S'));
      $r->setMethod(HttpConstants::GET);
      $this->assertEquals(
        "GET /?data[color]=green&data[size]=S HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n\r\n",
        $r->getRequestString()
      );
    }


    /**
     * Test HTTP GET - parameters via setParameters(array<string, array>)
     * in combination with parameters passed in the constructor.
     *
     */
    #[@test]
    public function getUrlWithArrayParameter() {
      $r= new HttpRequest(new URL('http://example.com/'));
      $r->setMethod(HttpConstants::GET);
      $r->setParameters(array('params' => array('target' => 'home', 'ssl' => 'true')));
      $this->assertEquals(
        "GET /?params[target]=home&params[ssl]=true HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n\r\n",
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
      $r->setMethod(HttpConstants::GET);
      $r->setParameters('a=b');
      $this->assertEquals(
        "GET /?a=b HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n\r\n",
        $r->getRequestString()
      );
    }

    /**
     * Test HTTP POST
     *
     */
    #[@test]
    public function post() {
      $r= new HttpRequest(new URL('http://example.com/'));
      $r->setMethod(HttpConstants::POST);
      $r->setParameters('a=b&c=d');
      $this->assertEquals(
        "POST / HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n".
        "Content-Length: 7\r\nContent-Type: application/x-www-form-urlencoded\r\n\r\n".
        "a=b&c=d",
        $r->getRequestString()
      );
    }

    /**
     * Test associative arrays are handled correctly in POST requests
     *
     */
    #[@test]
    public function postUrlWithStringContainingArrayParams() {
      $r= new HttpRequest(new URL('http://example.com/'));
      $r->setParameters('data[color]=green&data[size]=S');
      $r->setMethod(HttpConstants::POST);
      $this->assertEquals(
        "POST / HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n".
        "Content-Length: 30\r\nContent-Type: application/x-www-form-urlencoded\r\n\r\n".
        "data[color]=green&data[size]=S",
        $r->getRequestString()
      );
    }

    /**
     * Test associative arrays are handled correctly in POST requests
     *
     */
    #[@test]
    public function postUrlWithArrayParams() {
      $r= new HttpRequest(new URL('http://example.com/'));
      $r->setParameters(array('data' => array('color' => 'green', 'size' => 'S')));
      $r->setMethod(HttpConstants::POST);
      $this->assertEquals(
        "POST / HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n".
        "Content-Length: 30\r\nContent-Type: application/x-www-form-urlencoded\r\n\r\n".
        "data[color]=green&data[size]=S",
        $r->getRequestString()
      );
    }

    /**
     * Test HTTP PUT
     *
     */
    #[@test]
    public function put() {
      $r= new HttpRequest(new URL('http://example.com/'));
      $r->setMethod(HttpConstants::PUT);
      $r->setParameters('a=b&c=d');
      $this->assertEquals(
        "PUT / HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n".
        "Content-Length: 7\r\nContent-Type: application/x-www-form-urlencoded\r\n\r\n".
        "a=b&c=d",
        $r->getRequestString()
      );
    }

    /**
     * Test HTTP TRACE
     *
     */
    #[@test]
    public function trace() {
      $r= new HttpRequest(new URL('http://example.com/'));
      $r->setMethod(HttpConstants::TRACE);
      $r->setParameters('a=b&c=d');
      $this->assertEquals(
        "TRACE / HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n".
        "Content-Length: 7\r\nContent-Type: application/x-www-form-urlencoded\r\n\r\n".
        "a=b&c=d",
        $r->getRequestString()
      );
    }

    /**
     * Test HTTP HEAD
     *
     */
    #[@test]
    public function head() {
      $r= new HttpRequest(new URL('http://example.com/'));
      $r->setMethod(HttpConstants::HEAD);
      $r->setParameters('a=b&c=d');
      $this->assertEquals(
        "HEAD /?a=b&c=d HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n\r\n",
        $r->getRequestString()
      );
    }

    /**
     * Test HTTP DELETE
     *
     */
    #[@test]
    public function delete() {
      $r= new HttpRequest(new URL('http://example.com/'));
      $r->setMethod(HttpConstants::DELETE);
      $r->setParameters('a=b&c=d');
      $this->assertEquals(
        "DELETE /?a=b&c=d HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n\r\n",
        $r->getRequestString()
      );
    }

    /**
     * Test HTTP OPTIONS
     *
     */
    #[@test]
    public function options() {
      $r= new HttpRequest(new URL('http://example.com/'));
      $r->setMethod(HttpConstants::OPTIONS);
      $r->setParameters('a=b&c=d');
      $this->assertEquals(
        "OPTIONS /?a=b&c=d HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n\r\n",
        $r->getRequestString()
      );
    }

    /**
     * Test setHeader() method
     *
     */
    #[@test]
    public function customHeader() {
      $r= new HttpRequest(new URL('http://example.com/'));
      $r->setHeader('X-Binford', 6100);
      $this->assertEquals(
        "GET / HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\nX-Binford: 6100\r\n\r\n",
        $r->getRequestString()
      );
    }

    /**
     * Test addHeaders() method
     *
     */
    #[@test]
    public function customHeaders() {
      $r= new HttpRequest(new URL('http://example.com/'));
      $r->addHeaders(array('X-Binford' => 6100));
      $this->assertEquals(
        "GET / HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\nX-Binford: 6100\r\n\r\n",
        $r->getRequestString()
      );
    }

    /**
     * Test setHeader() method
     *
     */
    #[@test]
    public function duplicateHeader() {
      $r= new HttpRequest(new URL('http://example.com/'));
      $r->setHeader('X-Binford', 6100);
      $r->setHeader('X-Binford', 61000);
      $this->assertEquals(
        "GET / HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\nX-Binford: 61000\r\n\r\n",
        $r->getRequestString()
      );
    }
  }
?>
