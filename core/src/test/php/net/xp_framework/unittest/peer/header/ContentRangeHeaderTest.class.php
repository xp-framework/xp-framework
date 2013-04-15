<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'net.xp_framework.unittest.peer.header.AbstractHeaderTest',
    'peer.header.ContentRangeHeader'
  );

  /**
   * TestCase
   *
   * @see      xp://peer.header.ContentRangeHeader
   */
  class ContentRangeHeaderTest extends AbstractHeaderTest {

    /**
     * Setup
     *
     */
    public function setUp() {
      $this->isResponse=  TRUE;
      $this->isRequest=   FALSE;
      $this->isUnique=    TRUE;
      $this->className=   'peer.header.ContentRangeHeader';
    }

    /**
     * Create new Header
     *
     */
    #[@test]
    public function newHeader() {
      $this->assertEquals('Content-Range: bytes 0-2500/5000', create(new ContentRangeHeader(0, 2500, 5000))->toString());
    }
  }
?>
