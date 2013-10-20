<?php namespace net\xp_framework\unittest\io\archive;



/**
 * Base class for testing zip file contents
 *
 * @see      xp://io.archive.zip.ZipArchiveReader#entries
 */
class ZipFileEntriesTest extends ZipFileContentsTest {

  /**
   * Returns an array of entries in a given zip file
   *
   * @param   io.archive.zip.ZipArchiveReader reader
   * @return  [:string] content
   */
  protected function entriesWithContentIn(\io\archive\zip\ZipArchiveReader $zip) {
    $entries= array();
    foreach ($zip->entries() as $entry) {
      $entries[$entry->getName()]= $this->entryContent($entry);
    }
    return $entries;
  }
}
