<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('util.profiling.unittest.TestCase');

  /**
   * Tests ticks
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
        $line= __LINE__;
      }

      $this->assertEquals(1, sizeof($this->ticks));
      $this->assertTicks(__FILE__, array(
        $line    => 1, 
        $line+ 1 => 1
      ));
    }
  }
?>
