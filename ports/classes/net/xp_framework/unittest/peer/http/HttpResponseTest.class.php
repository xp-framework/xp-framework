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
      $this->assertEquals(404, $response->getStatusCode());
      $this->assertEquals('23', $response->getHeader('Content-Length'));
      $this->assertEquals('text/html', $response->getHeader('Content-Type'));
      $this->assertEquals($body, $response->readData());
    }
  
    /**
     * Test empty response
     *
     */
    #[@test]
    public function emptyDocument() {
      $response= $this->newResponse(array('HTTP/1.0 204 No content'));
      $this->assertEquals(204, $response->getStatusCode());
    }

    /**
     * Test chunked transfer-encoding
     *
     */
    #[@test]
    public function chunkedDocument() {
      $body= '<h1>File not found</h1>';
      $response= $this->newResponse(array('HTTP/1.0 404 OK', 'Transfer-Encoding: chunked'), "17\r\n".$body."\r\n0\r\n");
      $this->assertEquals(404, $response->getStatusCode());
      $this->assertEquals('chunked', $response->getHeader('Transfer-Encoding'));
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
      $this->assertEquals(404, $response->getStatusCode());
      $this->assertEquals('chunked', $response->getHeader('Transfer-Encoding'));
      
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
      $this->assertEquals(200, $response->getStatusCode());
      $this->assertEquals('4', $response->getHeader('Content-Length'));
      $this->assertEquals('Test', $response->readData());
    }
    
    /**
     * Test status code with message string
     *
     */
    #[@test]
    public function statusCodeWithMessage() {
      $response= $this->newResponse(array('HTTP/1.1 404 Not Found'), 'File Not Found');
      $this->assertEquals(404, $response->getStatusCode());
      $this->assertEquals('Not Found', $response->getMessage());
      $this->assertEquals('File Not Found', $response->readData());
    }
    
    /**
     * Test status code without message string
     *
     */
    #[@test]
    public function statusCodeWithoutMessage() {
      $response= $this->newResponse(array('HTTP/1.1 404'), 'File Not Found');
      $this->assertEquals(404, $response->getStatusCode());
      $this->assertEquals('', $response->getMessage());
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
  }
?>
