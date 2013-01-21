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
   * @see      xp://remote.protocol.ProtocolHandler
   * @purpose  Protocol Handler
   */
  class XpProtocolHandler extends Object implements ProtocolHandler {
    public
      $versionMajor   = 0,
      $versionMinor   = 0,
      $serializer     = NULL,
      $cat            = NULL,
      $timeout        = 60;
    
    public
      $_sock= NULL;  

    /**
     * Constructor
     *
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
     * Create a string representation of a given value
     *
     * @param   var value
     * @return  string
     */
    protected function stringOf($value) {
      if ($value instanceof Proxy) {
        $s= 'Proxy<';
        foreach ($value->getClass()->getInterfaces() as $iface) {
          $s.= $iface->getName().', ';
        }
        return substr($s, 0, -2).'>';
      }
      return xp::stringOf($value);
    }

    /**
     * Initialize this protocol handler
     *
     * @param   peer.URL proxy
     */
    public function initialize($proxy) {
      sscanf(
        $proxy->getParam('version', '1.0'), 
        '%d.%d', 
        $this->versionMajor, 
        $this->versionMinor
      );
      $this->_sock= new BSDSocket($proxy->getHost('localhost'), $proxy->getPort(6448));
      $this->_sock->setOption(getprotobyname('tcp'), TCP_NODELAY, TRUE);
      // Set default timeout
      $this->_sock->setTimeout($this->timeout);
      // Set sporty timeout for connect
      $this->_sock->connect(2.0);

      if ($user= $proxy->getUser()) {
        $this->cat && $this->cat->debugf(
          '>>> %s(%s:%d) INITIALIZE %s',
          $this->getClassName(),
          $this->_sock->host,
          $this->_sock->port,
          $user
        );
        $r= $this->sendPacket(REMOTE_MSG_INIT, "\1", array(
          new ByteCountedString($proxy->getUser()),
          new ByteCountedString($proxy->getPassword())
        ));
      } else {
        $this->cat && $this->cat->debugf(
          '>>> %s(%s:%d) INITIALIZE',
          $this->getClassName(),
          $this->_sock->host,
          $this->_sock->port
        );
        $r= $this->sendPacket(REMOTE_MSG_INIT, "\0");
      }

      $this->cat && $this->cat->debugf('<<< %s', $this->stringOf($r));
    }
    
    /**
     * Returns a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'(->'.$this->_sock->host.':'.$this->_sock->port.')';
    }
    
    /**
     * Look up an object by its name
     *
     * @param   string name
     * @param   lang.Object
     */
    public function lookup($name) {
      $this->cat && $this->cat->debugf(
        '>>> %s(%s:%d) LOOKUP %s',
        $this->getClassName(),
        $this->_sock->host,
        $this->_sock->port,
        $name
      );
      $r= $this->sendPacket(REMOTE_MSG_LOOKUP, '', array(new ByteCountedString($name)));
      $this->cat && $this->cat->debugf('<<< %s', $this->stringOf($r));
      return $r;
    }

    /**
     * Begin a transaction
     *
     * @param   remote.UserTransaction tran
     * @param   bool
     */
    public function begin($tran) {
      $this->cat && $this->cat->debugf(
        '>>> %s(%s:%d) BEGIN %s',
        $this->getClassName(),
        $this->_sock->host,
        $this->_sock->port,
        $this->stringOf($tran)
      );
      $r= $this->sendPacket(REMOTE_MSG_TRAN_OP, pack('N', REMOTE_TRAN_BEGIN));
      $this->cat && $this->cat->debugf('<<< %s', $this->stringOf($r));
      return $r;
    }

    /**
     * Rollback a transaction
     *
     * @param   remote.UserTransaction tran
     * @param   bool
     */
    public function rollback($tran) {
      $this->cat && $this->cat->debugf(
        '>>> %s(%s:%d) ROLLBACK %s',
        $this->getClassName(),
        $this->_sock->host,
        $this->_sock->port,
        $this->stringOf($tran)
      );
      $r= $this->sendPacket(REMOTE_MSG_TRAN_OP, pack('N', REMOTE_TRAN_ROLLBACK));
      $this->cat && $this->cat->debugf('<<< %s', $this->stringOf($r));
      return $r;
    }

    /**
     * Commit a transaction
     *
     * @param   remote.UserTransaction tran
     * @param   bool
     */
    public function commit($tran) {
      $this->cat && $this->cat->debugf(
        '>>> %s(%s:%d) COMMIT %s',
        $this->getClassName(),
        $this->_sock->host,
        $this->_sock->port,
        $this->stringOf($tran)
      );
      $r= $this->sendPacket(REMOTE_MSG_TRAN_OP, pack('N', REMOTE_TRAN_COMMIT));
      $this->cat && $this->cat->debugf('<<< %s', $this->stringOf($r));
      return $r;
    }

    /**
     * Invoke a method on a given object id with given method name
     * and given arguments
     *
     * @param   int oid
     * @param   string method
     * @param   var[] args
     * @return  var
     */
    public function invoke($oid, $method, $args) {
      $this->cat && $this->cat->debugf(
        '>>> %s(%s:%d) %d::%s(%s)',
        $this->getClassName(),
        $this->_sock->host,
        $this->_sock->port,
        $oid,
        $method,
        $this->stringOf($args)
      );
      $r= $this->sendPacket(
        REMOTE_MSG_CALL, 
        pack('NN', 0, $oid),
        array(
          new ByteCountedString($method),
          new ByteCountedString($this->serializer->representationOf(ArrayList::newInstance($args)))
        )
      );
      $this->cat && $this->cat->debugf('<<< %s', $this->stringOf($r));
      return $r;
    }

    /**
     * Sends a packet, reads and evaluates the response
     *
     * @param   int type
     * @param   string data default ''
     * @return  var
     * @throws  remote.RemoteException for server errors
     * @throws  lang.Error for unrecoverable errors
     */
    protected function sendPacket($type, $data= '', $bytes= array()) {
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

      try {
        $this->_sock->write($packet);
        $this->cat && $this->cat->debug('>>> Request:', $this->stringOf($bytes));
        for ($i= 0; $i < $bsize; $i++) {
          $bytes[$i]->writeTo($this->_sock);
        }
        $header= unpack(
          'Nmagic/cvmajor/cvminor/ctype/ctran/Nlength', 
          $this->readBytes(12)
        );
      } catch (IOException $e) {
        throw new RemoteException($e->getMessage(), $e);
      }
      
      if (DEFAULT_PROTOCOL_MAGIC_NUMBER != $header['magic']) {
        $this->_sock->close();
        throw new Error('Magic number mismatch (have: '.$header['magic'].' expect: '.DEFAULT_PROTOCOL_MAGIC_NUMBER);
      }

      // Perform actions based on response type
      $ctx= array('handler' => $this);
      try {
        switch ($header['type']) {
          case REMOTE_MSG_VALUE:
            $data= ByteCountedString::readFrom($this->_sock);
            $this->cat && $this->cat->debug('<<< Response:', addcslashes($data, "\0..\37!@\177..\377"));
            return $this->serializer->valueOf(new SerializedData($data), $ctx);

          case REMOTE_MSG_EXCEPTION:
            $data= ByteCountedString::readFrom($this->_sock);
            $this->cat && $this->cat->debug('<<< Response:', addcslashes($data, "\0..\37!@\177..\377"));
            $reference= $this->serializer->valueOf(new SerializedData($data), $ctx);
            if ($reference instanceof RemoteException) {
              throw $reference;
            } else if ($reference instanceof ExceptionReference) {
              throw new RemoteException($reference->getMessage(), $reference);
            } else {
              throw new RemoteException('lang.XPException', new XPException($this->stringOf($reference)));
            }

          case REMOTE_MSG_ERROR:
            $message= ByteCountedString::readFrom($this->_sock);    // Not serialized!
            $this->cat && $this->cat->debug('<<< Response:', addcslashes($message, "\0..\37!@\177..\377"));
            $this->_sock->close();
            throw new RemoteException($message, new Error($message));

          default:
            $data= $this->readBytes($header['length']);   // Read all left-over bytes
            $this->cat && $this->cat->debug('<<< Response:', addcslashes($data, "\0..\37!@\177..\377"));
            $this->_sock->close();
            throw new Error('Unknown message type '.xp::stringOf($header['type']));
        }
      } catch (IOException $e) {
        throw new RemoteException($e->getMessage(), $e);
      }
    }
    
    /**
     * Read a specified number of bytes
     *
     * @param   int num
     * @return  string 
     */
    protected function readBytes($num) {
      $return= '';
      while (strlen($return) < $num) {
        if (0 == strlen($buf= $this->_sock->readBinary($num - strlen($return)))) return;
        $return.= $buf;
      }
      return $return;
    }

    /**
     * Set trace
     *
     * @param   util.log.LogCategory cat
     */
    public function setTrace($cat) {
      $this->cat= $cat;
    }

    /**
     * Set timeout
     *
     * @param double timeout 
     */
    public function setTimeout($timeout) {
      $this->timeout= $timeout;
    }
  } 
?>
