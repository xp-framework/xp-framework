<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'scriptlet.HttpScriptletResponse'
  );

  /**
   * TestCase
   *
   * @see      xp://scriptlet.HttpScriptletResponse
   */
  class HttpScriptletResponseTest extends TestCase {
    protected $r= NULL;

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
  }
?>
