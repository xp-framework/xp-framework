<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('util.profiling.unittest.TestCase');

  /**
   * Tests ticks which we will use for code coverage.
   *
   * @see      php://register_tick_function
   * @see      php://unregister_tick_function
   * @purpose  Unit Test
   */
  class TicksTest extends TestCase {
    var
      $ticks= array();
    
    /**
     * Setup method. Sets up tick handling
     *
     * @access  public
     */
    function setUp() {
      set_error_handler(array(&$this, 'tick'));
      register_tick_function('trigger_error', NULL, E_USER_NOTICE);
    }
    
    /**
     * Teardown method. Unregisters tick handling.
     *
     * @access  public
     */
    function tearDown() {
      unregister_tick_function('trigger_error');
      restore_error_handler();
    }

    /**
     * Tick handler
     *
     * @access  protected
     * @param   int level
     * @param   string message
     * @param   string file
     * @param   int line
     */
    function tick($level, $message, $file, $line) {
      if (E_USER_NOTICE == $level) {
        $key= basename($file);
        if (!isset($this->ticks[$key])) $this->ticks[$key]= array();
        if (!isset($this->ticks[$key][$line])) $this->ticks[$key][$line]= 0;
        $this->ticks[$key][$line]++;
        return;
      }
      
      // Delegate to default error handler otherwise
      __error($level, $message, $file, $line);
    }
    
    /**
     * Helper method
     *
     * @access  protected
     * @param   string file
     * @param   array<int, int> frequencies keys are line numbers, values are frequency
     */
    function assertTicks($file, $frequencies) {
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
     * @access  public
     */
    #[@test]
    function mostBasicForm() {
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
     * @access  public
     */
    #[@test]
    function ifCondition() {
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
     * Tests an if / else statement where the condition in if() 
     * evaluates to false (thus the second block getting executed).
     *
     * @access  public
     */
    #[@test]
    function elseCondition() {
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
     * @access  public
     */
    #[@test]
    function forLoop() {
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
     * @access  public
     */
    #[@test]
    function notExecutedforLoop() {
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
     * @access  public
     */
    #[@test]
    function whileLoop() {
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
     * @access  public
     */
    #[@test]
    function notExecutedWhileLoop() {
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
  }
?>
