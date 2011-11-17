<?php
  /* This interface is part of the XP framework
   *
   * $Id$
   */

  /**
   * Interface for mock objects
   *
   * @purpose  Mockery
   */
  interface IMock{
    /**
     * Switches mock to replay mode.
     */
    function _replayMock();
    /**
     * Indicates whether the mock is in recording mode.
     *
     * @return boolean
     */
    function _isMockRecording();
    /**
     * Indicates whether the mock is in replaying mode.
     *
     * @return boolean
     */
    function _isMockReplaying();

    /**
     * Verifies expectations on the mock.
     */
    function _verifyMock();
  }
?>