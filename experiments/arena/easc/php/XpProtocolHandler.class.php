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
   * Handles the "XP" protocol
   *
   * @see      xp://ProtocolHandler
   * @purpose  Protocol Handler
   */
  class XpProtocolHandler extends Object {
    var
      $versionMajor   = 0,
      $versionMinor   = 0;
    
    var
      $_sock= NULL;  

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
      $this->_sock= &new Socket($proxy->getHost('localhost'), $proxy->getPort(4448));
      $this->_sock->connect();
      $this->sendPacket(REMOTE_MSG_INIT);
    }
    
    /**
     * Look up an object by its name
     *
     * @access  public
     * @param   string name
     * @param   &lang.Object
     */
    function &lookup($name) {
      return $this->sendPacket(REMOTE_MSG_LOOKUP, UTF::encode($name));
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
      return $this->sendPacket(REMOTE_MSG_CALL, pack(
        'NNa*a*',
        0,
        $oid,
        UTF::encode($method),
        UTF::encode(Serializer::representationOf(new ArrayList($args)))
      ));
    }

    /**
     * Sends a packet, reads and evaluates the response
     *
     * @access  protected
     * @param   int type
     * @param   string data default ''
     * @return  &mixed
     * @throws  io.IOException in case of I/O errors
     */
    function sendPacket($type, $data= '') {
    
      // Write packet
      $packet= pack(
        'Nc4Na*', 
        DEFAULT_PROTOCOL_MAGIC_NUMBER, 
        $this->versionMajor,
        $this->versionMinor,
        $type,
        FALSE,                  // compressed, not used at the moment
        strlen($data),
        $data
      );
      // DEBUG Console::writeLine('>>>', addcslashes($packet, "\0..\37!@\177..\377"));

      try(); {
        $this->_sock->write($packet);
        $header= unpack(
          'Nmagic/cvmajor/cvminor/ctype/ccompressed/Nlength', 
          $this->readBytes(12)
        );
      } if (catch('IOException', $e)) {
        return throw($e);
      }
      
      // DEBUG Console::writeLine('<<<', xp::stringOf($header));
      
      if (DEFAULT_PROTOCOL_MAGIC_NUMBER != $header['magic']) {
        $this->_sock->close();
        return throw(new Error('Magic number mismatch (have: '.$header['magic'].' expect: '.DEFAULT_PROTOCOL_MAGIC_NUMBER));
      }
      
      try(); {
        $data= $this->readBytes($header['length']+ 2);
      } if (catch('IOException', $e)) {
        return throw($e);
      }

      // DEBUG Console::writeLine('<<<', addcslashes($data, "\0..\37!@\177..\377"));

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

  } implements(__FILE__, 'ProtocolHandler');
?>
