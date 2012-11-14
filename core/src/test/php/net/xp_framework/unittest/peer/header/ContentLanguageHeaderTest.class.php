<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'net.xp_framework.unittest.peer.header.AbstractHeaderTest',
    'peer.header.ContentLanguageHeader'
  );

  /**
   * TestCase
   *
   * @see      xp://peer.header.ContentLanguageHeader
   */
  class ContentLanguageHeaderTest extends AbstractHeaderTest {

    /**
     * Setup
     *
     */
    public function setUp() {
      $this->isResponse=  TRUE;
      $this->isRequest=   FALSE;
      $this->isUnique=    TRUE;
      $this->className=   'peer.header.ContentLanguageHeader';
    }

    /**
     * Create new Header without content
     *
     */
    #[@test]
    public function newHeader() {
      $this->assertEquals('Content-Language: en-GB', create(new ContentLanguageHeader('en-GB'))->toString());
    }
  }
?>
