<?php namespace net\xp_framework\unittest\peer\mail;

use peer\mail\Message;
use peer\mail\InternetAddress;

/**
 * Tests Message class
 */
class MessageTest extends \unittest\TestCase {

  #[@test]
  public function unencoded_body() {
    $m= new Message();
    $m->setBody('Hello World');
    $this->assertEquals('Hello World', $m->getBody());
  }
}