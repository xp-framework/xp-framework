<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'net.xp_framework.unittest.peer.header.AbstractHeaderTest',
    'peer.header.ContentTypeHeader'
  );

  /**
   * TestCase
   *
   * @see      xp://peer.header.ContentTypeHeader
   */
  class ContentTypeHeaderTest extends AbstractHeaderTest {

    /**
     * Setup
     *
     */
    public function setUp() {
      $this->isResponse=  TRUE;
      $this->isRequest=   TRUE;
      $this->isUnique=    TRUE;
      $this->className=   'peer.header.ContentTypeHeader';
    }

    /**
     * Create new Header only type
     *
     */
    #[@test]
    public function newHeaderTypeOnly() {
      $this->assertEquals('Content-Type: application/pdf',
        create(new ContentTypeHeader('application/pdf'))->toString());
    }

    /**
     * Create new Header type+charset
     *
     */
    #[@test]
    public function newHeaderTypeCharset() {
      $this->assertEquals('Content-Type: application/msword; charset=utf-8',
        create(new ContentTypeHeader('application/msword', 'utf-8'))->toString());
    }

    /**
     * Create new Header type+boundary
     *
     */
    #[@test]
    public function newHeaderTypeBoundary() {
      $this->assertEquals('Content-Type: multipart/mixed; boundary=mybound',
        create(new ContentTypeHeader('multipart/mixed', NULL, 'mybound'))->toString());
    }

    /**
     * Create new Header all with overwrite
     *
     */
    #[@test]
    public function newHeaderAllOverwrite() {
      $this->assertEquals('Content-Type: useless/type; charset=utf-8; boundary=betterbound',
        create(new ContentTypeHeader('multipart/mixed', NULL, 'mybound'))
          ->withBoundary('betterbound')
          ->withCharset('utf-8')
          ->withType('useless/type')
          ->toString());
    }
  }
?>
