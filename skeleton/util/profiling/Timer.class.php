<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  /**
   * The Timer class provides a simple timer
   *
   * <code>
   *   $p= &new Timer();
   *   $p->start();
   *   // ... code you want profiled
   *   $p->stop();
   *   var_dump($p->elapsedTime());
   * </code>
   *
   * @purpose  Provide a simple profiling timer
   */
  class Timer extends Object {
    public
      $start    = 0.0,
      $float    = 0.0;
      
    /**
     * Retrieve current microtime
     *
     * @return  float microtime
     */
    public function microtime() {
      list($usec, $sec) = explode(' ', microtime()); 
      return (float)$usec + (float)$sec;
    }

    /**
     * Start the timer
     *
     */
    public function start() {
      $this->start= $this->microtime();
    }
    
    /**
     * Stop the timer
     *
     */
    public function stop() {
      $this->stop= $this->microtime();
    }
    
    /**
     * Retrieve elapsed time
     *
     * @return  float seconds elapsed
     */
    public function elapsedTime() {
      return $this->stop - $this->start;
    }
  }
?>
