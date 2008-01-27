<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'img.Color'
  );

  /**
   * TestCase
   *
   * @see      xp://img.Color
   * @purpose  Unittest
   */
  class ColorTest extends TestCase {
  
    /**
     * Test creating from hex string (with leading "#")
     *
     */
    #[@test]
    public function createFromHexStringWithHash() {
      $c= new Color('#fe3200');
      $this->assertEquals(0xFE, $c->red);
      $this->assertEquals(0x32, $c->green);
      $this->assertEquals(0x00, $c->blue);
    }

    /**
     * Test creating from hex string
     *
     */
    #[@test]
    public function createFromHexString() {
      $c= new Color('a7ff03');
      $this->assertEquals(0xA7, $c->red);
      $this->assertEquals(0xFF, $c->green);
      $this->assertEquals(0x03, $c->blue);
    }

    /**
     * Test creating from hex string
     *
     */
    #[@test]
    public function createFromHexStringWithCapitalLetters() {
      $c= new Color('A7FF03');
      $this->assertEquals(0xA7, $c->red);
      $this->assertEquals(0xFF, $c->green);
      $this->assertEquals(0x03, $c->blue);
    }

    /**
     * Test creating from RGB components
     *
     */
    #[@test]
    public function createFromRgb() {
      $c= new Color(0, 0, 0);
      $this->assertEquals(0, $c->red);
      $this->assertEquals(0, $c->green);
      $this->assertEquals(0, $c->blue);
    }

    /**
     * Test toHex() method
     *
     */
    #[@test]
    public function toHex() {
      $this->assertEquals('#efefef', create(new Color('#efefef'))->toHex());
    }

    /**
     * Test toString() method
     *
     */
    #[@test]
    public function toString() {
      $this->assertEquals('img.Color@(239, 010, 007)', create(new Color('#ef0a07'))->toString());
    }
  }
?>
