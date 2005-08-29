<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'peer.Socket',
    'com.sun.UTF', 
    'Serializer', 
    'RemoteInvocationHandler', 
    'RemoteInterfaceMapping',
    'RemoteException'
  );


  define('DEFAULT_PROTOCOL_MAGIC_NUMBER', 0x3c872747);

  // Request messages
  define('REMOTE_MSG_INIT',      0x0000);
  define('REMOTE_MSG_LOOKUP',    0x0001);
  define('REMOTE_MSG_CALL',      0x0002);
  define('REMOTE_MSG_FINALIZE',  0x0003);
  
  // Response messages
  define('REMOTE_MSG_VALUE',     0x0004);
  define('REMOTE_MSG_EXCEPTION', 0x0005);
  define('REMOTE_MSG_ERROR',     0x0006);

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class XpProtocolHandler extends Object {
    var
      $versionMajor   = 0,
      $versionMinor   = 0;
    
    var
      $_sock= NULL;  

    function initialize(&$proxy) {
      sscanf(
        $proxy->getParam('version', '1.0'), 
        '%d.%d', 
        $this->versionMajor, 
        $this->versionMinor
      );
      $this->_sock= &new Socket($proxy->getHost('localhost'), $proxy->getPort(4448));
      $this->_sock->connect();
      $this->sendPacket(REMOTE_MSG_INIT);
    }
    
    function &lookup($name) {
      return $this->sendPacket(REMOTE_MSG_LOOKUP, UTF::encode($name));
    }

    function &invoke($oid, $method, $args) {
      return $this->sendPacket(REMOTE_MSG_CALL, pack(
        'NNa*a*',
        0,
        $oid,
        UTF::encode($method),
        UTF::encode(Serializer::representationOf($args))
      ));
    }

    /**
     * (Insert method's description here)
     *
     * @access  protected
     * @param   int type
     * @param   string data default ''
     * @return  &mixed
     * @throws  rmi.RemoteException
     */
    function sendPacket($type, $data= '') {
    
      // Write packet
      $packet= pack(
        'Nc4Na*', 
        DEFAULT_PROTOCOL_MAGIC_NUMBER, 
        $this->versionMajor,
        $this->versionMinor,
        $type,
        FALSE,                  // compressed
        strlen($data),
        $data
      );
      // Console::writeLine('>>> ', $packet);
      $this->_sock->write($packet);
      
      // Read response header
      $header= unpack(
        'Nmagic/cvmajor/cvminor/ctype/ccompressed/Nlength', 
        $this->readBytes(12)
      );
      // Console::writeLine('<<< ', xp::stringOf($header));
      
      if (DEFAULT_PROTOCOL_MAGIC_NUMBER != $header['magic']) {
        $this->_sock->close();
        return throw(new Error('Magic number mismatch (have: '.$header['magic'].' expect: '.DEFAULT_PROTOCOL_MAGIC_NUMBER));
      }
      
      $data= $this->readBytes($header['length']+ 2);
      $ctx= array('handler' => &$this);

      // Perform actions based on response type
      switch ($header['type']) {
        case REMOTE_MSG_VALUE:
          return Serializer::valueOf(UTF::decode($data), $length= 0, $ctx);
        
        case REMOTE_MSG_EXCEPTION:
          $e= &Serializer::valueOf(UTF::decode($data), $length= 0, $ctx);
          return throw(new RemoteException($e->classname, $e));
        
        case REMOTE_MSG_ERROR:
          $this->_sock->close();
          return throw(new Error(UTF::decode($data), $length= 0, $ctx));
        
        default:
          $this->_sock->close();
          return throw(new Error('Unknown message type'));
      }
    }
    
    /**
     * Read a specified number of byte from the given socket
     *
     * @access  protected
     * @param   &peer.Socket sock
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
  }
?>
