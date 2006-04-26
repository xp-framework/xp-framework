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
        @$this->ticks[basename($file)][$line]++;
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
      $ticks= $this->ticks[basename($file)];
      
      // Ticks work differently in PHP 4.3.11+ compared to PHP 4.4 (or PHP <= 4.3.10). 
      //
      // First of all, the register_tick_function() call will produce a tick in the 
      // buggy versions, whereas it won't in ones without this bug. Second, the tick 
      // function will tick twice at the end of the declare block instead of once. 
      // Third (and last), buggy PHP versions will continue to tick even after the 
      // declare-block is closed for exactly *one* more time (TODO: Figure out why 
      // sometimes it doesn't!?? <-- seems if the ticks array is <= 2 in size...)
      $phpversion= phpversion();
      if (
        version_compare($phpversion, '4.3.11', '>=') && version_compare($phpversion, '4.4', '<')
      ) {
        unset($ticks[key($ticks)]);
        if (sizeof($ticks) > 2) {
          end($ticks);
          unset($ticks[key($ticks)]);
        }
        end($ticks);
        $ticks[key($ticks)]--;
      }      
      
      $this->assertEquals(
        $frequencies,
        $ticks
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
        $line= __LINE__;
      }

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
        }                     // tick
      }                       // tick

      $this->assertFalse($executed);
      $this->assertTicks(__FILE__, array(
        $line    => 1, 
        $line+ 4 => 1,
        $line+ 5 => 2,
        $line+ 6 => 1,
      ));
    }
  }
?>
