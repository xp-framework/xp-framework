<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'net.xp_framework.unittest.peer.header.AbstractHeaderTest',
    'peer.header.ContentEncodingHeader'
  );

  /**
   * TestCase
   *
   * @see      xp://peer.header.ContentEncodingHeader
   */
  class ContentEncodingHeaderTest extends AbstractHeaderTest {

    /**
     * Setup
     *
     */
    public function setUp() {
      $this->isResponse=  TRUE;
      $this->isRequest=   FALSE;
      $this->isUnique=    TRUE;
      $this->className=   'peer.header.ContentEncodingHeader';
    }

    /**
     * Create new Header without content
     *
     */
    #[@test]
    public function newHeader() {
      $this->assertEquals('Content-Encoding: base64', create(new ContentEncodingHeader('base64'))->toString());
    }
  }
?>
