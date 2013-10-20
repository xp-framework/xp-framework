<?php namespace net\xp_framework\unittest\io\archive;



/**
 * TestCase for malformed zip files
 *
 */
class MalformedZipFileTest extends ZipFileTest {

  /**
   * Tests reading a zip file which is 0 bytes long
   *
   */
  #[@test, @expect('lang.FormatException')]
  public function zeroBytes() {
    $this->entriesIn($this->archiveReaderFor('malformed', 'zerobytes'));
  }

  /**
   * Tests reading a zip file with incomplete header (just the bytes
   * "PK" but then nothing else following)
   *
   */
  #[@test, @expect('lang.FormatException')]
  public function incompleteHeader() {
    $this->entriesIn($this->archiveReaderFor('malformed', 'pk'));
  }
}
