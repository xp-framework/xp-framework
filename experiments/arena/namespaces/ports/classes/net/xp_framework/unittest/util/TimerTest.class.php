<?php
/* This class is part of the XP framework
 *
 * $Id: TimerTest.class.php 9195 2007-01-08 18:51:20Z friebe $ 
 */

  namespace net::xp_framework::unittest::util;
 
  ::uses(
    'unittest.TestCase',
    'util.profiling.Timer',
    'util.Comparator'
  );

  /**
   * Test Timer class
   *
   * @see      xp://util.profiling.Timer
   * @purpose  Unit Test
   */
  class TimerTest extends unittest::TestCase {
    public
      $fixture= NULL;
    
    /**
     * Setup method. Creates the map member
     *
     */
    public function setUp() {
      $this->fixture= new util::profiling::Timer();
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
     * Tests elapsed time after usleep(20 * 1000)
     *
     */
    #[@test]
    public function twentyMilliSeconds() {
      $this->fixture->start();
      usleep(20 * 1000);
      $this->fixture->stop();
      $elapsed= $this->fixture->elapsedTime();
      
      // Please note usleep() doesn't guarantee exact times, use an error 
      // margin of 10 milli seconds it may be faster(!)
      $this->assertTrue($elapsed- 0.020 <= 0.010, $elapsed);
    }
  }
?>
