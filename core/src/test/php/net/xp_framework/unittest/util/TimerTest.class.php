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
    
    /**
     * Tests elapsed time is zero if not started yet
     *
     */
    #[@test]
    public function initiallyZero() {
      $this->assertEquals(0.0, create(new Timer())->elapsedTime());
    }

    /**
     * Tests elapsed time after 100 milliseconds
     *
     */
    #[@test]
    public function elapsedTimeGreaterThanZeroUsingStartAndStop() {
      $fixture= new Timer();
      $fixture->start();
      usleep(100 * 1000);
      $fixture->stop();
      $elapsed= $fixture->elapsedTime();
      $this->assertTrue($elapsed > 0.0, 'Elapsed time should be greater than zero');
    }

    /**
     * Tests elapsed time after 100 milliseconds
     *
     */
    #[@test]
    public function elapsedTimeGreaterThanZeroUsingMeasure() {
      $fixture= Timer::measure(function() {
        usleep(100 * 1000);
      });
      $elapsed= $fixture->elapsedTime();
      $this->assertTrue($elapsed > 0.0, 'Elapsed time should be greater than zero');
    }

    /**
     * Tests measure()
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function notCallable() {
      Timer::measure('@not-callable@');
    }
  }
?>
