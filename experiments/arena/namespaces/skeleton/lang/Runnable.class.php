<?php
/* This class is part of the XP framework
 *
 * $Id: Runnable.class.php 9892 2007-04-05 14:53:30Z friebe $ 
 */

  namespace lang;

  /**
   * Denotes instances of implementing classes are runnable by 
   * invoking the run() method.
   *
   * @purpose  Interface
   */
  interface Runnable {
  
    /**
     * Runs this object
     *
     */
    public function run();
  
  }
?>
