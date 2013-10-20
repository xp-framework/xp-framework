<?php namespace net\xp_framework\unittest\scriptlet;

use unittest\TestCase;
use scriptlet\HttpScriptletResponse;


/**
 * TestCase
 *
 * @see      xp://scriptlet.HttpScriptletResponse
 */
class HttpScriptletResponseTest extends TestCase {
  protected $r= null;

  /**
   * Set up this testcase
   *
   */
  public function setUp() {
    $this->r= new HttpScriptletResponse();
  }

  /**
   * Test no headers are set initially
   *
   */
  #[@test]
  public function noHeaders() {
    $this->assertEmpty($this->r->headers);
  }

  /**
   * Test adding headers works
   *
   */
  #[@test]
  public function addHeader() {
    $this->r->setHeader('header', 'value');
    $this->assertEquals('value', $this->r->getHeader('header'));
  }

  /**
   * Test adding headers twice works
   *
   */
  #[@test]
  public function addHeaderTwice() {
    $this->r->setHeader('header', 'value');
    $this->r->setHeader('header', 'shadow');
    $this->assertEquals('value', $this->r->getHeader('header'));
    $this->assertEquals(2, sizeof($this->r->headers));
  }

  /**
   * Test
   *
   */
  #[@test]
  public function lookupCaseInsensitive() {
    $this->r->setHeader('header', 'value');
    $this->assertEquals('value', $this->r->getHeader('HEADER'));
  }

  /**
   * Test
   *
   */
  #[@test]
  public function nonexistingHeaderReturnsDefault() {
    $this->assertEquals('default', $this->r->getHeader('does_not_exist', 'default'));
  }

  /**
   * Test
   *
   */
  #[@test]
  public function writeToOutputStream() {
    $this->r->getOutputStream()->write('Hello');
    $this->assertEquals('Hello', $this->r->getContent());
  }

  /**
   * Test sendContent()
   *
   */
  #[@test]
  public function sendContent() {
    $this->r->setContent('Test');
    ob_start();
    $this->r->sendContent();
    $content= ob_get_contents();
    ob_end_clean();
    $this->assertEquals('Test', $content);
  }

  /**
   * Test sendContent()
   *
   */
  #[@test]
  public function doNotSendNullContent() {
    $this->r->setContent(null);
    ob_start();
    $this->r->sendContent();
    $content= ob_get_contents();
    ob_end_clean();
    $this->assertEquals('', $content);
  }

  /**
   * Test flush()
   *
   */
  #[@test]
  public function flush() {
    $this->r->flush();
  }

  /**
   * Test flush()
   *
   */
  #[@test, @expect('lang.IllegalStateException')]
  public function flushCalledTwice() {
    $this->r->flush();
    $this->r->flush();
  }

  /**
   * Test isCommitted()
   *
   */
  #[@test]
  public function isCommitted() {
    $this->assertFalse($this->r->isCommitted());
  }

  /**
   * Test isCommitted()
   *
   */
  #[@test]
  public function isCommittedAfterFlush() {
    $this->r->flush();
    $this->assertTrue($this->r->isCommitted());
  }

  /**
   * Test write()
   *
   */
  #[@test]
  public function writeToBuffer() {
    $this->r->write('Hello');
    $this->assertEquals('Hello', $this->r->getContent());
  }

  /**
   * Test write()
   *
   */
  #[@test]
  public function writeDirectly() {
    $this->r->flush();

    ob_start();
    $this->r->write('Hello');
    $content= ob_get_contents();
    ob_end_clean();

    $this->assertEquals('Hello', $content);
  }

  /**
   * Test write()
   *
   */
  #[@test]
  public function writeBufferedAndDirectWrites() {
    $this->r->write('Hello');   // This will be buffered

    ob_start();
    $this->r->flush();          // This will flush the buffer
    $this->r->write('World');
    $content= ob_get_contents();
    ob_end_clean();

    $this->assertEquals('HelloWorld', $content);
  }
}
