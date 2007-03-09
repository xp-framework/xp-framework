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
  class ControlStructuresEmitterTest extends AbstractEmitterTest {

    /**
     * Tests an if-statement without else
     *
     */
    #[@test]
    public function ifWithoutElse() {
      $this->assertSourcecodeEquals(
        preg_replace('/\n\s*/', '', 'if (1){ 
          echo 1; 
        };'),
        $this->emit('if (1) {
          echo 1;
        }')
      );
    }

    /**
     * Tests an if-statement with else
     *
     */
    #[@test]
    public function ifWithElse() {
      $this->assertSourcecodeEquals(
        preg_replace('/\n\s*/', '', 'if (1){ 
          echo 1; 
        } else {
          echo \'The world is upside-down!\'; 
        };'),
        $this->emit('if (1) {
          echo 1;
        } else {
          echo "The world is upside-down!";
        }')
      );
    }

    /**
     * Tests an if-statement with multiple elses
     *
     */
    #[@test]
    public function ifWithElses() {
      $this->assertSourcecodeEquals(
        preg_replace('/\n\s*/', '', 'if ($argc==0){ 
          echo 0; 
        } else if  ($argc==1){ 
          echo 1; 
        } else {
          echo \'Incorrect # of args!\'; 
        };'),
        $this->emit('if ($argc == 0) {
          echo 0; 
        } else if  ($argc == 1) {
          echo 1;
        } else {
          echo "Incorrect # of args!";
        }')
      );
    }

    /**
     * Tests an switch-statement with neither cases nor a default
     *
     */
    #[@test]
    public function switchWithoutCasesOrDefault() {
      $this->assertSourcecodeEquals(
        preg_replace('/\n\s*/', '', 'switch (TRUE) {
        };'),
        $this->emit('switch (TRUE) {
        }')
      );
    }

    /**
     * Tests an switch-statement without a default
     *
     */
    #[@test]
    public function switchWithoutDefault() {
      $this->assertSourcecodeEquals(
        preg_replace('/\n\s*/', '', 'switch ($argc) {
          case 0: echo 0; break; ; 
          case 1: echo 1; break; ; 
        };'),
        $this->emit('switch ($argc) {
          case 0: echo 0; break;
          case 1: echo 1; break;
        }')
      );
    }

    /**
     * Tests an switch-statement with a case that uses a block
     *
     */
    #[@test]
    public function switchWithCaseUsingBlocks() {
      $this->assertSourcecodeEquals(
        preg_replace('/\n\s*/', '', 'switch ($argc) {
          case 0: {$block= TRUE; }; break; ; 
          case 1: echo 1; break; ; 
          default: echo \'Incorrect # of args!\'; ; 
        };'),
        $this->emit('switch ($argc) {
          case 0: { $block= TRUE; } break;
          case 1: echo 1; break;
          default: echo "Incorrect # of args!";
        }')
      );
    }

    /**
     * Tests an switch-statement with a default
     *
     */
    #[@test]
    public function switchWithDefault() {
      $this->assertSourcecodeEquals(
        preg_replace('/\n\s*/', '', 'switch ($argc) {
          case 0: echo 0; break; ; 
          case 1: echo 1; break; ; 
          default: echo \'Incorrect # of args!\'; ; 
        };'),
        $this->emit('switch ($argc) {
          case 0: echo 0; break;
          case 1: echo 1; break;
          default: echo "Incorrect # of args!";
        }')
      );
    }

    /**
     * Tests a ternary operator
     *
     */
    #[@test]
    public function ternary() {
      $this->assertSourcecodeEquals(
        'echo $argc>1 ? \'OK\' : \'ERR\';',
        $this->emit('echo $argc > 1 ? "OK" : "ERR";')
      );
    }
  }
?>
