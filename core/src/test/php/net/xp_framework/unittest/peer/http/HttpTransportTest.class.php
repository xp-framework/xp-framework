<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'peer.http.HttpTransport'
  );

  /**
   * TestCase
   *
   * @see      xp://peer.http.HttpTransport
   */
  class HttpTransportTest extends TestCase {

    /**
     * Register test transport
     *
     */
    #[@beforeClass]
    public static function registerTransport() {
      HttpTransport::register('test', ClassLoader::defineClass('TestHttpTransport', 'peer.http.HttpTransport', array(), '{
        public $host, $port, $arg;

        public function __construct(URL $url, $arg) {
          $this->host= $url->getHost();
          $this->port= $url->getPort(80);
          $this->arg= $arg;
        }
        
        public function send(HttpRequest $request, $timeout= 60, $connecttimeout= 2.0) {
          // Not implemented
        }
      }'));
    }

    /**
     * Test register() method
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function registerIncorrectClass() {
      HttpTransport::register('irrelevant', $this->getClass());
    }
  
    /**
     * Test transportFor() method
     *
     */
    #[@test]
    public function port80IsDefaultPort() {
      $t= HttpTransport::transportFor(new URL('test://example.com'));
      $this->assertEquals('example.com', $t->host);
      $this->assertEquals(80, $t->port);
      $this->assertEquals(NULL, $t->arg);
    }

    /**
     * Test transportFor() method
     *
     */
    #[@test]
    public function withPort() {
      $t= HttpTransport::transportFor(new URL('test://example.com:8080'));
      $this->assertEquals('example.com', $t->host);
      $this->assertEquals(8080, $t->port);
      $this->assertEquals(NULL, $t->arg);
    }

    /**
     * Test transportFor() method
     *
     */
    #[@test]
    public function withPortAndArg() {
      $t= HttpTransport::transportFor(new URL('test+v2://example.com:443'));
      $this->assertEquals('example.com', $t->host);
      $this->assertEquals(443, $t->port);
      $this->assertEquals('v2', $t->arg);
    }
  }
?>
