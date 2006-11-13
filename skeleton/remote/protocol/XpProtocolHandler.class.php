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
    'remote.protocol.XpProtocolConstants'
  );

  /**
   * Handles the "XP" protocol
   *
   * @see      xp://remote.protocol.ProtocolHandler
   * @purpose  Protocol Handler
   */
  class XpProtocolHandler extends Object {
    var
      $versionMajor   = 0,
      $versionMinor   = 0,
      $serializer     = NULL,
      $cat            = NULL;
    
    var
      $_sock= NULL;  

    /**
     * Constructor
     *
     * @access  public
     */
    function __construct() {
    
      // Create a serializer for this protocol handler
      $this->serializer= &new Serializer();
      
      // Register default mappings / exceptions / packages
      $this->serializer->mapping('I', new RemoteInterfaceMapping());
      $this->serializer->exceptionName('naming/NameNotFound', 'remote.NameNotFoundException');
      $this->serializer->exceptionName('invoke/Exception', 'remote.InvocationException');
      $this->serializer->packageMapping('net.xp_framework.easc.reflect', 'remote.reflect');
    }
    
    /**
     * Create a string representation of a given value
     *
     * @access  protected
     * @param   &mixed value
     * @return  string
     */
    function stringOf(&$value) {
      if (is_a($value, 'Proxy')) {
        $s= 'Proxy<';
        $c= get_class($value);
        $implements= xp::registry('implements');
        foreach (array_keys($implements[$c]) as $iface) {
          $s.= xp::nameOf($iface).', ';
        }
        return substr($s, 0, -2).'>';
      }
      return xp::stringOf($value);
    }

    /**
     * Initialize this protocol handler
     *
     * @access  public
     * @param   &peer.URL proxy
     */
    function initialize(&$proxy) {
      sscanf(
        $proxy->getParam('version', '1.0'), 
        '%d.%d', 
        $this->versionMajor, 
        $this->versionMinor
      );
      $this->_sock= &new BSDSocket($proxy->getHost('localhost'), $proxy->getPort(6448));
      $this->_sock->setOption(getprotobyname('tcp'), TCP_NODELAY, TRUE);
      $this->_sock->connect();
      
      if ($user= $proxy->getUser()) {
        $this->cat && $this->cat->infof(
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
        $this->cat && $this->cat->infof(
          '>>> %s(%s:%d) INITIALIZE',
          $this->getClassName(),
          $this->_sock->host,
          $this->_sock->port
        );
        $r= $this->sendPacket(REMOTE_MSG_INIT, "\0");
      }
      $this->cat && $this->cat->infof('<<< %s', $this->stringOf($r));
    }
    
    /**
     * Returns a string representation of this object
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return $this->getClassName().'(->'.$this->_sock->host.':'.$this->_sock->port.')';
    }
    
    /**
     * Look up an object by its name
     *
     * @access  public
     * @param   string name
     * @param   &lang.Object
     */
    function &lookup($name) {
      $this->cat && $this->cat->infof(
        '>>> %s(%s:%d) LOOKUP %s',
        $this->getClassName(),
        $this->_sock->host,
        $this->_sock->port,
        $name
      );
      $r= &$this->sendPacket(REMOTE_MSG_LOOKUP, '', array(new ByteCountedString($name)));
      $this->cat && $this->cat->infof('<<< %s', $this->stringOf($r));
      return $r;
    }

    /**
     * Begin a transaction
     *
     * @access  public
     * @param   &remote.UserTransaction tran
     * @param   bool
     */
    function begin(&$tran) {
      $this->cat && $this->cat->infof(
        '>>> %s(%s:%d) BEGIN %s',
        $this->getClassName(),
        $this->_sock->host,
        $this->_sock->port,
        $this->stringOf($tran)
      );
      $r= &$this->sendPacket(REMOTE_MSG_TRAN_OP, pack('N', REMOTE_TRAN_BEGIN));
      $this->cat && $this->cat->infof('<<< %s', $this->stringOf($r));
      return $r;
    }

    /**
     * Rollback a transaction
     *
     * @access  public
     * @param   &remote.UserTransaction tran
     * @param   bool
     */
    function rollback(&$tran) {
      $this->cat && $this->cat->infof(
        '>>> %s(%s:%d) ROLLBACK %s',
        $this->getClassName(),
        $this->_sock->host,
        $this->_sock->port,
        $this->stringOf($tran)
      );
      $r= &$this->sendPacket(REMOTE_MSG_TRAN_OP, pack('N', REMOTE_TRAN_ROLLBACK));
      $this->cat && $this->cat->infof('<<< %s', $this->stringOf($r));
      return $r;
    }

    /**
     * Commit a transaction
     *
     * @access  public
     * @param   &remote.UserTransaction tran
     * @param   bool
     */
    function commit(&$tran) {
      $this->cat && $this->cat->infof(
        '>>> %s(%s:%d) COMMIT %s',
        $this->getClassName(),
        $this->_sock->host,
        $this->_sock->port,
        $this->stringOf($tran)
      );
      $r= &$this->sendPacket(REMOTE_MSG_TRAN_OP, pack('N', REMOTE_TRAN_COMMIT));
      $this->cat && $this->cat->infof('<<< %s', $this->stringOf($r));
      return $r;
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
    function &invoke($oid, $method, $args) {
      $this->cat && $this->cat->infof(
        '>>> %s(%s:%d) %d::%s(%s)',
        $this->getClassName(),
        $this->_sock->host,
        $this->_sock->port,
        $oid,
        $method,
        $this->stringOf($args)
      );
      $r= &$this->sendPacket(
        REMOTE_MSG_CALL, 
        pack('NN', 0, $oid),
        array(
          new ByteCountedString($method),
          new ByteCountedString($this->serializer->representationOf(new ArrayList($args)))
        )
      );
      $this->cat && $this->cat->infof('<<< %s', $this->stringOf($r));
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
    function &sendPacket($type, $data= '', $bytes= array()) {
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

      try(); {
        $this->_sock->write($packet);
        $this->cat && $this->cat->debug('>>> Request:', $this->stringOf($bytes));
        for ($i= 0; $i < $bsize; $i++) {
          $bytes[$i]->writeTo($this->_sock);
        }
        $header= unpack(
          'Nmagic/cvmajor/cvminor/ctype/ctran/Nlength', 
          $this->readBytes(12)
        );
      } if (catch('IOException', $e)) {
        return throw(new RemoteException($e->getMessage(), $e));
      }
      
      if (DEFAULT_PROTOCOL_MAGIC_NUMBER != $header['magic']) {
        $this->_sock->close();
        return throw(new Error('Magic number mismatch (have: '.$header['magic'].' expect: '.DEFAULT_PROTOCOL_MAGIC_NUMBER));
      }

      // Perform actions based on response type
      $ctx= array('handler' => &$this);
      try(); {
        switch ($header['type']) {
          case REMOTE_MSG_VALUE:
            $data= &ByteCountedString::readFrom($this->_sock);
            $this->cat && $this->cat->debug('<<< Response:', addcslashes($data, "\0..\37!@\177..\377"));
            return $this->serializer->valueOf($data, $length= 0, $ctx);

          case REMOTE_MSG_EXCEPTION:
            $data= &ByteCountedString::readFrom($this->_sock);
            $this->cat && $this->cat->debug('<<< Response:', addcslashes($data, "\0..\37!@\177..\377"));
            $reference= &$this->serializer->valueOf($data, $length= 0, $ctx);
            if (is('RemoteException', $reference)) {
              return throw($reference);
            } else if (is('ExceptionReference', $reference)) {
              return throw(new RemoteException($reference->getMessage(), $reference));
            } else {
              return throw(new RemoteException('lang.Exception', new Exception($this->stringOf($reference))));
            }

          case REMOTE_MSG_ERROR:
            $message= ByteCountedString::readFrom($this->_sock);    // Not serialized!
            $this->cat && $this->cat->debug('<<< Response:', addcslashes($message, "\0..\37!@\177..\377"));
            $this->_sock->close();
            return throw(new RemoteException($message, new Error($message)));

          default:
            $data= &$this->readBytes($header['length']);   // Read all left-over bytes
            $this->cat && $this->cat->debug('<<< Response:', addcslashes($data, "\0..\37!@\177..\377"));
            $this->_sock->close();
            return throw(new Error('Unknown message type'));
        }
      } if (catch('IOException', $e)) {
        return throw(new RemoteException($e->getMessage(), $e));
      }
    }
    
    /**
     * Read a specified number of bytes
     *
     * @access  protected
     * @param   int num
     * @return  string 
     */
    function readBytes($num) {
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
     * @access  public
     * @param   &util.log.LogCategory cat
     */
    function setTrace(&$cat) {
      $this->cat= &$cat;
    }

  } implements(__FILE__, 'remote.protocol.ProtocolHandler', 'util.log.Traceable');
?>
