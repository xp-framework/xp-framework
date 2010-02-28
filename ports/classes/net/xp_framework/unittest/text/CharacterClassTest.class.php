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
   * @see      xp://text.regex.CharacterClass
   */
  class CharacterClassTest extends TestCase {

    /**
     * Test ALNUM class
     *
     */
    #[@test]
    public function alnumClass() {
      foreach (array('hello', 'hello123', '123', 'HelloWorld') as $value) {
        $this->assertTrue(CharacterClass::$ALNUM->matches($value), $value);
      }
    }

    /**
     * Test ALNUM class
     *
     */
    #[@test]
    public function notAlnumClass() {
      foreach (array('', '123.2', ' ', "Hello\0World") as $value) {
        $this->assertFalse(CharacterClass::$ALNUM->matches($value), $value);
      }
    }

    /**
     * Test WORD class
     *
     */
    #[@test]
    public function wordClass() {
      foreach (array('hello', 'hello123', '123', 'HelloWorld', 'A_B', '123_456') as $value) {
        $this->assertTrue(CharacterClass::$WORD->matches($value), $value);
      }
    }

    /**
     * Test WORD class
     *
     */
    #[@test]
    public function notWordClass() {
      foreach (array('', 'Hello World') as $value) {
        $this->assertFalse(CharacterClass::$WORD->matches($value), $value);
      }
    }

    /**
     * Test ALPHA class
     *
     */
    #[@test]
    public function alphaClass() {
      foreach (array('hello', 'HelloWorld') as $value) {
        $this->assertTrue(CharacterClass::$ALPHA->matches($value), $value);
      }
    }

    /**
     * Test ALPHA class
     *
     */
    #[@test]
    public function notAlphaClass() {
      foreach (array('', '123', '_') as $value) {
        $this->assertFalse(CharacterClass::$ALPHA->matches($value), $value);
      }
    }

    /**
     * Test BLANK class
     *
     */
    #[@test]
    public function blankClass() {
      foreach (array(' ', "\t", '    ', "\t ") as $value) {
        $this->assertTrue(CharacterClass::$BLANK->matches($value), $value);
      }
    }

    /**
     * Test BLANK class
     *
     */
    #[@test]
    public function notBlankClass() {
      foreach (array('', "\n", "\r", 'Hello World') as $value) {
        $this->assertFalse(CharacterClass::$BLANK->matches($value), $value);
      }
    }

    /**
     * Test CNTRL class
     *
     */
    #[@test]
    public function cntrlClass() {
      foreach (array("\1", "\r\n", "\x7F", "\0\1\2\3") as $value) {
        $this->assertTrue(CharacterClass::$CNTRL->matches($value), addcslashes($value, "\0..\377"));
      }
    }

    /**
     * Test CNTRL class
     *
     */
    #[@test]
    public function notCntrlClass() {
      foreach (array('', ' ', 'Hello World', '123', "\0Hello") as $value) {
        $this->assertFalse(CharacterClass::$CNTRL->matches($value), $value);
      }
    }

    /**
     * Test DIGIT class
     *
     */
    #[@test]
    public function digitClass() {
      foreach (array('0', '123') as $value) {
        $this->assertTrue(CharacterClass::$DIGIT->matches($value), $value);
      }
    }

    /**
     * Test DIGIT class
     *
     */
    #[@test]
    public function notDigitClass() {
      foreach (array('', ' ', '123.2', '0xFF', 'Hello World') as $value) {
        $this->assertFalse(CharacterClass::$DIGIT->matches($value), $value);
      }
    }

    /**
     * Test GRAPH class
     *
     */
    #[@test]
    public function graphClass() {
      foreach (array('0', '123', 'Hello', '.', 'ARG!!!', 'a[0]', '0xFF') as $value) {
        $this->assertTrue(CharacterClass::$GRAPH->matches($value), $value);
      }
    }

    /**
     * Test GRAPH class
     *
     */
    #[@test]
    public function notGraphClass() {
      foreach (array('', ' ', "\r\n\t", "\0") as $value) {
        $this->assertFalse(CharacterClass::$GRAPH->matches($value), $value);
      }
    }

    /**
     * Test LOWER class
     *
     */
    #[@test]
    public function lowerClass() {
      foreach (array('abc', 'z') as $value) {
        $this->assertTrue(CharacterClass::$LOWER->matches($value), $value);
      }
    }

    /**
     * Test LOWER class
     *
     */
    #[@test]
    public function notLowerClass() {
      foreach (array('', ' ', "\r\n\t", "\0", 'ABC', 'aBC', '123') as $value) {
        $this->assertFalse(CharacterClass::$LOWER->matches($value), $value);
      }
    }

    /**
     * Test PRINT class
     *
     */
    #[@test]
    public function printClass() {
      foreach (array('0', '123', ' ', 'Hello', '.', 'ARG!!!', 'a[0]', '0xFF') as $value) {
        $this->assertTrue(CharacterClass::$PRINT->matches($value), $value);
      }
    }

    /**
     * Test PRINT class
     *
     */
    #[@test]
    public function notPrintClass() {
      foreach (array('', "\r\n\t", "\0") as $value) {
        $this->assertFalse(CharacterClass::$PRINT->matches($value), $value);
      }
    }

    /**
     * Test PUNCT class
     *
     */
    #[@test]
    public function punctClass() {
      foreach (array(',', '.', '"!!"', '[', ']', '__') as $value) {
        $this->assertTrue(CharacterClass::$PUNCT->matches($value), $value);
      }
    }

    /**
     * Test PUNCT class
     *
     */
    #[@test]
    public function notPunctClass() {
      foreach (array('', "\r\n\t", "\0", 'Hello World', '123') as $value) {
        $this->assertFalse(CharacterClass::$PUNCT->matches($value), $value);
      }
    }

    /**
     * Test SPACE class
     *
     */
    #[@test]
    public function spaceClass() {
      foreach (array(' ', "\r\n\t", "\x0b\x0c") as $value) {
        $this->assertTrue(CharacterClass::$SPACE->matches($value), $value);
      }
    }

    /**
     * Test SPACE class
     *
     */
    #[@test]
    public function notSpaceClass() {
      foreach (array('', "\0", 'Hello World', '123') as $value) {
        $this->assertFalse(CharacterClass::$SPACE->matches($value), $value);
      }
    }

    /**
     * Test UPPER class
     *
     */
    #[@test]
    public function upperClass() {
      foreach (array('ABC', 'Z') as $value) {
        $this->assertTrue(CharacterClass::$UPPER->matches($value), $value);
      }
    }

    /**
     * Test UPPER class
     *
     */
    #[@test]
    public function notUpperClass() {
      foreach (array('', ' ', "\r\n\t", "\0", 'abc', 'aBC', '123') as $value) {
        $this->assertFalse(CharacterClass::$UPPER->matches($value), $value);
      }
    }

    /**
     * Test XDIGIT class
     *
     */
    #[@test]
    public function xDigitClass() {
      foreach (array('0', '123', 'FF', 'FE12AC', 'efefef') as $value) {
        $this->assertTrue(CharacterClass::$XDIGIT->matches($value), $value);
      }
    }

    /**
     * Test XDIGIT class
     *
     */
    #[@test]
    public function notXDigitClass() {
      foreach (array('', ' ', '123.2', '0xFF', 'Hello World') as $value) {
        $this->assertFalse(CharacterClass::$XDIGIT->matches($value), $value);
      }
    }
  }
?>
