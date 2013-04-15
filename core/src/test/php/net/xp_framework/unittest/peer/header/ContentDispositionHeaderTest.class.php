<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'net.xp_framework.unittest.peer.header.AbstractHeaderTest',
    'peer.header.ContentDispositionHeader'
  );

  /**
   * TestCase
   *
   * @see      xp://peer.header.ContentDispositionHeader
   */
  class ContentDispositionHeaderTest extends AbstractHeaderTest {

    /**
     * Setup
     *
     */
    public function setUp() {
      $this->isResponse=  TRUE;
      $this->isRequest=   FALSE;
      $this->isUnique=    TRUE;
      $this->className=   'peer.header.ContentDispositionHeader';
    }

    /**
     * Create new Header without content
     *
     */
    #[@test]
    public function newHeaderInitFilename() {
      $this->assertEquals('Content-Disposition: attachment; filename="filename.txt"', create(new ContentDispositionHeader('attachment', 'filename.txt'))->toString());
    }

    /**
     * Create new Header without content
     *
     */
    #[@test]
    public function newHeaderFilenameOverwrite() {
      $this->assertEquals('Content-Disposition: attachment; filename="secondFile.png"', create(new ContentDispositionHeader('attachment', 'firstFile.txt'))->withFilename('secondFile.png')->toString());
    }
  }
?>
