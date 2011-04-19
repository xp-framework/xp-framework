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
   * @purpose  Unittest v2 XARs
   */
  class ArchiveV2Test extends ArchiveTest {

    /**
     * Returns the xar version to test
     *
     * @return  int
     */
    protected function version() { 
      return 2;
    }

    /**
     * Test reading empty archive with version *1* (BC)
     *
     */
    #[@test]
    public function readingEmptyArchiveV1() {
      $a= new Archive($this->archiveBytesAsStream(1));
      $a->open(ARCHIVE_READ);
      $this->assertEquals(1, $a->version);
      $this->assertEntries($a, array());
    }

    /**
     * Test reading non-empty archive with version *1* (BC)
     *
     */
    #[@test]
    public function readingArchiveV1() {
      $a= new Archive($this->getClass()->getPackage()->getResourceAsStream('v1.xar'));
      $a->open(ARCHIVE_READ);
      $this->assertEquals(1, $a->version);
      $this->assertTrue($a->contains('contained.txt'));
      $this->assertEntries($a, array('contained.txt' => "This file is contained in an archive!\n"));
    }
  }
?>
