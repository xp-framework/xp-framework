<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
  uses(
    'util.log.Traceable',
    'peer.Socket',
    'peer.SocketInputStream',
    'peer.SocketOutputStream',
    'io.streams.MemoryOutputStream',
    'io.streams.TextReader',
    'io.streams.TextWriter',
    'peer.AuthenticationException',
    'peer.ProtocolException',
    'org.codehaus.stomp.frame.LoginFrame',
    'org.codehaus.stomp.frame.SendFrame',
    'org.codehaus.stomp.frame.SubscribeFrame',
    'org.codehaus.stomp.frame.UnsubscribeFrame',
    'org.codehaus.stomp.frame.BeginFrame',
    'org.codehaus.stomp.frame.CommitFrame',
    'org.codehaus.stomp.frame.AbortFrame',
    'org.codehaus.stomp.frame.AckFrame',
    'org.codehaus.stomp.frame.DisconnectFrame'
  );

  class StompConnection extends Object implements Traceable {
    protected
      $server = NULL,
      $port   = NULL;

    protected
      $socket = NULL,
      $in     = NULL,
      $out    = NULL;

    protected
      $cat    = NULL;

    public function __construct($server, $port) {
      $this->server= $server;
      $this->port= $port;
    }

    public function setTrace($cat) {
      $this->cat= $cat;
    }

    protected function _connect() {
      $this->socket= new Socket($this->server, $this->port);
      $this->socket->connect();

      $this->in= new StringReader(new SocketInputStream($this->socket));
      $this->out= new StringWriter(new SocketOutputStream($this->socket));
    }

    protected function _disconnect() {
      $this->out= NULL;
      $this->in= NULL;
      $this->socket->close();
    }

    public function recvFrame() {

      // Check whether we can read, before we actually read...
      if ($this->socket instanceof Socket && !$this->socket->canRead(0.2)) {
        $this->cat && $this->cat->debug($this->getClassName(), '<<<', '0 bytes - reading no frame.');
        return NULL;
      }

      $line= $this->in->readLine();
      $this->cat && $this->cat->debug($this->getClassName(), '<<<', 'Have "'.trim($line).'" command.');

      if (0 == strlen($line)) throw new ProtocolException('Expected frame token, got "'.xp::stringOf($line).'"');

      $frame= XPClass::forName(sprintf('org.codehaus.stomp.frame.%sFrame', ucfirst(strtolower(trim($line)))))
        ->newInstance()
      ;

      $frame->fromWire($this->in);
      return $frame;
    }

    public function sendFrame(org·codehaus·stomp·frame·Frame $frame) {

      // Trace
      if ($this->cat) {
        $mo= new MemoryOutputStream();
        $frame->write(new StringWriter($mo));

        $this->cat->debug($this->getClassName(), '>>>', $mo->getBytes());
      }

      $frame->write($this->out);

      if ($frame->requiresImmediateResponse()) {
        return $this->recvFrame();
      }

      return NULL;
    }

    public function connect($user, $pass) {
      $this->_connect();

      $frame= $this->sendFrame(new org·codehaus·stomp·frame·LoginFrame($user, $pass));
      if (!$frame instanceof org·codehaus·stomp·frame·Frame) {
        throw new ProtocolException('Did not receive frame, got: '.xp::stringOf($frame));
      }
      if (!$frame instanceof org·codehaus·stomp·frame·ConnectedFrame) {
        throw new AuthenticationException(
          'Could not log in to stomp broker "'.$this->server.':'.$this->port.'": Got "'.$frame->command().'" frame',
          $user,
          $pass
        );
      }

      return TRUE;
    }

    public function disconnect() {

      // Bail out if not connected
      if (!$this->out instanceof OutputStreamWriter) return;

      // Send disconnect frame and exit
      create(new org·codehaus·stomp·frame·DisconnectFrame())->write($this->out);
      $this->_disconnect();
    }

    public function begin($transaction) {
      return $this->sendFrame(new org·codehaus·stomp·frame·BeginFrame($transaction));
    }

    public function abort($transaction) {
      return $this->sendFrame(new org·codehaus·stomp·frame·AbortFrame($transaction));
    }

    public function commit($transaction) {
      return $this->sendFrame(new org·codehaus·stomp·frame·CommitFrame($transaction));
    }

    public function send($destination, $body) {
      return $this->sendFrame(new org·codehaus·stomp·frame·SendFrame($destination, $body));
    }

    public function subscribe($destination, $ackMode= org·codehaus·stomp·frame·SubscribeFrame::ACK_AUTO) {
      return $this->sendFrame(new org·codehaus·stomp·frame·SubscribeFrame($destination, $ackMode));
    }

    // TBI
    public function ack($messageId) {
      return $this->sendFrame(new org·codehaus·stomp·frame·AckFrame($messageId));

    }

    public function receive() {
      return $this->recvFrame();
    }
  }
?>
