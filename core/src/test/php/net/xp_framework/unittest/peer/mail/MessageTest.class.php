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
}