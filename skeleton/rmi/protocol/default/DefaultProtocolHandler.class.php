<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'peer.Socket', 
    'rmi.protocol.default.PacketMarshaller', 
    'rmi.protocol.default.Serializer', 
    'com.sun.UTF', 
    'rmi.NoSuchObjectException',
    'rmi.NoSuchOperationException',
    'rmi.InvocationException',
    'rmi.protocol.ProtocolFormatException'
  );
  
  /**
   * Protocol handler
   *
   * @see      xp://rmi.protocol.ProtocolHandler
   * @purpose  Protocol Handler
   */
  class DefaultProtocolHandler extends Object {
    var
      $_sock        = NULL,
      $_marshaller  = NULL,
      $_oid         = '';
    
    var
      $options      = '';
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string options
     */
    function __construct($options) {
      $this->options= $options;
    }

    /**
     * Initialize
     *
     * @access  package
     * @param   &peer.URL url
     * @return  string fully qualified interface name
     */
    function initializeFor(&$url, &$context) {
      
      // Set up socket
      $this->_sock= &new Socket($url->getHost('localhost'), $url->getPort(4444));
      $this->_sock->connect();
      
      // Set up marshaller
      $this->_marshaller= &new PacketMarshaller('1.0');
      
      // Send initialize message
      try(); {
        $r= $this->sendPacket(REMOTE_MSG_INIT, (
          UTF::encode(trim($url->getPath(), '/')).
          UTF::encode(Serializer::representationOf($context))
        ));
      } if (catch('Throwable', $e)) {
        return throw($e);
      }

      if (2 != sscanf($r, '%[^=]=%s', $this->_oid, $interface)) {
        return throw(new IllegalArgumentException('Return "'.$r.'" invalid'));
      }

      // DEBUG Console::writeLine('OID {', addcslashes(xp::stringOf($this->_oid), "\0..\37!@\177..\377"), '}');
      register_shutdown_function(array(&$this, 'sendClosePacket'));
      
      return $interface;
    }
    
    /**
     * Create a new protocol handler
     *
     * @access  package
     * @param   string oid
     * @return  &rmi.protocol.default.DefaultProtocolHandler
     */
    function &newInstance($oid) {
      $instance= &new DefaultProtocolHandler();
      $instance->_sock= &$this->_sock;
      $instance->_marshaller= &$this->_marshaller;
      $instance->_oid= $oid;
      return $instance;
    }
    
    /**
     * Read a specified number of byte from the given socket
     *
     * @access  protected
     * @param   &peer.Socket sock
     * @param   int num
     * @return  string 
     */
    function readBytes(&$sock, $num) {
      $return= '';
      while (strlen($return) < $num) {
        if (0 == strlen($buf= $this->_sock->readBinary($num - strlen($return)))) return;
        $return.= $buf;
      }
      return $return;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  protected
     * @param   int type
     * @param   &mixed data
     * @return  &mixed
     * @throws  rmi.RemoteException
     */
    function sendPacket($type, $data) {
      $packet= $this->_marshaller->marshal($type, $data);
      // DEBUG Console::writeLine('>>> ', addcslashes($packet, "\0..\37!@\177..\377"));
      $this->_sock->write($packet, "\n");
      
      // Read header
      $header= $this->_marshaller->unmarshal($this->readBytes($this->_sock, 12));
      // DEBUG Console::writeLine('<<< header = ',  xp::stringOf($header));
      
      if ($header['magic'] != DEFAULT_PROTOCOL_MAGIC_NUMBER) {
        // XXX FIXME XXX Use ProtocolException?
        return throw(new FormatException('Illegal magic number "'.$header['magic'].'"'));
      }
      
      switch ($header['type']) {
        case REMOTE_MSG_CLOSE: {
          $this->_sock->close();
          return;
        }
        
        case REMOTE_MSG_REPLY: {
      
          // Read data
          $return= unpack('cstatus/a*data', $this->readBytes($this->_sock, 1 + $header['length']+ 2));
          // DEBUG Console::writeLine('<<< status = ', xp::stringOf($return['status']), ' / data = ',  xp::stringOf($return['data']));

          // Switch on returned value
          switch ($return['status']) {
            case 0:   // Success
              return Serializer::valueOf(UTF::decode($return['data']), $length, $this);
            
            case 1:   // User exception FIXME: Use real exception
              return throw(new RemoteException(UTF::decode($return['data'])));
            
            case 2:   // Object does not exist
              return throw(new NoSuchObjectException(UTF::decode($return['data'])));
            
            case 3:   // Method does not exist
              return throw(new NoSuchOperationException(UTF::decode($return['data'])));
            
            case 4:   // Method invokation error
              return throw(new InvocationException(UTF::decode($return['data'])));

            case 127: // Protocol error
              return throw(new ProtocolFormatException(UTF::decode($return['data'])));
            
            default:  // Any other status
              $this->_sock->close();
              return throw(new ProtocolFormatException('Unknown status '.$return['status']));
          }
        }
        
        default: {
          return throw(new ProtocolFormatException('Illegal message type "'.$header['type'].'"'));
        }
      }
      
      // Should never be reached
    }

    /**
     * Sends a close packet
     *
     * @access  protected
     */
    function sendClosePacket() {
      $this->sendPacket(REMOTE_MSG_CLOSE, pack('c', 0));
    }
    
    /**
     * Processes a method invocation on a proxy instance and returns
     * the result.
     *
     * @access  public
     * @param   lang.reflect.Proxy proxy
     * @param   string method the method name
     * @param   mixed* args an array of arguments
     * @return  mixed
     */
    function invoke(&$proxy, $method, $args) { 
      Console::writeLine('>>> '.$proxy->getClassName().'::'.$method.'('.xp::stringOf($args).')');
      
      $mode= 1;   // XXX FIXME XXX
      $return= $this->sendPacket(REMOTE_MSG_REQUEST, (
        pack('c', $mode).
        UTF::encode($this->_oid).
        UTF::encode($method).
        UTF::encode(serialize($args))
      ));
      
      return $return;
    }
  
  } implements(__FILE__, 'rmi.protocol.ProtocolHandler');
?>
