<?php namespace net\xp_framework\unittest\archive;

use unittest\TestCase;
use lang\archive\Archive;
use io\Stream;

/**
 * Base class for archive file tests
 *
 * @see  xp://net.xp_framework.unittest.archive.ArchiveV1Test
 * @see  xp://net.xp_framework.unittest.archive.ArchiveV2Test
 * @see   xp://lang.archive.Archive
 */
abstract class ArchiveTest extends TestCase {
  
  /**
   * Returns the xar version to test
   *
   * @return  int
   */
  protected abstract function version();

  /**
   * Asserts on entries in an archive
   *
   * @param   lang.archive.Archive a
   * @param   [:string] entries
   * @throws  unittest.AssertionFailedError
   */
  protected function assertEntries(Archive $a, array $entries) {
    $a->open(ARCHIVE_READ);
    $actual= array();
    while ($key= $a->getEntry()) {
      $actual[$key]= $a->extract($key);
    }
    $this->assertEquals($entries, $actual);
    $a->close();
  }
  
  /**
   * Returns 
   *
   * @return  io.Stream
   */
  protected function archiveBytesAsStream($version= -1) {
    static $bytes= array(
      1 => "CCA\1\0\0\0\0",
      2 => "CCA\2\0\0\0\0",
    );
    
    $s= new Stream();
    $s->open(STREAM_WRITE);
    $s->write($bytes[$version < 0 ? $this->version() : $version]);
    $s->write(str_repeat("\0", 248));   // Reserved bytes
    $s->close();
    
    return $s;
  }

  #[@test, @expect('lang.FormatException')]
  public function open_non_archive() {
    $a= new Archive(new Stream());
    $a->open(ARCHIVE_READ);
  }

  #[@test]
  public function version_equals_stream_version() {
    $a= new Archive($this->archiveBytesAsStream());
    $a->open(ARCHIVE_READ);
    $this->assertEquals($this->version(), $a->version);
  }

  #[@test]
  public function version_equals_resource_version() {
    $a= new Archive($this->getClass()->getPackage()->getResourceAsStream('v'.$this->version().'.xar'));
    $a->open(ARCHIVE_READ);
    $this->assertEquals($this->version(), $a->version);
  }

  #[@test]
  public function contains_non_existant() {
    $a= new Archive($this->archiveBytesAsStream());
    $a->open(ARCHIVE_READ);
    $this->assertFalse($a->contains('DOES-NOT-EXIST'));
  }

  #[@test, @expect('lang.ElementNotFoundException')]
  public function extract_non_existant() {
    $a= new Archive($this->archiveBytesAsStream());
    $a->open(ARCHIVE_READ);
    $a->extract('DOES-NOT-EXIST');
  }

  #[@test]
  public function entries_for_empty_archive_are_an_empty_array() {
    $a= new Archive($this->archiveBytesAsStream());
    $a->open(ARCHIVE_READ);
    $this->assertEntries($a, array());
  }

  #[@test]
  public function contains_existant() {
    $a= new Archive($this->getClass()->getPackage()->getResourceAsStream('v'.$this->version().'.xar'));
    $a->open(ARCHIVE_READ);
    $this->assertTrue($a->contains('contained.txt'));
  }

  #[@test]
  public function entries_for_empty_archive_contain_file() {
    $a= new Archive($this->getClass()->getPackage()->getResourceAsStream('v'.$this->version().'.xar'));
    $a->open(ARCHIVE_READ);
    $this->assertEntries($a, array('contained.txt' => "This file is contained in an archive!\n"));
  }

  #[@test]
  public function creating_empty_archive() {
    $a= new Archive(new Stream());
    $a->open(ARCHIVE_CREATE);
    $a->create();
    
    $this->assertEntries($a, array());
  }

  #[@test]
  public function creating_archive() {
    $contents= array(
      'lang/Object.class.php'    => 'class Object { }',
      'lang/Type.class.php'      => 'class Type extends Object { }'
    );
    
    $a= new Archive(new Stream());
    $a->open(ARCHIVE_CREATE);
    foreach ($contents as $filename => $bytes) {
      $a->addFileBytes($filename, NULL, NULL, $bytes);
    }
    $a->create();
    
    $this->assertEntries($a, $contents);
  }
}
