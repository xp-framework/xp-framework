<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'unittest.TestCase',
    'util.profiling.Timer',
    'util.Comparator'
  );

  /**
   * Tests Timer class
   *
   * @see      xp://util.profiling.Timer
   */
  class TimerTest extends TestCase {
    protected $fixture= NULL;
    
    /**
     * Setup method. Creates the map member
     *
     */
    public function setUp() {
      $this->fixture= new Timer();
    }
        
    /**
     * Tests elapsed time is zero if not started yet
     *
     */
    #[@test]
    public function initiallyZero() {
      $this->assertEquals(0.0, $this->fixture->elapsedTime());
    }

    /**
     * Tests elapsed time after 100 milliseconds
     *
     */
    #[@test]
    public function elapsedTimeGreaterThanZero() {
      $this->fixture->start();
      usleep(100 * 1000);
      $this->fixture->stop();
      $elapsed= $this->fixture->elapsedTime();
      $this->assertTrue($elapsed > 0.0, 'Elapsed time should be greater than zero');
    }
  }
?>
