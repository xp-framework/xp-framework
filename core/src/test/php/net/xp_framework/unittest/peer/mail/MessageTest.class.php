<?php namespace net\xp_framework\unittest\peer\mail;

use util\Date;
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

  /**
   * Returns recipient types
   *
   * @return string[]
   */
  protected function recipientTypes() {
    return array('to', 'cc', 'bcc');
  }

  #[@test, @values('recipientTypes')]
  public function getRecipient_for_single_recipient($type) {
    $r= new InternetAddress('thekid@example.com');
    $this->fixture->addRecipient($type, $r);
    $this->assertEquals($r, $this->fixture->getRecipient($type));
  }

  #[@test, @values('recipientTypes')]
  public function getRecipient_for_multiple_recipients($type) {
    $r1= new InternetAddress('thekid@example.com');
    $r2= new InternetAddress('alex@example.com');
    $this->fixture->addRecipient($type, $r1);
    $this->fixture->addRecipient($type, $r2);
    $this->assertEquals($r1, $this->fixture->getRecipient($type));
    $this->assertEquals($r2, $this->fixture->getRecipient($type));
  }

  #[@test, @values('recipientTypes')]
  public function getRecipients_initially_returns_empty_array($type) {
    $this->assertEquals(array(), $this->fixture->getRecipients($type));
  }

  #[@test, @values('recipientTypes')]
  public function getRecipients_returns_recipients_added_via_addRecipient($type) {
    $r1= new InternetAddress('thekid@example.com');
    $r2= new InternetAddress('alex@example.com');
    $this->fixture->addRecipient($type, $r1);
    $this->fixture->addRecipient($type, $r2);
    $this->assertEquals(array($r1, $r2), $this->fixture->getRecipients($type));
  }

  #[@test, @values('recipientTypes')]
  public function getRecipients_returns_recipients_added_via_addRecipients($type) {
    $r1= new InternetAddress('thekid@example.com');
    $r2= new InternetAddress('alex@example.com');
    $this->fixture->addRecipients($type, array($r1, $r2));
    $this->assertEquals(array($r1, $r2), $this->fixture->getRecipients($type));
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
  public function subject_accessors() {
    $this->fixture->setSubject('Hello World');
    $this->assertEquals('Hello World', $this->fixture->getSubject());
  }

  #[@test]
  public function message_id_accessors() {
    $this->fixture->setMessageId('1234');
    $this->assertEquals('1234', $this->fixture->getMessageId());
  }

  #[@test]
  public function date_accessors() {
    $d= Date::now();
    $this->fixture->setDate($d);
    $this->assertEquals($d, $this->fixture->getDate());
  }

  #[@test]
  public function encoding_accessors() {
    $this->fixture->setEncoding('8bit');
    $this->assertEquals('8bit', $this->fixture->getEncoding());
  }

  #[@test]
  public function charset_accessors() {
    $this->fixture->setCharset('utf-8');
    $this->assertEquals('utf-8', $this->fixture->getCharset());
  }

  #[@test]
  public function content_type_accessors() {
    $this->fixture->setContentType('text/plain');
    $this->assertEquals('text/plain', $this->fixture->getContentType());
  }

  #[@test]
  public function mime_version_accessors() {
    $this->fixture->setMimeVersion('1.0');
    $this->assertEquals('1.0', $this->fixture->getMimeVersion());
  }

  #[@test]
  public function unencoded_body() {
    $this->fixture->setBody('Hello World');
    $this->assertEquals('Hello World', $this->fixture->getBody());
  }

  #[@test]
  public function base64_encoded_body() {
    $this->fixture->setBody('SGVsbG8gV29ybGQ=');
    $this->fixture->setEncoding('base64');
    $this->assertEquals('Hello World', $this->fixture->getBody(true));
  }

  #[@test]
  public function quoted_printable_encoded_body() {
    $this->fixture->setBody('Hello_World');
    $this->fixture->setEncoding('quoted-printable');
    $this->assertEquals('Hello World', $this->fixture->getBody(true));
  }
}