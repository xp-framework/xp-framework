<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'peer.BSDSocket',
    'remote.RemoteInvocationHandler',
    'remote.protocol.ByteCountedString',
    'remote.protocol.Serializer',
    'remote.protocol.RemoteInterfaceMapping',
    'remote.protocol.XpProtocolConstants',
    'remote.protocol.ProtocolHandler'
  );

  /**
   * Handles the "XP" protocol
   *
   * @see      xp://ProtocolHandler
   * @purpose  Protocol Handler
   */
  class XpProtocolHandler extends Object implements ProtocolHandler {
    public
      $versionMajor   = 0,
      $versionMinor   = 0,
      $serializer     = NULL;
    
    public
      $_sock= NULL;  

    /**
     * Constructor
     *
     * @access  public
     */
    public function __construct() {
    
      // Create a serializer for this protocol handler
      $this->serializer= new Serializer();
      
      // Register default mappings / exceptions / packages
      $this->serializer->mapping('I', new RemoteInterfaceMapping());
      $this->serializer->exceptionName('naming/NameNotFound', 'remote.NameNotFoundException');
      $this->serializer->exceptionName('invoke/Exception', 'remote.InvocationException');
      $this->serializer->packageMapping('net.xp_framework.easc.reflect', 'remote.reflect');
    }

    /**
     * Initialize this protocol handler
     *
     * @access  public
     * @param   &peer.URL proxy
     */
    public function initialize(&$proxy) {
      sscanf(
        $proxy->getParam('version', '1.0'), 
        '%d.%d', 
        $this->versionMajor, 
        $this->versionMinor
      );
      $this->_sock= new BSDSocket($proxy->getHost('localhost'), $proxy->getPort(6448));
      $this->_sock->setOption(getprotobyname('tcp'), TCP_NODELAY, TRUE);
      $this->_sock->connect();
      
      if ($user= $proxy->getUser()) {
        $this->sendPacket(REMOTE_MSG_INIT, "\1", array(
          new ByteCountedString($proxy->getUser()),
          new ByteCountedString($proxy->getPassword())
        ));
      } else {
        $this->sendPacket(REMOTE_MSG_INIT, "\0");
      }
    }
    
    /**
     * Returns a string representation of this object
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'(->'.$this->_sock->host.':'.$this->_sock->port.')';
    }
    
    /**
     * Look up an object by its name
     *
     * @access  public
     * @param   string name
     * @param   &lang.Object
     */
    public function &lookup($name) {
      return $this->sendPacket(REMOTE_MSG_LOOKUP, '', array(new ByteCountedString($name)));
    }

    /**
     * Begin a transaction
     *
     * @access  public
     * @param   UserTransaction tran
     * @param   bool
     */
    public function begin(&$tran) {
      return $this->sendPacket(REMOTE_MSG_TRAN_OP, pack('N', REMOTE_TRAN_BEGIN));
    }

    /**
     * Rollback a transaction
     *
     * @access  public
     * @param   UserTransaction tran
     * @param   bool
     */
    public function rollback(&$tran) {
      return $this->sendPacket(REMOTE_MSG_TRAN_OP, pack('N', REMOTE_TRAN_ROLLBACK));
    }

    /**
     * Commit a transaction
     *
     * @access  public
     * @param   UserTransaction tran
     * @param   bool
     */
    public function commit(&$tran) {
      return $this->sendPacket(REMOTE_MSG_TRAN_OP, pack('N', REMOTE_TRAN_COMMIT));
    }

    /**
     * Invoke a method on a given object id with given method name
     * and given arguments
     *
     * @access  public
     * @param   int oid
     * @param   string method
     * @param   mixed[] args
     * @return  &mixed
     */
    public function &invoke($oid, $method, $args) {
      $r= &$this->sendPacket(
        REMOTE_MSG_CALL, 
        pack('NN', 0, $oid),
        array(
          new ByteCountedString($method),
          new ByteCountedString($this->serializer->representationOf(new ArrayList($args)))
        )
      );
      return $r;
    }

    /**
     * Sends a packet, reads and evaluates the response
     *
     * @access  protected
     * @param   int type
     * @param   string data default ''
     * @return  &mixed
     * @throws  remote.RemoteException for server errors
     * @throws  lang.Error for unrecoverable errors
     */
    public function &sendPacket($type, $data= '', $bytes= array()) {
      $bsize= sizeof($bytes);
      
      // Calculate packet length
      $length= strlen($data);
      for ($i= 0; $i < $bsize; $i++) {
        $length+= $bytes[$i]->length();
      }
      
      // Write packet
      $packet= pack(
        'Nc4Na*', 
        DEFAULT_PROTOCOL_MAGIC_NUMBER, 
        $this->versionMajor,
        $this->versionMinor,
        $type,
        FALSE,
        $length,
        $data
      );
      Console::writeLine('>>>', addcslashes($packet, "\0..\37!@\177..\377"));

      try {
        $this->_sock->write($packet);
        for ($i= 0; $i < $bsize; $i++) {
          $bytes[$i]->writeTo($this->_sock);
        }
        $header= unpack(
          'Nmagic/cvmajor/cvminor/ctype/ctran/Nlength', 
          $this->readBytes(12)
        );
      } catch (IOException $e) {
        throw(new RemoteException($e->getMessage(), $e));
      }
      
      Console::writeLine('<<<', xp::stringOf($header));
      if (DEFAULT_PROTOCOL_MAGIC_NUMBER != $header['magic']) {
        $this->_sock->close();
        throw(new Error('Magic number mismatch (have: '.$header['magic'].' expect: '.DEFAULT_PROTOCOL_MAGIC_NUMBER));
      }

      // Perform actions based on response type
      $ctx= array('handler' => &$this);
      try {
        switch ($header['type']) {
          case REMOTE_MSG_VALUE:
            $data= &ByteCountedString::readFrom($this->_sock);
            Console::writeLine('<<<', addcslashes($data, "\0..\37!@\177..\377"));
            return $this->serializer->valueOf($data, $length= 0, $ctx);

          case REMOTE_MSG_EXCEPTION:
            $reference= &$this->serializer->valueOf(ByteCountedString::readFrom($this->_sock), $length= 0, $ctx);
            if (is('RemoteException', $reference)) {
              throw($reference);
            } else if (is('ClassReference', $reference)) {
              throw(new RemoteException($reference->getClassName(), $reference));
            } else {
              throw(new RemoteException('lang.Exception', new XPException(xp::stringOf($reference))));
            }

          case REMOTE_MSG_ERROR:
            $message= ByteCountedString::readFrom($this->_sock);    // Not serialized!
            $this->_sock->close();
            throw(new RemoteException($message, new Error($message)));

          default:
            $this->readBytes($header['length']);   // Read all left-over bytes
            $this->_sock->close();
            throw(new Error('Unknown message type'));
        }
      } catch (IOException $e) {
        throw(new RemoteException($e->getMessage(), $e));
      }
    }
    
    /**
     * Read a specified number of bytes
     *
     * @access  protected
     * @param   int num
     * @return  string 
     */
    public function readBytes($num) {
      $return= '';
      while (strlen($return) < $num) {
        if (0 == strlen($buf= $this->_sock->readBinary($num - strlen($return)))) return;
        $return.= $buf;
      }
      return $return;
    }

  } 
?>
