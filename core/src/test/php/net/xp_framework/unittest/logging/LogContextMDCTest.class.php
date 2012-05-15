<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'util.log.context.LogContextMDC'
  );

  /**
   * Tests LogContextMDC class
   *
   */
  class LogContextMDCTest extends TestCase {
    protected $context= NULL;

    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->context= new LogContextMDC();
    }

    /**
     * Tests LogContextMDC::hasKey()
     *
     */
    #[@test]
    public function hasKey() {
      $this->assertFalse($this->context->hasKey('key1'));
      $this->context->put('key1', 'val1');
      $this->assertTrue($this->context->hasKey('key1'));

      $this->assertFalse($this->context->hasKey('key2'));
      $this->context->put('key2', 'val2');
      $this->assertTrue($this->context->hasKey('key2'));
    }

    /**
     * Tests LogContextMDC::get()
     *
     */
    #[@test]
    public function get() {
      $this->assertNull($this->context->get('key1'));
      $this->context->put('key1', 'val1');
      $this->assertEquals('val1', $this->context->get('key1'));

      $this->assertNull($this->context->get('key2'));
      $this->context->put('key2', 'val2');
      $this->assertEquals('val2', $this->context->get('key2'));
    }

    /**
     * Tests LogContextMDC::remove()
     *
     */
    #[@test]
    public function remove() {
      $this->context->put('key1', 'val1');
      $this->assertEquals('val1', $this->context->get('key1'));
      $this->context->remove('key1');
      $this->assertNull($this->context->get('key1'));
    }

    /**
     * Tests LogContextMDC::remove()
     *
     */
    #[@test]
    public function removeUnexistingKey() {
      $this->context->remove('unexistingKey');
    }

    /**
     * Tests LogContextMDC::clear()
     *
     */
    #[@test]
    public function clear() {
      $this->context->put('key1', 'val1');
      $this->context->put('key2', 'val2');
      $this->context->clear();
      $this->assertFalse($this->context->hasKey('key1'));
      $this->assertFalse($this->context->hasKey('key2'));
    }

    /**
     * Tests LogContextMDC::format()
     *
     */
    #[@test]
    public function format() {
      $this->assertEquals('', $this->context->format());
      $this->context->put('key1', 'val1');
      $this->context->put('key2', 'val2');
      $this->assertEquals('key1=val1 key2=val2', $this->context->format());
    }
  }
?>