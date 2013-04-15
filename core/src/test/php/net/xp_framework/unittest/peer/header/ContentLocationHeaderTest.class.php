<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'net.xp_framework.unittest.peer.header.AbstractHeaderTest',
    'peer.header.ContentLocationHeader'
  );

  /**
   * TestCase
   *
   * @see      xp://peer.header.ContentLocationHeader
   */
  class ContentLocationHeaderTest extends AbstractHeaderTest {

    /**
     * Setup
     *
     */
    public function setUp() {
      $this->isResponse=  TRUE;
      $this->isRequest=   FALSE;
      $this->isUnique=    TRUE;
      $this->className=   'peer.header.ContentLocationHeader';
    }

    /**
     * Create new Header
     *
     */
    #[@test]
    public function newHeader() {
      $this->assertEquals('Content-Location: /index.htm', create(new ContentLocationHeader('/index.htm'))->toString());
    }
  }
?>
