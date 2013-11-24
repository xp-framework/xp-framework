<?php namespace net\xp_framework\unittest\peer\mail;

use peer\mail\Message;

/**
 * Tests Message class
 */
class MessageTest extends AbstractMessageTest {

  /**
   * Returns a new fixture
   *
   * @return  peer.mail.Message
   */
  protected function newFixture() {
    return new Message();
  }

  #[@test]
  public function default_headers_returned_by_getHeaderString() {
    $this->fixture->setHeader('x-common-header', 'test');
    $this->assertEquals(
      "x-common-header: test\n".
      "content-type: text/plain;\n".
      "\tcharset=\"iso-8859-1\"\n".
      "mime-version: 1.0\n".
      "content-transfer-encoding: 8bit\n".
      "x-priority: 3 (Normal)\n".
      "date: ".$this->fixture->getDate()->toString('r')."\n",
      $this->fixture->getHeaderString()
    );
  }

}