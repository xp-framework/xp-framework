<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'net.xp_framework.unittest.peer.header.AbstractHeaderTest',
    'peer.header.ContentLengthHeader'
  );

  /**
   * TestCase
   *
   * @see      xp://peer.header.ContentLengthHeader
   */
  class ContentLengthHeaderTest extends AbstractHeaderTest {

    /**
     * Setup
     *
     */
    public function setUp() {
      $this->isResponse=  TRUE;
      $this->isRequest=   TRUE;
      $this->isUnique=    TRUE;
      $this->className=   'peer.header.ContentLengthHeader';
    }

    /**
     * Create new Header without content
     *
     */
    #[@test]
    public function newHeaderWithoutContent() {
      $this->assertEquals('Content-Length: 22', create(new ContentLengthHeader(22))->toString());
    }

    /**
     * Create new Header with content
     *
     */
    #[@test]
    public function newHeaderWithContent() {
      $content= 'This content has a length of 31';
      $this->assertEquals('Content-Length: 31', create(new ContentLengthHeader())->withContent($content)->toString());
    }
  }
?>
