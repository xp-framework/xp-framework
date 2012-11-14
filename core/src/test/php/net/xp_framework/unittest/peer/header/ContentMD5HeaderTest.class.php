<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'net.xp_framework.unittest.peer.header.AbstractHeaderTest',
    'peer.header.ContentMD5Header'
  );

  /**
   * TestCase
   *
   * @see      xp://peer.header.ContentMD5Header
   */
  class ContentMD5HeaderTest extends AbstractHeaderTest {

    /**
     * Setup
     *
     */
    public function setUp() {
      $this->isResponse=  TRUE;
      $this->isRequest=   TRUE;
      $this->isUnique=    TRUE;
      $this->className=   'peer.header.ContentMD5Header';
    }

    /**
     * Create new Header without content
     *
     */
    #[@test]
    public function newHeaderWithoutContent() {
      $this->assertEquals('Content-MD5: none-md5-test', create(new ContentMD5Header('none-md5-test'))->toString());
    }

    /**
     * Create new Header with content
     *
     */
    #[@test]
    public function newHeaderWithContent() {
      $content= 'This is some content';
      $md5= base64_encode(md5($content));
      $this->assertEquals('Content-MD5: '.$md5, create(new ContentMD5Header())->withContent($content)->toString());
    }
  }
?>
