<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'lang.IllegalStateException',
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
     * with content
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function getUrlWithStringParamsWithContent() {
      $r= new HttpRequest(new URL('http://example.com/'));
      $r->setMethod(HttpConstants::GET);
      $r->setParameters('a=b');
      $r->setBody('aloha content');
      $r->getRequestString();
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
      $r->setParameters(new FormRequestData(array(
        new FormData('key', 'value'),
        new FormData('xml', '<foo/>', 'text/xml')
      )));

      // Fetch randomly generated boundary
      $this->assertTrue($r->parameters instanceof FormRequestData);
      $boundary= $r->parameters->getBoundary();

      $this->assertEquals(
        "POST / HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n".
        "Content-Type: multipart/form-data; boundary=".$boundary."\r\nContent-Length: 265\r\n\r\n".
        "--".$boundary."\r\nContent-Disposition: form-data; name=\"key\"\r\n".
        "\r\nvalue\r\n".
        "--".$boundary."\r\n".
        "Content-Disposition: form-data; name=\"xml\"\r\n".
        "Content-Type: text/xml\r\n".
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
     * Test HTTP GET - parameters via setParameters(array<string, array>)
     * in combination with parameters passed in the constructor.
     *
     */
    #[@test]
    public function getUrlWithArrayXDepthParameter() {
      $r= new HttpRequest(new URL('http://example.com/'));
      $r->setMethod(HttpConstants::GET);
      $r->setParameters(
        array(
          'params'    => array(
            'target'    => 'home', 
            'ssl'       => 'true'
          ),
          'paramsExt' => array(
            'location'  => array(
              'work'      =>  'Brauerstr',
              'home'      =>  'somewhere else'
            )
          )
        )
      );
      $this->assertEquals(
        "GET /?params[target]=home&params[ssl]=true&paramsExt[location][work]=Brauerstr&paramsExt[location][home]=somewhere+else HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n\r\n",
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
     * Test HTTP POST
     * with content
     */
    #[@test]
    public function postWithContent() {
      $r= new HttpRequest(new URL('http://example.com/'));
      $r->setMethod(HttpConstants::POST);
      $r->setParameters('a=b&c=d');
      $r->setBody('aloha content');
      $this->assertEquals(
        "POST /?a=b&c=d HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n".
        "Content-Length: 13\r\nContent-Type: text/plain\r\n\r\n".
        "aloha content",
        $r->getRequestString()
      );
    }

    /**
     * Test HTTP POST
     * with content and url params
     */
    #[@test]
    public function postWithContentAndUrlParam() {
      $r= new HttpRequest(new URL('http://example.com/?ar=ber'));
      $r->setMethod(HttpConstants::POST);
      $r->setParameters('a=b&c=d');
      $r->setBody('aloha content');
      $this->assertEquals(
        "POST /?ar=ber&a=b&c=d HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n".
        "Content-Length: 13\r\nContent-Type: text/plain\r\n\r\n".
        "aloha content",
        $r->getRequestString()
      );
    }

    /**
     * Test HTTP POST
     * with array content and url params
     */
    #[@test]
    public function postWithArrayContentAndUrlParam() {
      $r= new HttpRequest(new URL('http://example.com/?ar=ber'));
      $r->setMethod(HttpConstants::POST);
      $r->setParameters('a=b&c=d');
      $r->setBody(array('aloha content'));
      $this->assertEquals(
        "POST /?ar=ber&a=b&c=d HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n".
        "Content-Length: 15\r\nContent-Type: application/x-www-form-urlencoded\r\n\r\n".
        "0=aloha+content",
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
     * Test HTTP PUT
     * with content
     */
    #[@test]
    public function putWithContent() {
      $r= new HttpRequest(new URL('http://example.com/'));
      $r->setMethod(HttpConstants::PUT);
      $r->setParameters('a=b&c=d');
      $r->setBody('aloha content');
      $this->assertEquals(
        "PUT /?a=b&c=d HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n".
        "Content-Length: 13\r\nContent-Type: text/plain\r\n\r\n".
        "aloha content",
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
     * Test HTTP TRACE
     * with content
     */
    #[@test]
    public function traceWithContent() {
      $r= new HttpRequest(new URL('http://example.com/'));
      $r->setMethod(HttpConstants::TRACE);
      $r->setParameters('a=b&c=d');
      $r->setBody('aloha content');
      $this->assertEquals(
        "TRACE /?a=b&c=d HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n".
        "Content-Length: 13\r\nContent-Type: text/plain\r\n\r\n".
        "aloha content",
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
     * Test HTTP HEAD
     * with content
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function headWithContent() {
      $r= new HttpRequest(new URL('http://example.com/'));
      $r->setMethod(HttpConstants::HEAD);
      $r->setParameters('a=b&c=d');
      $r->setBody('aloha content');
      $r->getRequestString();
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
     * Test HTTP DELETE
     * with content
     */
    #[@test]
    public function deleteWithContent() {
      $r= new HttpRequest(new URL('http://example.com/'));
      $r->setMethod(HttpConstants::DELETE);
      $r->setParameters('a=b&c=d');
      $r->setBody('aloha content');
      $this->assertEquals(
        "DELETE /?a=b&c=d HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n".
        "Content-Length: 13\r\nContent-Type: text/plain\r\n\r\n".
        "aloha content",
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
     * Test HTTP OPTIONS
     * with content
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function optionsWithContent() {
      $r= new HttpRequest(new URL('http://example.com/'));
      $r->setMethod(HttpConstants::OPTIONS);
      $r->setParameters('a=b&c=d');
      $r->setBody('aloha content');
      $r->getRequestString();
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
     * Test setHeader() method
     *
     */
    #[@test]
    public function customHeaderObject() {
      $r= new HttpRequest(new URL('http://example.com/'));
      $r->setHeader('X-Binford', new Header('X-Binford', 6100));
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
    public function customHeaderList() {
      $r= new HttpRequest(new URL('http://example.com/'));
      $r->setHeader('X-Binford', array(6100, 'More Power'));
      $this->assertEquals(
        "GET / HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\nX-Binford: 6100\r\nX-Binford: More Power\r\n\r\n",
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
     * Test addHeaders() method
     *
     */
    #[@test]
    public function customHeadersObject() {
      $r= new HttpRequest(new URL('http://example.com/'));
      $r->addHeaders(array('X-Binford' => new Header('X-Binford', 6100)));
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
    public function customHeadersObjectList() {
      $r= new HttpRequest(new URL('http://example.com/'));
      $r->addHeaders(array(new Header('X-Binford', 6100)));
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
    public function customHeadersList() {
      $r= new HttpRequest(new URL('http://example.com/'));
      $r->addHeaders(array('X-Binford' => array(6100, 'Even more power')));
      $this->assertEquals(
        "GET / HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\nX-Binford: 6100\r\nX-Binford: Even more power\r\n\r\n",
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

    /**
     * Test getHeaderString()
     *
     */
    #[@test]
    public function headerString() {
      $r= new HttpRequest(new URL('http://example.com/'));
      $r->setMethod(HttpConstants::GET);
      $r->setParameters('a=b');
      $this->assertEquals(
        "GET /?a=b HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n\r\n",
        $r->getHeaderString()
      );
    }

    /**
     * Test getHeaderString()
     *
     */
    #[@test]
    public function headerStringDoesNotIncludeContent() {
      $r= new HttpRequest(new URL('http://example.com/'));
      $r->setMethod(HttpConstants::POST);
      $r->setParameters('a=b');
      $this->assertEquals(
        "POST / HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\nContent-Length: 3\r\nContent-Type: application/x-www-form-urlencoded\r\n\r\n",
        $r->getHeaderString()
      );
    }

    /**
     * Test getHeaderString()
     *
     */
    #[@test]
    public function emptyPostBody() {
      $r= new HttpRequest(new URL('http://example.com/'));
      $r->setMethod(HttpConstants::POST);
      $r->setParameters('');
      $this->assertEquals(
        "POST / HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\nContent-Length: 0\r\nContent-Type: application/x-www-form-urlencoded\r\n\r\n",
        $r->getHeaderString()
      );
    }

    /**
     * Test getHeaderString()
     *
     */
    #[@test]
    public function oneByteBody() {
      $r= new HttpRequest(new URL('http://example.com/'));
      $r->setMethod(HttpConstants::POST);
      $r->setParameters(new RequestData('1'));
      $this->assertEquals(
        "POST / HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\nContent-Length: 1\r\nContent-Type: application/x-www-form-urlencoded\r\n\r\n",
        $r->getHeaderString()
      );
    }

  }
?>
