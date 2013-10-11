<?php namespace net\xp_framework\unittest\peer\mail;

use peer\mail\Message;
use peer\mail\InternetAddress;

/**
 * Tests Message class
 */
class MessageTest extends \unittest\TestCase {
  protected $fixture= null;

  /**
   * Setup
   */
  public function setUp() {
    $this->fixture= new Message();
  }

  #[@test]
  public function getRecipient_for_single_recipient() {
    $r= new InternetAddress('thekid@example.com');
    $this->fixture->addRecipient(TO, $r);
    $this->assertEquals($r, $this->fixture->getRecipient(TO));
  }

  #[@test]
  public function getRecipient_for_multiple_recipients() {
    $r1= new InternetAddress('thekid@example.com');
    $r2= new InternetAddress('alex@example.com');
    $this->fixture->addRecipient(TO, $r1);
    $this->fixture->addRecipient(TO, $r2);
    $this->assertEquals($r1, $this->fixture->getRecipient(TO));
    $this->assertEquals($r2, $this->fixture->getRecipient(TO));
  }

  #[@test]
  public function getRecipients_initially_returns_empty_array() {
    $this->assertEquals(array(), $this->fixture->getRecipients(TO));
  }

  #[@test]
  public function getRecipients_returns_added_recipients() {
    $r1= new InternetAddress('thekid@example.com');
    $r2= new InternetAddress('alex@example.com');
    $this->fixture->addRecipient(TO, $r1);
    $this->fixture->addRecipient(TO, $r2);
    $this->assertEquals(array($r1, $r2), $this->fixture->getRecipients(TO));
  }

  #[@test]
  public function getHeader_returns_null_if_header_doesnt_exist() {
    $this->assertNull($this->fixture->getHeader('X-Common-Header'));
  }

  #[@test]
  public function getHeader_returns_added_header() {
    $this->fixture->setHeader('X-Common-Header', 'test');
    $this->assertEquals('test', $this->fixture->getHeader('X-Common-Header'));
  }

  #[@test]
  public function default_headers_returned_by_getHeaderString() {
    $this->fixture->setHeader('X-Common-Header', 'test');
    $this->assertEquals(
      "X-Common-Header: test\n".
      "Content-Type: text/plain;\n".
      "\tcharset=\"iso-8859-1\"\n".
      "Mime-Version: 1.0\n".
      "Content-Transfer-Encoding: 8bit\n".
      "X-Priority: 3 (Normal)\n".
      "Date: ".$this->fixture->getDate()->toString('r')."\n",
      $this->fixture->getHeaderString()
    );
  }
  #[@test]
  public function unencoded_body() {
    $this->fixture->setBody('Hello World');
    $this->assertEquals('Hello World', $this->fixture->getBody());
  }
}