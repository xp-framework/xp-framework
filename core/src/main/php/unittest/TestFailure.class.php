<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestOutcome');

  /**
   * Indicates a test failed
   *
   * @see      xp://unittest.TestAssertionFailed
   * @see      xp://unittest.TestError
   */
  interface TestFailure extends TestOutcome {
    
  }
?>
