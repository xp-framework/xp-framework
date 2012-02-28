<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Interface for mock objects
   *
   */
  interface IMock {
      
    /**
     * Switches mock to replay mode.
     */
    public function _replayMock();
    
    /**
     * Indicates whether the mock is in recording mode.
     *
     * @return boolean
     */
    public function _isMockRecording();
    
    /**
     * Indicates whether the mock is in replaying mode.
     *
     * @return boolean
     */
    public function _isMockReplaying();

    /**
     * Verifies expectations on the mock.
     */
    public function _verifyMock();
  }
?>
