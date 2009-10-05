<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'lang.archive.Archive',
    'io.Stream'
  );

  /**
   * TestCase
   *
   * @see      xp://lang.archive.Archive
   * @purpose  Unittest
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
     * @param   array<string, string> entries
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

    /**
     * Test reading a malformed archive
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function openNonArchive() {
      $a= new Archive(new Stream());
      $a->open(ARCHIVE_READ);
    }

    /**
     * Test contains() method
     *
     */
    #[@test]
    public function containsNonExistant() {
      $a= new Archive($this->archiveBytesAsStream());
      $a->open(ARCHIVE_READ);
      $this->assertEquals($this->version(), $a->version);
      $this->assertFalse($a->contains('DOES-NOT-EXIST'));
    }

    /**
     * Test extract() method
     *
     */
    #[@test, @expect('lang.ElementNotFoundException')]
    public function extractNonExistant() {
      $a= new Archive($this->archiveBytesAsStream());
      $a->open(ARCHIVE_READ);
      $this->assertEquals($this->version(), $a->version);
      $a->extract('DOES-NOT-EXIST');
    }

    /**
     * Test reading empty archive
     *
     */
    #[@test]
    public function readingEmptyArchive() {
      $a= new Archive($this->archiveBytesAsStream());
      $a->open(ARCHIVE_READ);
      $this->assertEquals($this->version(), $a->version);
      $this->assertEntries($a, array());
    }

    /**
     * Test reading non-empty archive
     *
     */
    #[@test]
    public function readingArchive() {
      $a= new Archive($this->getClass()->getPackage()->getResourceAsStream('v'.$this->version().'.xar'));
      $a->open(ARCHIVE_READ);
      $this->assertEquals($this->version(), $a->version);
      $this->assertTrue($a->contains('contained.txt'));
      $this->assertEntries($a, array('contained.txt' => "This file is contained in an archive!\n"));
    }
  
    /**
     * Test creating an empty archive
     *
     */
    #[@test]
    public function creatingEmptyArchive() {
      $a= new Archive(new Stream());
      $a->open(ARCHIVE_CREATE);
      $a->create();
      
      $this->assertEntries($a, array());
    }

    /**
     * Test creating an archive
     *
     */
    #[@test]
    public function creatingArchive() {
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
?>
