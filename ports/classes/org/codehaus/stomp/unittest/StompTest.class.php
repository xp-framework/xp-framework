<?php
  uses(
    'unittest.TestCase',
    'org.codehaus.stomp.StompConnection',
    'io.streams.StringReader',
    'io.streams.StringWriter',
    'io.streams.MemoryInputStream',
    'io.streams.MemoryOutputStream'
  );

  class StompTest extends TestCase {
    protected $fixture= NULL;

    /**
     * Sets up unittest and creates fixture
     *
     */
    public function setUp() {
      $this->fixture= newinstance('org.codehaus.stomp.StompConnection', array('localhost', 61616), '{
        protected $response= "";
        protected $sent= NULL;

        public function __construct($server, $port) {
          parent::__construct($server, $port);

          // FIXME: Required for unittest
          $this->_connect();
        }

        protected function _connect() {
          $this->in= new StringReader(new MemoryInputStream($this->response));
          $this->out= new StringWriter(new MemoryOutputStream());
        }

        protected function _disconnect() {
          $this->sent= $this->out->getStream()->getBytes();
          $this->in= NULL;
          $this->out= NULL;
        }

        public function setResponseBytes($s) {
          $this->in= new StringReader(new MemoryInputStream($s));
          $this->response= $s;
        }

        public function readSentBytes() {

          // Case of DISCONNECT
          if (NULL !== $this->sent) {
            $sent= $this->sent;
            $this->sent= NULL;
            return $sent;
          }

          return $this->out->getStream()->getBytes();
        }
      }');
    }

    /**
     * Tests connect message
     *
     */
    #[@test]
    public function connect() {
      $this->fixture->setResponseBytes("CONNECTED\n".
        "session-id:0xdeadbeef\n".
        "\n\0"
      );
      $this->fixture->connect('user', 'pass');

      $this->assertEquals("CONNECT\n".
        "login:user\n".
        "passcode:pass\n".
        "\n\0",
        $this->fixture->readSentBytes()
      );
    }

    /**
     * Tests connect message when login fails
     *
     */
    #[@test, @expect('peer.AuthenticationException')]
    public function loginFailed() {
      $this->fixture->setResponseBytes("ERROR\n".
        "message: Invalid credentials\n".
        "\n\0"
      );
      $this->fixture->connect('user', 'pass');
    }
    
    /**
     * Tests send message
     *
     */
    #[@test]
    public function sendFrame() {
      $this->fixture->setResponseBytes("RECEIPT\n".
        "receipt-id:message-id\n".
        "\n\0"
      );

      $this->fixture->sendFrame(new org·codehaus·stomp·frame·SendFrame('/queue/a', 'my-data'));
      $this->assertEquals("SEND\n".
        "destination:/queue/a\n".
        "content-length:7\n".
        "\nmy-data\0",
        $this->fixture->readSentBytes()
      );
      $response= $this->fixture->receive();

      $this->assertTrue($response instanceof org·codehaus·stomp·frame·ReceiptFrame);
    }

    /**
     * Tests error message
     *
     */
    #[@test]
    public function receiveError() {
      $this->fixture->setResponseBytes("ERROR\n".
        "message:Unknown command\n".
        "\n".
        "Line1\nLine2\0");

      $response= $this->fixture->recvFrame();
      $this->assertEquals('Unknown command', $response->getHeader('message'));
      $this->assertEquals("Line1\nLine2", $response->getBody());
    }

    /**
     * Tests error message w/ content-length
     *
     */
    #[@test]
    public function receiveErrorWithContentLengthGiven() {
      $this->fixture->setResponseBytes("ERROR\n".
        "message:Unknown command\n".
        "code:message:unknown\n".
        "content-length:11\n".
        "\n".
        "Line1\nLine2\0\n");

      $response= $this->fixture->recvFrame();
      $this->assertEquals('Unknown command', $response->getHeader('message'));
      $this->assertEquals('message:unknown', $response->getHeader('code'));
      $this->assertEquals("Line1\nLine2", $response->getBody());
    }

    /**
     * Tests message with invalid content-length
     *
     */
    #[@test, @expect('peer.ProtocolException')]
    public function catchInvalidContentLength() {
      $this->fixture->setResponseBytes("ERROR\n".
        "message:Unknown command\n".
        "content-length:10\n".
        "\n".
        "Content longer that 10 bytes.\0"
      );
      $response= $this->fixture->recvFrame();
    }

    /**
     * Helper
     *
     * @param   org.codehaus.stomp.frame.Frame fram
     */
    protected function sendWithReceiptFrame(org·codehaus·stomp·frame·Frame $frame) {
      $this->fixture->setResponseBytes("RECEIPT\n".
        "receipt-id:message-id\n".
        "\n\0"
      );

      return $this->fixture->sendFrame($frame);
    }

    /**
     * Tests subscription
     *
     */
    #[@test]
    public function subscribe() {
      $this->sendWithReceiptFrame(new org·codehaus·stomp·frame·SubscribeFrame('/queue/a'));
      $this->assertEquals("SUBSCRIBE\n".
        "destination:/queue/a\n".
        "ack:auto\n".
        "\n".
        "\0",
        $this->fixture->readSentBytes()
      );
    }

    /**
     * Tests subscription
     *
     */
    #[@test]
    public function unsubscribe() {
      $this->sendWithReceiptFrame(new org·codehaus·stomp·frame·UnsubscribeFrame('/queue/a'));
      $this->assertEquals("UNSUBSCRIBE\n".
        "destination:/queue/a\n".
        "\n".
        "\0",
        $this->fixture->readSentBytes()
      );
    }

    /**
     * Tests beginning a transaction
     *
     */
    #[@test]
    public function beginTransaction() {
      $this->sendWithReceiptFrame(new org·codehaus·stomp·frame·BeginFrame('my-transaction'));
      $this->assertEquals("BEGIN\n".
        "transaction:my-transaction\n\n\0"
        , $this->fixture->readSentBytes()
      );
    }

    /**
     * Tests aborting a transaction
     *
     */
    #[@test]
    public function abortTransaction() {
      $this->sendWithReceiptFrame(new org·codehaus·stomp·frame·AbortFrame('my-transaction'));
      $this->assertEquals("ABORT\n".
        "transaction:my-transaction\n\n\0"
        , $this->fixture->readSentBytes()
      );
    }

    /**
     * Tests committing a transaction
     *
     */
    #[@test]
    public function commitTransaction() {
      $this->sendWithReceiptFrame(new org·codehaus·stomp·frame·CommitFrame('my-transaction'));
      $this->assertEquals("COMMIT\n".
        "transaction:my-transaction\n\n\0"
        , $this->fixture->readSentBytes()
      );
    }

    /**
     * Tests ack message
     *
     */
    #[@test]
    public function ack() {
      $this->sendWithReceiptFrame(new org·codehaus·stomp·frame·AckFrame('0xefefef'));
      $this->assertEquals("ACK\n".
        "message-id:0xefefef\n".
        "\n\0"
        , $this->fixture->readSentBytes()
      );
    }

    /**
     * Tests ack message
     *
     */
    #[@test]
    public function ackWithinTransaction() {
      $this->sendWithReceiptFrame(new org·codehaus·stomp·frame·AckFrame('0xefefef', "some-transaction"));
      $this->assertEquals("ACK\n".
        "message-id:0xefefef\n".
        "transaction:some-transaction\n".
        "\n\0"
        , $this->fixture->readSentBytes()
      );
    }

    /**
     * Tests disconnect
     *
     */
    #[@test]
    public function disconnect() {
      $this->fixture->disconnect();

      $this->assertEquals("DISCONNECT\n\n\0", $this->fixture->readSentBytes());
    }
  }
?>
