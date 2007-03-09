<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('net.xp_framework.tools.vm.unittest.emit.php5.AbstractEmitterTest');

  /**
   * Tests PHP5 emitter
   *
   * @purpose  Unit Test
   */
  class StringEmitterTest extends AbstractEmitterTest {

    /**
     * Tests a single-quoted string
     *
     */
    #[@test]
    public function singleQuotedString() {
      $this->assertSourcecodeEquals(
        'echo \'Hello\';',
        $this->emit('echo \'Hello\';')
      );
    }

    /**
     * Tests a single-quoted string with escaped single-quote inside
     *
     */
    #[@test]
    public function singleQuotedStringWithEscapedSingleQuote() {
      $this->assertSourcecodeEquals(
        'echo \'He\\\'s lazy\';',
        $this->emit('echo \'He\\\'s lazy\';')
      );
    }

    /**
     * Tests a single-quoted string
     *
     */
    #[@test]
    public function doubleQuotedString() {
      $this->assertSourcecodeEquals(
        'echo \'Hello\';',
        $this->emit('echo "Hello";')
      );
    }

    /**
     * Tests a single-quoted string with escaped double-quotes inside
     *
     */
    #[@test]
    public function doubleQuotedStringWithEscapedDoubleQuotes() {
      $this->assertSourcecodeEquals(
        'echo \'"Hello", he said\';',
        $this->emit('echo "\"Hello\", he said";')
      );
    }

    /**
     * Tests a single-quoted string with single-quote inside
     *
     */
    #[@test]
    public function doubleQuotedStringWithSingleQuote() {
      $this->assertSourcecodeEquals(
        'echo \'He\\\'s lazy\';',
        $this->emit('echo "He\'s lazy";')
      );
    }

    /**
     * Tests a hex-escape (\x[0-9A-Fa-f]{1,2})
     *
     */
    #[@test]
    public function hexEscape() {
      foreach (array(
        '\x0'     => chr(0),
        '\x00'    => chr(0),
        '\x67a'   => 'ga',
        'a\x67a'  => 'aga',
        'a\x67'   => 'ag',
      ) as $src => $emitted) {
        $this->assertSourcecodeEquals(
          'echo \''.$emitted.'\';',
          $this->emit('echo "'.$src.'";'),
          $src
        );
      }
    }

    /**
     * Tests an octal escape (\[0-7]{1,3})
     *
     */
    #[@test]
    public function octalEscape() {
      foreach (array(
        '\0'      => chr(0),
        '\00'     => chr(0),
        '\000'    => chr(0),
        '\147a'   => 'ga',
        'a\147a'  => 'aga',
        'a\147'   => 'ag',
      ) as $src => $emitted) {
        $this->assertSourcecodeEquals(
          'echo \''.$emitted.'\';',
          $this->emit('echo "'.$src.'";'),
          $src
        );
      }
    }
  }
?>
