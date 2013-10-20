<?php namespace net\xp_framework\unittest\io\archive;



/**
 * Base class for testing zip file contents
 *
 * @see      xp://io.archive.zip.ZipArchiveReader#entries
 */
class ZipFileIteratorTest extends ZipFileContentsTest {

  /**
   * Returns an array of entries in a given zip file
   *
   * @param   io.archive.zip.ZipArchiveReader reader
   * @return  [:string] content
   */
  protected function entriesWithContentIn(\io\archive\zip\ZipArchiveReader $zip) {
    $entries= array();
    for ($it= $zip->iterator(); $it->hasNext(); ) {
      $entry= $it->next();
      $entries[$entry->getName()]= $this->entryContent($entry);
    }
    return $entries;
  }

  /**
   * Tests iterator workings
   *
   */
  #[@test]
  public function emptyFilesHasNoEntries() {
    $this->assertFalse($this->archiveReaderFor('fixtures', 'nofiles')->iterator()->hasNext());
  }

  /**
   * Tests iterator workings
   *
   */
  #[@test, @expect('util.NoSuchElementException')]
  public function iterationOverEndForEmpty() {
    $this->archiveReaderFor('fixtures', 'nofiles')->iterator()->next();
  }

  /**
   * Tests iterator workings
   *
   */
  #[@test]
  public function iterationOverEnd() {
    $it= $this->archiveReaderFor('fixtures', 'onefile')->iterator();
    $it->next();
    try {
      $it->next();
      $this->fail('Expected exception not thrown', null, 'util.NoSuchElementException');
    } catch (\util\NoSuchElementException $expected) { }
    $this->assertFalse($it->hasNext());
  }

  /**
   * Tests iterator workings
   *
   */
  #[@test]
  public function iterator() {
    $it= $this->archiveReaderFor('fixtures', 'onefile')->iterator();
    $this->assertTrue($it->hasNext());
    $this->assertInstanceOf('io.archive.zip.ZipEntry', $it->next());
    $this->assertFalse($it->hasNext());
  }
}
