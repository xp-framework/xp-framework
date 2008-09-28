<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'io.streams.MemoryInputStream',
    'peer.http.HttpInputStream', 
    'peer.http.HttpResponse', 
    'peer.http.HttpConstants'
  );

  /**
   * HTTP input stream tests
   *
   * @see      xp://peer.http.HttpInputStream
   * @purpose  Unittest
   */
  class HttpInputStreamTest extends TestCase {
  
    /**
     * Returns a HTTP response object
     *
     * @param   int status
     * @param   array<string, string> headers
     * @param   string body default ''
     * @return  peer.http.HttpResponse
     */
    protected function httpResponse($status, $headers, $body= '') {
      $response= 'HTTP/'.HttpConstants::VERSION_1_1.' '.$status." Test\r\n";
      foreach ($headers as $key => $val) {
        $response.= $key.': '.$val."\r\n";
      }
      $response.="\r\n".$body;

      return new HttpResponse(new MemoryInputStream($response));
    }
    
    /**
     * Reads stream contents
     *
     * @param   io.streams.InputStream is
     * @return  string bytes
     */
    protected function readAll(InputStream $is) {
      for ($contents= ''; $is->available(); ) {
        $contents.= $is->read();
      }
      $is->close();
      return $contents;
    }
    
    /**
     * Assertion helper
     *
     * @param   string data
     * @throws  unittest.AssertionFailedError
     */
    protected function assertRead($data) {
      with ($length= strlen($data), $r= $this->httpResponse(HttpConstants::STATUS_OK, array('Content-Length' => $length), $data)); {
      
        // Self-testing
        $this->assertEquals(HttpConstants::STATUS_OK, $r->getStatusCode());
        $this->assertEquals($length, (int)$r->getHeader('Content-Length'));
        
        // Check data
        $this->assertEquals($data, $this->readAll(new HttpInputStream($r)));
      }
    }
  
    /**
     * Test reading an empty response
     *
     */
    #[@test]
    public function readEmpty() {
      $this->assertRead('');
    }

    /**
     * Test reading a non-empty response
     *
     */
    #[@test]
    public function readNonEmpty() {
      $this->assertRead('Hello World');
    }

    /**
     * Test reading binary data (an image part of the XP framework's design)
     *
     */
    #[@test]
    public function readBinaryData() {
      $this->assertRead(
        "GIF89a\001\000\035\000\302\004\000\356\356\356\366\362\366\366\366\366\377\372".
        "\377\377\377\377\377\377\377\377\377\377\377\377\377!\371\004\001\n\000\007\000".
        ",\000\000\000\000\001\000\035\000\000\003\013H\272\323-P\200\031\002lK%\000;"
      );
    }

    /**
     * Test available() method
     *
     */
    #[@test]
    public function available() {
      with ($s= new HttpInputStream($this->httpResponse(
        HttpConstants::STATUS_OK, 
        array('Content-Length' => 10), 
        'HelloWorld'
      ))); {

        $this->assertNotEquals(0, $s->available(), 'before read #1');
        $this->assertEquals('Hello', $s->read(5));

        $this->assertNotEquals(0, $s->available(), 'before read #2');
        $this->assertEquals('World', $s->read(5));

        $this->assertEquals(0, $s->available(), 'after read #3');
      }
    }

    /**
     * Test available() method
     *
     */
    #[@test]
    public function availableWithChunks() {
      with ($s= new HttpInputStream($this->httpResponse(
        HttpConstants::STATUS_OK, 
        array('Transfer-Encoding' => 'chunked'), 
        "5\r\nHello\r\n".
        "5\r\nWorld\r\n".
        "0\r\n"
      ))); {

        $this->assertNotEquals(0, $s->available(), 'before read #1');
        $this->assertEquals('Hello', $s->read(5));

        $this->assertNotEquals(0, $s->available(), 'before read #2');
        $this->assertEquals('World', $s->read(5));

        $this->assertEquals(0, $s->available(), 'after read #3');
      }
    }
  }
?>
