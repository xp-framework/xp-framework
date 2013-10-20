<?php namespace net\xp_framework\unittest\archive;

use lang\archive\Archive;

/**
 * Unittest v2 XARs
 *
 * @see  xp://net.xp_framework.unittest.archive.ArchiveTest
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

  #[@test]
  public function read_empty_archive_with_version_1() {
    $a= new Archive($this->archiveBytesAsStream(1));
    $a->open(ARCHIVE_READ);
    $this->assertEquals(1, $a->version);
    $this->assertEntries($a, array());
  }

  #[@test]
  public function read_archive_with_version_1() {
    $a= new Archive($this->getClass()->getPackage()->getResourceAsStream('v1.xar'));
    $a->open(ARCHIVE_READ);
    $this->assertEquals(1, $a->version);
    $this->assertTrue($a->contains('contained.txt'));
    $this->assertEntries($a, array('contained.txt' => "This file is contained in an archive!\n"));
  }
}
