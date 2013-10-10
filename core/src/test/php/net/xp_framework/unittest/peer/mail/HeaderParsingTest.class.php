<?php namespace net\xp_framework\unittest\peer\mail;

use peer\mail\Message;
use peer\mail\InternetAddress;

/**
 * Tests header parsing - Message::setHeaderString()
 */
class HeaderParsingTest extends \unittest\TestCase {

  /**
   * Parse a header
   *
   * @param  string $str
   * @return peer.mail.Message
   */
  protected function parse($str) {
    $m= new Message();
    $m->setHeaderString($str."\n\n");
    return $m;
  }

  #[@test]
  public function from_email() {
    $this->assertEquals(
      new InternetAddress('a@example.com'),
      $this->parse('From: a@example.com')->getFrom()
    );
  }

  #[@test, @values([
  #  ['to', 'To: b@example.com'],
  #  ['cc', 'Cc: b@example.com']
  #])]
  public function recipient_email($type, $header) {
    $this->assertEquals(
      array(new InternetAddress('b@example.com')),
      $this->parse($header)->getRecipients($type)
    );
  }

  #[@test, @values([
  #  ['to', 'To: a@example.com, b@example.com'],
  #  ['cc', 'Cc: a@example.com, b@example.com']
  #])]
  public function recipient_emails_separated_by_commas($type, $header) {
    $this->assertEquals(
      array(new InternetAddress('a@example.com'), new InternetAddress('b@example.com')),
      $this->parse($header)->getRecipients($type)
    );
  }

  #[@test, @values([
  #  ['to', "To: a@example.com\nTo: b@example.com"],
  #  ['cc', "Cc: a@example.com\nCc: b@example.com"]
  #])]
  public function multiple_recipient_headers($type, $header) {
    $this->assertEquals(
      array(new InternetAddress('a@example.com'), new InternetAddress('b@example.com')),
      $this->parse($header)->getRecipients($type)
    );
  }

  #[@test, @values([
  #  ['to', 'To: A <a@example.com>, B <b@example.com>'],
  #  ['cc', 'Cc: A <a@example.com>, B <b@example.com>']
  #])]
  public function recipient_emails_with_names($type, $header) {
    $this->assertEquals(
      array(new InternetAddress('a@example.com', 'A'), new InternetAddress('b@example.com', 'B')),
      $this->parse($header)->getRecipients($type)
    );
  }

  #[@test, @values([
  #  ['to', 'To: "A, B" <a@example.com>, "B, A" <b@example.com>'],
  #  ['cc', 'Cc: "A, B" <a@example.com>, "B, A" <b@example.com>']
  #])]
  public function recipient_emails_with_quoted_names($type, $header) {
    $this->assertEquals(
      array(new InternetAddress('a@example.com', 'A, B'), new InternetAddress('b@example.com', 'B, A')),
      $this->parse($header)->getRecipients($type)
    );
  }
}