<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.unittest.archive.ArchiveTest');

  /**
   * TestCase
   *
   * @see      xp://net.xp_framework.unittest.archive.ArchiveTest
   * @purpose  Unittest v1 XARs
   */
  class ArchiveV1Test extends ArchiveTest {

    /**
     * Returns the xar version to test
     *
     * @return  int
     */
    protected function version() { 
      return 1;
    }
  }
?>
