<?php namespace net\xp_framework\unittest\peer\mail;

use peer\mail\MimeMessage;

/**
 * Tests MimeMessage class
 */
class MimeMessageTest extends AbstractMessageTest {

  /**
   * Returns a new fixture
   *
   * @return  peer.mail.Message
   */
  protected function newFixture() {
    return new MimeMessage();
  }

  #[@test]
  public function default_headers_returned_by_getHeaderString() {
    $this->fixture->setHeader('X-Common-Header', 'test');
    $this->assertEquals(
      "Mime-Version: 1.0\n".
      "X-Common-Header: test\n".
      "Content-Type: multipart/mixed; boundary=\"".$this->fixture->getBoundary()."\";\n".
      "\tcharset=\"iso-8859-1\"\n".
      "X-Priority: 3 (Normal)\n".
      "Date: ".$this->fixture->getDate()->toString('r')."\n",
      $this->fixture->getHeaderString()
    );
  }
}