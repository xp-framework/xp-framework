<?php
/* This class is part of the XP framework
 *
 * $Id: EascProtocol.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace remote::server;

  uses(
    'remote.protocol.ByteCountedString',
    'remote.protocol.Serializer',
    'remote.protocol.RemoteInterfaceMapping',
    'remote.server.ServerHandler',
    'remote.server.RemoteObjectMap',
    'io.sys.ShmSegment',
    'peer.server.ServerProtocol'
  );
  
  /**
   * EASC protocol handler
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class EascProtocol extends lang::Object implements peer::server::ServerProtocol {
    public
      $serializer  = NULL,
      $context     = NULL,
      $scanner     = NULL;

    /**
     * Constructor
     *
     * @param   remote.server.deploy.scan.FileSystemScanner scanner
     */
    public function __construct($scanner) {
      $this->serializer= new remote::protocol::Serializer();
      $this->serializer->mapping('I', new remote::protocol::RemoteInterfaceMapping());
      $this->context[RemoteObjectMap::CTX_KEY]= new RemoteObjectMap();
      $this->scanner= $scanner;

      $this->deployer= new ();
    }

    /**
     * Initialize protocol
     *
     * @return  bool
     */
    public function initialize() {
      if ($this->scanner->scanDeployments()) {
        foreach ($this->scanner->getDeployments() as $deployment) {
          try {
            $this->deployer->deployBean($deployment);
          } catch (remote::server::deploy::DeployException $e) {
            // Fall through
          }
        }
      }
      return TRUE; 
    }

    /**
     * Write answer
     *
     * @param   io.Stream stream
     * @param   int type
     * @param   mixed data
     */
    protected function answer($stream, $type, $data) {
      $length= strlen($data);
      $packet= pack(
        'Nc4Na*', 
        0x3c872747, 
        1,
        0,
        $type,
        FALSE,
        $length,
        $data
      );
      $stream->write($packet);
    }

    /**
     * Write answer
     *
     * @param   io.Stream stream
     * @param   int type
     * @param   remote.protocol.ByteCountedString[] bcs
     */
    protected function answerWithBytes($stream, $type, $bcs) {
      $header= pack(
        'Nc4Na*', 
        0x3c872747, 
        1,
        0,
        $type,
        FALSE,
        $bcs->length(),
        ''
      );
      
      // Console::writeLine('Header: ', addcslashes($header, "\0..\37!@\177..\377"));
      $stream->write($header);
      // Console::writeLine('Body: ', xp::stringOf($bcs));
      $bcs->writeTo($stream);
    }
    
    /**
     * Write answer
     *
     * @param   io.Stream stream
     * @param   remote.server.message.EascMessage message
     */
    public function answerWithMessage($stream, $m) {
      $this->answerWithBytes(
        $stream,
        $m->getType(),
        new remote::protocol::ByteCountedString($this->serializer->representationOf($m->getValue(), $this->context))
      );
    }

    /**
     * Read bytes from socket
     *
     * @param   peer.Socket sock
     * @param   int num
     * @return  string
     */
    protected function readBytes($sock, $num) {
      $return= '';
      while (strlen($return) < $num) {
        if (0 == strlen($buf= $sock->readBinary($num - strlen($return)))) return;
        $return.= $buf;
      }
      return $return;
    }
    
    /**
     * Extract a string out of packed data
     *
     * @param   string data
     * @param   int offset
     * @return  string
     */
    public function readString($data, &$offset) {
      $string= '';
      do {
        $ctl= unpack('nlength/cnext', substr($data, $offset, 4));
        $string.= substr($data, $offset+ 3, $ctl['length']);
        $offset+= $ctl['length']+ 1;
      } while ($ctl['next']);

      return utf8_decode($string);
    }    
    
    /**
     * Handle client connect
     *
     * @param   peer.Socket socket
     */
    public function handleConnect($socket) { }

    /**
     * Handle client disconnect
     *
     * @param   peer.Socket socket
     */
    public function handleDisconnect($socket) { }
  
    /**
     * Handle client data
     *
     * @param   peer.Socket socket
     * @return  mixed
     */
    public function handleData($socket) {
      $header= unpack(
        'Nmagic/cvmajor/cvminor/ctype/ctran/Nlength', 
        $this->readBytes($socket, 12)
      );

      if (0x3c872747 != $header['magic']) {
        $this->answer($socket, 0x0007 /* REMOTE_MSG_ERROR */, 'Magic number mismatch');
        return NULL;
      }
      
      $impl= new ServerHandler();
      $impl->setSerializer($this->serializer);
      
      return $impl->handle($socket, $this, $header['type'], $this->readBytes($socket, $header['length']));
    }
    
    /**
     * Handle I/O error
     *
     * @param   peer.Socket socket
     * @param   lang.Exception e
     */
    public function handleError($socket, $e) { }

  } 
?>
