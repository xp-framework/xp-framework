<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'peer.http.HttpResponse',
    'io.streams.MemoryInputStream'
  );

  /**
   * TestCase for HTTP responses
   *
   * @see      xp://peer.http.HttpResponse
   * @purpose  Unittest
   */
  class HttpResponseTest extends TestCase {
  
    /**
     * Get a response with the specified headers and body
     *
     * @param   string[] headers
     * @param   string body default ''
     * @return  peer.http.HttpResponse
     */
    protected function newResponse(array $headers, $body= '') {
      return new HttpResponse(new MemoryInputStream(implode("\r\n", $headers)."\r\n\r\n".$body));
    }

    /**
     * Test non-empty response
     *
     */
    #[@test]
    public function errorDocument() {
      $body= '<h1>File not found</h1>';
      $response= $this->newResponse(array('HTTP/1.0 404 OK', 'Content-Length: 23', 'Content-Type: text/html'), $body);
      $this->assertEquals(404, $response->statusCode());
      $this->assertEquals(array('23'), $response->header('Content-Length'));
      $this->assertEquals(array('text/html'), $response->header('Content-Type'));
      $this->assertEquals($body, $response->readData());
    }
  
    /**
     * Test empty response
     *
     */
    #[@test]
    public function emptyDocument() {
      $response= $this->newResponse(array('HTTP/1.0 204 No content'));
      $this->assertEquals(204, $response->statusCode());
    }

    /**
     * Test chunked transfer-encoding
     *
     */
    #[@test]
    public function chunkedDocument() {
      $body= '<h1>File not found</h1>';
      $response= $this->newResponse(array('HTTP/1.0 404 OK', 'Transfer-Encoding: chunked'), "17\r\n".$body."\r\n0\r\n");
      $this->assertEquals(404, $response->statusCode());
      $this->assertEquals(array('chunked'), $response->header('Transfer-Encoding'));
      $this->assertEquals($body, $response->readData());
    }

    /**
     * Test chunked transfer-encoding
     *
     */
    #[@test]
    public function multipleChunkedDocument() {
      $response= $this->newResponse(
        array('HTTP/1.0 404 OK', 'Transfer-Encoding: chunked'),
        "17\r\n<h1>File not found</h1>\r\n13\r\nDid my best, sorry.\r\n0\r\n"
      );
      $this->assertEquals(404, $response->statusCode());
      $this->assertEquals(array('chunked'), $response->header('Transfer-Encoding'));
      
      // Read data & test body contents
      $buffer= ''; while ($l= $response->readData()) { $buffer.= $l; }
      $this->assertEquals('<h1>File not found</h1>Did my best, sorry.', $buffer);
    }

    /**
     * Test HTTP 100 Continue
     *
     */
    #[@test]
    public function httpContinue() {
      $response= $this->newResponse(array('HTTP/1.0 100 Continue', '', 'HTTP/1.0 200 OK', 'Content-Length: 4'), 'Test');
      $this->assertEquals(200, $response->statusCode());
      $this->assertEquals(array('4'), $response->header('Content-Length'));
      $this->assertEquals('Test', $response->readData());
    }
    
    /**
     * Test status code with message string
     *
     */
    #[@test]
    public function statusCodeWithMessage() {
      $response= $this->newResponse(array('HTTP/1.1 404 Not Found'), 'File Not Found');
      $this->assertEquals(404, $response->statusCode());
      $this->assertEquals('Not Found', $response->message());
      $this->assertEquals('File Not Found', $response->readData());
    }
    
    /**
     * Test status code without message string
     *
     */
    #[@test]
    public function statusCodeWithoutMessage() {
      $response= $this->newResponse(array('HTTP/1.1 404'), 'File Not Found');
      $this->assertEquals(404, $response->statusCode());
      $this->assertEquals('', $response->message());
      $this->assertEquals('File Not Found', $response->readData());
    }

    /**
     * Test what happens when the server responds with an incorrect protocol
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function incorrectProtocol() {
      $this->newResponse(array('* OK IMAP server ready H mimap20 68140'));
    }

    /**
     * Test getHeader()
     *
     * @deprecated  HttpResponse::getHeader() is deprecated
     */
    #[@test]
    public function getHeader() {
      $response= $this->newResponse(array('HTTP/1.0 200 OK', 'X-Binford: 6100', 'Content-Type: text/html'));
      $this->assertEquals('6100', $response->getHeader('X-Binford'));
      $this->assertEquals('text/html', $response->getHeader('Content-Type'));
    }

    /**
     * Test getHeader()
     *
     * @deprecated  HttpResponse::getHeader() is deprecated
     */
    #[@test]
    public function getHeaderIsCaseInsensitive() {
      $response= $this->newResponse(array('HTTP/1.0 200 OK', 'X-Binford: 6100', 'Content-Type: text/html'));
      $this->assertEquals('6100', $response->getHeader('x-binford'), 'all-lowercase');
      $this->assertEquals('text/html', $response->getHeader('CONTENT-TYPE'), 'all-uppercase');
    }

    /**
     * Test getHeader()
     *
     * @deprecated  HttpResponse::getHeader() is deprecated
     */
    #[@test]
    public function nonExistantGetHeader() {
      $response= $this->newResponse(array('HTTP/1.0 204 No Content'));
      $this->assertNull($response->getHeader('Via'));
    }

    /**
     * Test getHeader()
     *
     * @deprecated  HttpResponse::getHeader() is deprecated
     */
    #[@test]
    public function multipleCookiesInGetHeader() {
      $response= $this->newResponse(array('HTTP/1.0 200 OK', 'Set-Cookie: color=green; path=/', 'Set-Cookie: make=example; path=/'));
      $this->assertEquals(
        'make=example; path=/',
        $response->getHeader('Set-Cookie')
      );
    }

    /**
     * Test getHeaders()
     *
     * @deprecated  HttpResponse::getHeaders() is deprecated
     */
    #[@test]
    public function getHeaders() {
      $response= $this->newResponse(array('HTTP/1.0 200 OK', 'X-Binford: 6100', 'Content-Type: text/html'));
      $this->assertEquals(
        array('X-Binford' => '6100', 'Content-Type' => 'text/html'),
        $response->getHeaders()
      );
    }

    /**
     * Test getHeaders()
     *
     * @deprecated  HttpResponse::getHeaders() is deprecated
     */
    #[@test]
    public function emptyGetHeaders() {
      $response= $this->newResponse(array('HTTP/1.0 204 No Content'));
      $this->assertEquals(
        array(),
        $response->getHeaders()
      );
    }

    /**
     * Test getHeaders()
     *
     * @deprecated  HttpResponse::getHeaders() is deprecated
     */
    #[@test]
    public function multipleCookiesInGetHeaders() {
      $response= $this->newResponse(array('HTTP/1.0 200 OK', 'Set-Cookie: color=green; path=/', 'Set-Cookie: make=example; path=/'));
      $this->assertEquals(
        array('Set-Cookie' => 'make=example; path=/'),
        $response->getHeaders('Set-Cookie')
      );
    }

    /**
     * Test header()
     *
     */
    #[@test]
    public function header() {
      $response= $this->newResponse(array('HTTP/1.0 200 OK', 'X-Binford: 6100', 'Content-Type: text/html'));
      $this->assertEquals(array('6100'), $response->header('X-Binford'));
      $this->assertEquals(array('text/html'), $response->header('Content-Type'));
    }

    /**
     * Test header()
     *
     */
    #[@test]
    public function headerIsCaseInsensitive() {
      $response= $this->newResponse(array('HTTP/1.0 200 OK', 'X-Binford: 6100', 'Content-Type: text/html'));
      $this->assertEquals(array('6100'), $response->header('x-binford'), 'all-lowercase');
      $this->assertEquals(array('text/html'), $response->header('CONTENT-TYPE'), 'all-uppercase');
    }

    /**
     * Test header()
     *
     */
    #[@test]
    public function nonExistantHeader() {
      $response= $this->newResponse(array('HTTP/1.0 204 No Content'));
      $this->assertNull($response->header('Via'));
    }

    /**
     * Test header()
     *
     */
    #[@test]
    public function multipleCookiesInHeader() {
      $response= $this->newResponse(array('HTTP/1.0 200 OK', 'Set-Cookie: color=green; path=/', 'Set-Cookie: make=example; path=/'));
      $this->assertEquals(
        array('color=green; path=/', 'make=example; path=/'),
        $response->header('Set-Cookie')
      );
    }

    /**
     * Test headers()
     *
     */
    #[@test]
    public function multipleCookiesInHeaders() {
      $response= $this->newResponse(array('HTTP/1.0 200 OK', 'Set-Cookie: color=green; path=/', 'Set-Cookie: make=example; path=/'));
      $this->assertEquals(
        array('Set-Cookie' => array('color=green; path=/', 'make=example; path=/')),
        $response->headers()
      );
    }

    /**
     * Test headers()
     *
     */
    #[@test]
    public function headers() {
      $response= $this->newResponse(array('HTTP/1.0 200 OK', 'X-Binford: 6100', 'Content-Type: text/html'));
      $this->assertEquals(
        array('X-Binford' => array('6100'), 'Content-Type' => array('text/html')),
        $response->headers()
      );
    }

    /**
     * Test headers()
     *
     */
    #[@test]
    public function emptyHeaders() {
      $response= $this->newResponse(array('HTTP/1.0 204 No Content'));
      $this->assertEquals(
        array(),
        $response->headers()
      );
    }

    /**
     * Test headers() with inconsistent casing in response
     *
     */
    #[@test]
    public function multipleHeadersWithDifferentCasing() {
      $response= $this->newResponse(array('HTTP/1.0 200 OK', 'X-Example: K', 'x-example: V'));
      $this->assertEquals(
        array('X-Example' => array('K', 'V')),
        $response->headers()
      );
    }

    /**
     * Test header() with inconsistent casing in response
     *
     */
    #[@test]
    public function multipleHeaderWithDifferentCasing() {
      $response= $this->newResponse(array('HTTP/1.0 200 OK', 'X-Example: K', 'x-example: V'));
      $this->assertEquals(
        array('K', 'V'),
        $response->header('X-Example')
      );
    }
  }
?>
