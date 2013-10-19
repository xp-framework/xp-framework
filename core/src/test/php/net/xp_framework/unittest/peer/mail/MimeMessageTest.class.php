<?php namespace net\xp_framework\unittest\peer\mail;

use peer\mail\MimeMessage;
use peer\mail\MimePart;

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

  #[@test]
  public function boundary_accessors() {
    $this->fixture->setBoundary('----=_Part_4711Test');
    $this->assertEquals('----=_Part_4711Test', $this->fixture->getBoundary());
  }

  #[@test]
  public function add_part_returns_added_part() {
    $part= new MimePart();
    $this->assertEquals($part, $this->fixture->addPart($part));
  }

  #[@test]
  public function getParts_initially_returns_empty_array() {
    $this->assertEquals(array(), $this->fixture->getParts());
  }

  #[@test]
  public function getParts_returns_added_part() {
    $part= $this->fixture->addPart(new MimePart());
    $this->assertEquals(array($part), $this->fixture->getParts());
  }

  #[@test]
  public function getParts_returns_added_parts() {
    $part1= $this->fixture->addPart(new MimePart());
    $part2= $this->fixture->addPart(new MimePart());
    $this->assertEquals(array($part1, $part2), $this->fixture->getParts());
  }

  #[@test]
  public function getPart_returns_added_part() {
    $part= $this->fixture->addPart(new MimePart());
    $this->assertEquals($part, $this->fixture->getPart(0));
  }

  #[@test]
  public function getPart_returns_added_parts() {
    $part1= $this->fixture->addPart(new MimePart());
    $part2= $this->fixture->addPart(new MimePart());
    $this->assertEquals(array($part1, $part2), array($this->fixture->getPart(0), $this->fixture->getPart(1)));
  }

  #[@test]
  public function setBody_sets_first_part() {
    $this->fixture->setBody('Test');
    $this->assertEquals(new MimePart('Test', 'text/plain'), $this->fixture->getPart(0));
  }

  #[@test]
  public function setBody_removes_previously_added_parts() {
    $this->fixture->addPart(new MimePart());
    $this->fixture->setBody('Test');
    $this->assertEquals(new MimePart('Test', 'text/plain'), $this->fixture->getPart(0));
  }

  #[@test]
  public function getBody_for_two_parts() {
    $this->fixture->setBoundary('------=_Part_4711Test');
    $this->fixture->addPart(new MimePart('Test', 'text/plain'));
    $this->fixture->addPart(new MimePart('GIF89aXXXX', 'image/gif', '8bit', 'test.gif'));
    $this->assertEquals(
      "This is a multi-part message in MIME format.\n".
      "\n".
      "--------=_Part_4711Test\n".
      "Content-Type: text/plain; charset=\"iso-8859-1\"\n".
      "\n".
      "Test\n".
      "\n".
      "--------=_Part_4711Test\n".
      "Content-Type: image/gif; name=test.gif\n".
      "Content-Transfer-Encoding: 8bit\n".
      "Content-Disposition: attachment; filename=\"test.gif\"\n".
      "\n".
      "GIF89aXXXX\n".
      "\n".
      "--------=_Part_4711Test--\n",
      $this->fixture->getBody()
    );
  }
}