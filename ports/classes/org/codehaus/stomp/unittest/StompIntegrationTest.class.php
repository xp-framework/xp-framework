<?php
  uses(
    'unittest.TestCase',
    'org.codehaus.stomp.StompConnection',
    'util.log.Logger',
    'util.log.ColoredConsoleAppender',
    'org.codehaus.stomp.frame.MessageFrame'
  );

  class StompIntegrationTest extends TestCase {
    const QUEUE = '/queue/unittest';

    protected
      $fixture  = NULL;

    #[@beforeClass]
    public static function logger() {
      // Logger::getInstance()->getCategory()->addAppender(new ColoredConsoleAppender());
    }

    public function setUp() {
      $this->fixture= new StompConnection('localhost', 61613);
      $this->fixture->setTrace(Logger::getInstance()->getCategory());
      $this->fixture->connect('system', 'manager');
    }

    public function tearDown() {
      $this->fixture->disconnect();
    }

    #[@test, @ignore, @expect('peer.AuthenticationException')]
    public function invalidCredentials() {
      $conn= new StompConnection('localhost', 61613);
      $conn->connect('unknownuser', 'invalidpass');
    }

    #[@test]
    public function sendMessage() {
      $this->fixture->send(self::QUEUE, 'This is a text message');
    }

    #[@test]
    public function subscribeAndReceive() {
      $this->fixture->subscribe(self::QUEUE, 'client');

      $message= $this->fixture->receive();
      $this->assertTrue($message instanceof org·codehaus·stomp·frame·MessageFrame);
    }

    #[@test]
    public function receiveReceipt() {
      $frame= new org·codehaus·stomp·frame·SendFrame(self::QUEUE, 'body');
      $frame->addHeader('receipt', 'some-message-receipt');

      $response= $this->fixture->sendFrame($frame);
      $this->assertTrue($response instanceof org·codehaus·stomp·frame·ReceiptFrame);
      $this->assertEquals($frame->getHeader('receipt'), $response->getHeader('receipt-id'));
    }

    #[@test]
    public function emptyQueue() {
      $this->fixture->subscribe(self::QUEUE, 'client');

      while ($message= $this->fixture->receive()) {
        $this->fixture->ack($message->getHeader('message-id'));
      }
    }
  }
?>
