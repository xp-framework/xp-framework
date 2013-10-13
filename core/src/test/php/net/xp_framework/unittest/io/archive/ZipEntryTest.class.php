<?php namespace net\xp_framework\unittest\io\archive;

use unittest\TestCase;
use io\archive\zip\ZipFileEntry;
use io\archive\zip\ZipDirEntry;


/**
 * TestCase
 *
 * @see     xp://io.archive.zip.ZipFileEntry
 * @see     xp://io.archive.zip.ZipDirEntry
 */
class ZipEntryTest extends TestCase {

  /**
   * Test
   *
   */
  #[@test]
  public function simpleFileName() {
    $this->assertEquals('Hello.txt', create(new ZipFileEntry('Hello.txt'))->getName());
  }

  /**
   * Test
   *
   */
  #[@test]
  public function simpleDirName() {
    $this->assertEquals('Hello/', create(new ZipDirEntry('Hello'))->getName());
  }

  /**
   * Test
   *
   */
  #[@test]
  public function backslashesReplacedInFile() {
    $this->assertEquals('hello/World.txt', create(new ZipFileEntry('hello\\World.txt'))->getName());
  }

  /**
   * Test
   *
   */
  #[@test]
  public function backslashesReplacedInDir() {
    $this->assertEquals('hello/World/', create(new ZipDirEntry('hello\\World'))->getName());
  }

  /**
   * Test
   *
   */
  #[@test]
  public function trailingSlashesInDirNormalized() {
    $this->assertEquals('hello/', create(new ZipDirEntry('hello//'))->getName());
  }

  /**
   * Test
   *
   */
  #[@test]
  public function trailingBackslashesInDirNormalized() {
    $this->assertEquals('hello/', create(new ZipDirEntry('hello\\\\'))->getName());
  }

  /**
   * Test
   *
   */
  #[@test]
  public function composeFileFromString() {
    $this->assertEquals('META-INF/manifest.ini', create(new ZipFileEntry('META-INF', 'manifest.ini'))->getName());
  }

  /**
   * Test
   *
   */
  #[@test]
  public function composeDirFromString() {
    $this->assertEquals('META-INF/services/', create(new ZipDirEntry('META-INF', 'services'))->getName());
  }

  /**
   * Test
   *
   */
  #[@test]
  public function composeFileFromDirAndString() {
    $this->assertEquals('META-INF/manifest.ini', create(new ZipFileEntry(new ZipDirEntry('META-INF'), 'manifest.ini'))->getName());
  }

  /**
   * Test
   *
   */
  #[@test]
  public function composeDirFromDirAndString() {
    $this->assertEquals('META-INF/services/', create(new ZipDirEntry(new ZipDirEntry('META-INF'), 'services'))->getName());
  }
}
