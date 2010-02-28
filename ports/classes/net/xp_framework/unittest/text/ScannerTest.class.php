<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'text.regex.CharacterClass'
  );

  /**
   * TestCase
   *
   * @see      xp://text.regex.Scanner
   * @purpose  purpose
   */
  class ScannerTest extends TestCase {

    /**
     * Test %d
     *
     */
    #[@test]
    public function int() {
      $scanner= new Scanner('%d');
      $this->assertEquals(array('123'), $scanner->match('123')->group(0));
    }

    /**
     * Test %d
     *
     */
    #[@test]
    public function negativeInt() {
      $scanner= new Scanner('%d');
      $this->assertEquals(array('-123'), $scanner->match('-123')->group(0));
    }

    /**
     * Test %x
     *
     */
    #[@test]
    public function hex() {
      $scanner= new Scanner('%x');
      $this->assertEquals(array('FF'), $scanner->match('FF')->group(0));
    }

    /**
     * Test %x
     *
     */
    #[@test]
    public function hexWith0XPrefix() {
      $scanner= new Scanner('%x');
      $this->assertEquals(array('0xFF'), $scanner->match('0xFF')->group(0));
    }

    /**
     * Test %f
     *
     */
    #[@test]
    public function float() {
      $scanner= new Scanner('%f');
      $this->assertEquals(array('123.20'), $scanner->match('123.20')->group(0));
    }

    /**
     * Test %f
     *
     */
    #[@test]
    public function negativeFloat() {
      $scanner= new Scanner('%f');
      $this->assertEquals(array('-123.20'), $scanner->match('-123.20')->group(0));
    }

    /**
     * Test %s
     *
     */
    #[@test]
    public function string() {
      $scanner= new Scanner('%s');
      $this->assertEquals(array('Hello'), $scanner->match('Hello')->group(0));
    }

    /**
     * Test %%
     *
     */
    #[@test]
    public function percentsSign() {
      $scanner= new Scanner('%d%%');
      $this->assertEquals(array('100', '%'), $scanner->match('100%')->group(0));
    }

    /**
     * Test %s
     *
     */
    #[@test]
    public function stringDoesNotMatchSpace() {
      $scanner= new Scanner('%s');
      $this->assertEquals(array('Hello'), $scanner->match('Hello World')->group(0));
    }

    /**
     * Test %s
     *
     */
    #[@test]
    public function scanEmptyString() {
      $scanner= new Scanner('%s');
      $this->assertEquals(array(), $scanner->match('')->groups());
      $this->assertEquals(0, $scanner->match('')->length());
    }

    /**
     * Test %[a-z ]
     *
     */
    #[@test]
    public function characterSequence() {
      $scanner= new Scanner('%[a-z ]');
      $this->assertEquals(array('hello world'), $scanner->match('hello world')->group(0));
    }

    /**
     * Test %[a-z-]
     *
     */
    #[@test]
    public function characterSequenceWithMinus() {
      $scanner= new Scanner('%[a-z-]');
      $this->assertEquals(array('hello-world'), $scanner->match('hello-world')->group(0));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function characterSequenceExcludes() {
      $scanner= new Scanner('%[^ ]');
      $this->assertEquals(array('0123'), $scanner->match('0123 are numbers')->group(0));
    }

    /**
     * Test %[][0-9.]
     *
     */
    #[@test]
    public function characterSequenceWithBracket() {
      $scanner= new Scanner('%[][0-9.]');
      $this->assertEquals(array('[0..9]'), $scanner->match('[0..9]')->group(0));
    }
  }
?>
