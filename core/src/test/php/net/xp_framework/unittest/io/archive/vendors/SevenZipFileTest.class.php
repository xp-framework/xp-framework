<?php namespace net\xp_framework\unittest\io\archive\vendors;

use io\streams\Streams;


/**
 * Tests 7-ZIP archives
 *
 * @see   http://www.7-zip.org/
 */
class SevenZipFileTest extends ZipFileVendorTest {
  
  /**
   * Returns vendor name
   *
   * @return  string
   */
  protected function vendorName() {
    return '7zip';
  }
  
  /**
   * Assertion helper
   *
   * @param   io.archive.zip.ZipArchiveReader reader
   * @throws  unittest.AssertionFailedError
   */
  protected function assertCompressedEntryIn($reader) {
    $entry= $reader->iterator()->next();
    $this->assertEquals('compression.txt', $entry->getName());
    $this->assertEquals(1660, $entry->getSize());
    
    with ($is= $entry->getInputStream()); {
      $this->assertEquals('This file is to be compressed', (string)$is->read(29));
      $is->read(1630);
      $this->assertEquals('.', (string)$is->read(1));
    }
  }

  /**
   * Tests deflate algorithm
   *
   */
  #[@test, @ext('zlib')]
  public function deflate() {
    $this->assertCompressedEntryIn($this->archiveReaderFor($this->vendor, 'deflate'));
  }

  /**
   * Tests bzip2 algorithm
   *
   */
  #[@test, @ext('bz2')]
  public function bzip2() {
    $this->assertCompressedEntryIn($this->archiveReaderFor($this->vendor, 'bzip2'));
  }

  /**
   * Tests deflate64 algorithm
   *
   */
  #[@test, @ignore('Not yet supported')]
  public function deflate64() {
    $this->assertCompressedEntryIn($this->archiveReaderFor($this->vendor, 'deflate64'));
  }

  /**
   * Tests lzma algorithm
   *
   */
  #[@test, @ignore('Not yet supported')]
  public function lzma() {
    $this->assertCompressedEntryIn($this->archiveReaderFor($this->vendor, 'lzma'));
  }

  /**
   * Tests ppmd algorithm
   *
   */
  #[@test, @ignore('Not yet supported')]
  public function ppmd() {
    $this->assertCompressedEntryIn($this->archiveReaderFor($this->vendor, 'ppmd'));
  }

  /**
   * Assertion helper
   *
   * @param   io.archive.zip.ZipArchiveReader reader
   * @throws  unittest.AssertionFailedError
   */
  protected function assertSecuredEntriesIn($reader) {
    with ($it= $reader->usingPassword('secret')->iterator()); {
      $entry= $it->next();
      $this->assertEquals('password.txt', $entry->getName());
      $this->assertEquals(15, $entry->getSize());
      $this->assertEquals('Secret contents', (string)Streams::readAll($entry->getInputStream()));

      $entry= $it->next();
      $this->assertEquals('very.txt', $entry->getName());
      $this->assertEquals(20, $entry->getSize());
      $this->assertEquals('Very secret contents', (string)Streams::readAll($entry->getInputStream()));
    }
  }

  /**
   * Tests password protection
   *
   */
  #[@test]
  public function zipCryptoPasswordProtected() {
    $this->assertSecuredEntriesIn($this->archiveReaderFor($this->vendor, 'zip-crypto'));
  }

  /**
   * Tests password protection
   *
   */
  #[@test, @ignore('Not yet supported')]
  public function aes256PasswordProtected() {
    $this->assertSecuredEntriesIn($this->archiveReaderFor($this->vendor, 'aes-256'));
  }
}
