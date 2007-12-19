<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('unittest.TestCase');

  /**
   * Tests ticks which we will use for code coverage.
   *
   * @see      php://register_tick_function
   * @see      php://unregister_tick_function
   * @purpose  Unit Test
   */
  class TicksTest extends TestCase {
    public
      $ticks= array();

    /**
     * Static initializer block. Resets (global) ticks declaration to zero 
     * so they will not interfere with ours.
     *
     */
    static function __static() {
      declare(ticks= 0);
    }
    
    /**
     * Setup method. Sets up tick handling
     *
     */
    public function setUp() {
      set_error_handler(array($this, 'tick'));
      register_tick_function('trigger_error', NULL, E_USER_NOTICE);
    }
    
    /**
     * Teardown method. Unregisters tick handling.
     *
     */
    public function tearDown() {
      unregister_tick_function('trigger_error');
      restore_error_handler();
    }

    /**
     * Tick handler
     *
     * @param   int level
     * @param   string message
     * @param   string file
     * @param   int line
     */
    public function tick($level, $message, $file, $line) {
      if (E_USER_NOTICE == $level) {
        $key= basename($file);
        if (!isset($this->ticks[$key])) $this->ticks[$key]= array();
        if (!isset($this->ticks[$key][$line])) $this->ticks[$key][$line]= 0;
        $this->ticks[$key][$line]++;
        return;
      }
      
      // Default error handler otherwise
      $e= error_reporting(0);
      $errors= xp::registry('errors');
      $errors[$file][$line][$msg]++;
      xp::registry('errors', $errors);
      error_reporting($e);
    }
    
    /**
     * Helper method
     *
     * @param   string file
     * @param   array<int, int> frequencies keys are line numbers, values are frequency
     */
    protected function assertTicks($file, $frequencies) {
      $this->assertEquals(
        $frequencies,
        $this->ticks[basename($file)]
      );      
    }
    
    /**
     * Tests the most basic form (which we will use for all other tests):
     * <code>
     *   declare(ticks= 1) {
     *     $line= __LINE__;
     *   }
     * </code>
     * We use the line to be able to reference this in the following 
     * assertions.
     *
     * In this case, two ticks should be produced:
     * <ol>
     *   <li>One for the assignment</li>
     *   <li>One for the closing curly bracket }</li>
     * </ol>
     *
     */
    #[@test]
    public function mostBasicForm() {
      declare(ticks= 1) {
        $line= __LINE__;      // tick
      }                       // tick

      $this->assertTicks(__FILE__, array(
        $line    => 1, 
        $line+ 1 => 1
      ));
    }

    /**
     * Tests an if / else statement where the condition in if() 
     * evaluates to true (thus the first block getting executed).
     *
     */
    #[@test]
    public function ifCondition() {
      declare(ticks= 1) {
        $line= __LINE__;      // tick
        if (TRUE) {
          $executed= TRUE;    // tick
        } else {              // tick
          $executed= FALSE;
        }                     // tick
      }                       // tick

      $this->assertTrue($executed);
      $this->assertTicks(__FILE__, array(
        $line    => 1, 
        $line+ 2 => 1,
        $line+ 3 => 1,
        $line+ 5 => 1,
        $line+ 6 => 1,
      ));
    }

    /**
     * Tests an if statement without the optional block (e.g. if (1) return;
     * instead of if (1) { return; }).
     *
     */
    #[@test]
    public function ifConditionWithoutBlock() {
      declare(ticks= 1) {
        $line= __LINE__;                            // tick 
        $executed= FALSE;                           // tick      
        if (TRUE) $executed= TRUE;                  // tick
      }                                             // tick (2?)

      $this->assertTrue($executed);
      $this->assertTicks(__FILE__, array(
        $line    => 1, 
        $line+ 1 => 1,
        $line+ 2 => 1,
        $line+ 3 => 2
      ));
    }

    /**
     * Tests an if / else statement where the condition in if() 
     * evaluates to false (thus the second block getting executed).
     *
     */
    #[@test]
    public function elseCondition() {
      declare(ticks= 1) {
        $line= __LINE__;      // tick
        if (FALSE) {
          $executed= TRUE;    
        } else {
          $executed= FALSE;   // tick
        }                     // tick (2)
      }                       // tick

      $this->assertFalse($executed);
      $this->assertTicks(__FILE__, array(
        $line    => 1, 
        $line+ 4 => 1,
        $line+ 5 => 2,
        $line+ 6 => 1,
      ));
    }

    /**
     * Tests a for loop
     *
     */
    #[@test]
    public function forLoop() {
      declare(ticks= 1) {
        $line= __LINE__;      // tick
        $executed= 0;         // tick
        for ($i= 0; $i < 5; $i++) {
          $executed++;        // tick (5)
        }                     // tick (6)                
      }                       // tick

      $this->assertEquals(5, $executed);
      $this->assertTicks(__FILE__, array(
        $line    => 1,
        $line+ 1 => 1,
        $line+ 3 => 5,
        $line+ 4 => 6,
        $line+ 5 => 1
      ));
    }

    /**
     * Tests a for loop that is not executed
     *
     */
    #[@test]
    public function notExecutedforLoop() {
      declare(ticks= 1) {
        $line= __LINE__;      // tick
        $executed= 0;         // tick
        for ($i= 0; $i < 0; $i++) {
          $executed++;
        }                     // tick             
      }                       // tick

      $this->assertEquals(0, $executed);
      $this->assertTicks(__FILE__, array(
        $line    => 1,
        $line+ 1 => 1,
        $line+ 4 => 1,
        $line+ 5 => 1
      ));
    }

    /**
     * Tests a while loop
     *
     */
    #[@test]
    public function whileLoop() {
      declare(ticks= 1) {
        $line= __LINE__;      // tick
        $executed= 0;         // tick
        $i= 0;                // tick
        while ($i++ < 5) {
          $executed++;        // tick (5)
        }                     // tick (6)            
      }                       // tick

      $this->assertEquals(5, $executed);
      $this->assertTicks(__FILE__, array(
        $line    => 1,
        $line+ 1 => 1,
        $line+ 2 => 1,
        $line+ 4 => 5,
        $line+ 5 => 6,
        $line+ 6 => 1
      ));
    }

    /**
     * Tests a while loop that is not executed
     *
     */
    #[@test]
    public function notExecutedWhileLoop() {
      declare(ticks= 1) {
        $line= __LINE__;      // tick
        $executed= 0;         // tick
        while (0) {
          $executed++;
        }                     // tick
      }                       // tick

      $this->assertEquals(0, $executed);
      $this->assertTicks(__FILE__, array(
        $line    => 1,
        $line+ 1 => 1,
        $line+ 4 => 1,
        $line+ 5 => 1
      ));
    }

    /**
     * Tests a multi-line assignment via ternary operator
     *
     */
    #[@test]
    public function multiLineStatement() {
      declare(ticks= 1) {
        $line= __LINE__;      // tick
        $greeting= (strlen('Hello') == 5
          ? 'Hello'
          : 'Moto'
        );                    // tick
      }                       // tick

      $this->assertEquals('Hello', $greeting);
      $this->assertTicks(__FILE__, array(
        $line    => 1,
        $line+ 4 => 1,
        $line+ 5 => 1
      ));
    }
    
    /**
     * Helper method for methodCall() test. No ticks are produced
     * in this method because only the code within the declare-
     * block is "tick"ed.
     *
     * @param   string who
     * @return  string
     */
    public function sayHelloTo($who) {
      $length= strlen($who);
      return 'Hello '.$who.' ('.$length.' bytes)';
    }

    /**
     * Tests a method call
     *
     */
    #[@test]
    public function methodCall() {
      declare(ticks= 1) {
        $line= __LINE__;                            // tick
        $helloWorld= $this->sayHelloTo('World');    // tick
      }                                             // tick

      $this->assertEquals('Hello World (5 bytes)', $helloWorld);
      $this->assertTicks(__FILE__, array(
        $line    => 1,
        $line+ 1 => 1,
        $line+ 2 => 1
      ));
    }

    /**
     * Tests a switch statement where the case that is matched is empty.
     *
     */
    #[@test]
    public function switchStatementWithEmptyCase() {
      declare(ticks= 1) {
        $line= __LINE__;                            // tick
        switch (strlen('Hello')) {
          case 5: break;
          default: throw(new PrerequisitesNotMetError('strlen() broken!'));
        }                                           // tick
      }                                             // tick

      $this->assertTicks(__FILE__, array(
        $line    => 1,
        $line+ 4 => 1,
        $line+ 5 => 1
      ));
    }

    /**
     * Tests a switch statement
     *
     */
    #[@test]
    public function switchStatement() {
      declare(ticks= 1) {
        $line= __LINE__;                            // tick
        switch (strlen('Hello')) {
          case 5: $result= TRUE; break;             // tick
          default: throw(new PrerequisitesNotMetError('strlen() broken!'));
        }                                           // tick
      }                                             // tick

      $this->assertTrue($result);
      $this->assertTicks(__FILE__, array(
        $line    => 1,
        $line+ 2 => 1,
        $line+ 4 => 1,
        $line+ 5 => 1
      ));
    }

    /**
     * Tests eval()
     *
     * @see     php://eval
     */
    #[@test]
    public function evaluation() {
      declare(ticks= 1) {
        $line= __LINE__;                            // tick
        eval('$evaluated= TRUE;');                  // tick
      }                                             // tick

      $this->assertTrue($evaluated);
      $this->assertTicks(__FILE__, array(
        $line    => 1,
        $line+ 1 => 1,
        $line+ 2 => 1,
      ));
    }
    
    /**
     * Helper method for returnStatement() test. A return statement
     * does not create a tick, whereas the assignment will!
     *
     * @return  int the line creating a tick within this method
     */
    public function tickedReturnSomething() {
      declare(ticks= 1) {
        $line= __LINE__;    // tick
        return $line;
      }
    }

    /**
     * Tests return
     *
     * @see     php://eval
     */
    #[@test]
    public function returnStatement() {
      $line= $this->tickedReturnSomething();

      $this->assertTicks(__FILE__, array(
        $line    => 1
      ));
    }

    /**
     * Tests eval() when the to-be-evaluated code spans multiple lines
     * The multiple lines inside the eval will not cause ticks for the
     * same reason the methodCall() test's helper method sayHelloTo()
     * states: Only the direct scope the declare statement (if used as
     * a block) is related to "tick"s.
     *
     * @see     php://eval
     */
    #[@test]
    public function evaluationOfMultiLineCode() {
      declare(ticks= 1) {
        $line= __LINE__;                            // tick
        eval('
          $evaluated= 0;
          $evaluated++;
          $evaluated--;
        ');                                         // tick
      }                                             // tick

      $this->assertEquals(0, $evaluated);
      $this->assertTicks(__FILE__, array(
        $line    => 1,
        $line+ 5 => 1,
        $line+ 6 => 1,
      ));
    }
    
    /**
     * Tests run-time-created function
     *
     * @see     php://create_function
     */
    #[@test]
    public function runtimeCreatedFunction() {
      declare(ticks= 1) {
        $line= __LINE__;                            // tick
        $strcmp= create_function(
          '$a, $b', 
          'return strcmp($a, $b);'
        );                                          // tick
        $result= $strcmp('A', 'A');                 // tick
      }                                             // tick

      $this->assertEquals(0, $result);
      $this->assertTicks(__FILE__, array(
        $line    => 1,
        $line+ 4 => 1,
        $line+ 5 => 1,
        $line+ 6 => 1
      ));
    }

    /**
     * Tests multiple statements per line
     *
     */
    #[@test]
    public function multipleStatements() {
      declare(ticks= 1) {
        $line= __LINE__;                            // tick
        $one= 1; $two= 2;                           // tick (2)
        $three= $drei= 3;                           // tick
      }                                             // tick

      $this->assertEquals(1, $one);
      $this->assertEquals(2, $two);
      $this->assertEquals(3, $three);
      $this->assertEquals($three, $drei);
      $this->assertTicks(__FILE__, array(
        $line    => 1,
        $line+ 1 => 2,
        $line+ 2 => 1,
        $line+ 3 => 1
      ));
    }
    
    /**
     * Tests a code block declared inline (by simply opening curly braces
     * and closing them again) will trigger a tick.
     *
     */
    #[@test]
    public function codeBlock() {
      declare(ticks= 1) {
        $line= __LINE__;                            // tick
        {
        }                                           // tick
      }                                             // tick

      $this->assertTicks(__FILE__, array(
        $line    => 1,
        $line+ 2 => 1,
        $line+ 3 => 1
      ));
    }

    /**
     * Tests nested code blocks
     *
     */
    #[@test]
    public function nestedCodeBlocks() {
      declare(ticks= 1) {
        $line= __LINE__;                            // tick
        $nestingLevel= 0;                           // tick
        {
          $nestingLevel++;                          // tick
          {
            $nestingLevel++;                        // tick
          }                                         // tick
        }                                           // tick
      }                                             // tick

      $this->assertEquals(2, $nestingLevel);
      $this->assertTicks(__FILE__, array(
        $line    => 1,
        $line+ 1 => 1,
        $line+ 3 => 1,
        $line+ 5 => 1,
        $line+ 6 => 1,
        $line+ 7 => 1,
        $line+ 8 => 1
      ));
    }

    /**
     * Tests exceptions.
     *
     */
    #[@test]
    public function exception() {
      declare(ticks= 1) {
        $line= __LINE__;                            // tick
        $message= NULL;                             // tick
        try {
          throw new XPException('*Boom*');
        } catch (XPException $e) {
          $message= $e->getMessage();               // tick
        }
      }                                             // tick (2?)

      $this->assertEquals('*Boom*', $message);
      $this->assertTicks(__FILE__, array(
        $line    => 1,
        $line+ 1 => 1,
        $line+ 5 => 1,
        $line+ 7 => 2,
      ));
    }

    /**
     * Tests define.
     *
     * @see     php://define
     */
    #[@test]
    public function definition() {
      declare(ticks= 1) {
        $line= __LINE__;                            // tick
        define('DEFINITION_TEST', 'defined');       // tick
      }                                             // tick

      $this->assertEquals('defined', DEFINITION_TEST);
      $this->assertTicks(__FILE__, array(
        $line    => 1,
        $line+ 1 => 1,
        $line+ 2 => 1
      ));
    }
  }
?>
