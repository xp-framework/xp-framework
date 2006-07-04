<?php
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'peer.server.Server',
    'remote.protocol.ByteCountedString', 
    'remote.protocol.Serializer'
  );

  class RemoteInvocationListener extends ConnectionListener {

    /**
     * Method to be triggered when a client connects
     *
     * @access  public
     * @param   &peer.server.ConnectionEvent event
     */
    function connected(&$event) {
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function answer(&$stream, $type, $data) {
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
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function answerWithBytes(&$stream, $type, &$bcs) {
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
      $stream->write($header);
      $bcs->writeTo($stream);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function answerWithValue(&$stream, $value) {
      $this->answerWithBytes(
        $stream, 
        0x0005 /* REMOTE_MSG_VALUE */, 
        new ByteCountedString(Serializer::representationOf($value))
      );
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function answerWithException(&$stream, $e) {
      $this->answerWithBytes(
        $stream, 
        0x0006 /* REMOTE_MSG_EXCEPTION */, 
        new ByteCountedString(Serializer::representationOf($value))
      );
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function readString($data, &$offset) {
      $string= '';
      do {
        $ctl= unpack('nlength/cnext', substr($data, $offset, 4));
        $string.= substr($data, $offset+ 3, $ctl['length']);
        $offset+= $ctl['length']+ 1;
      } while ($ctl['next']);

      return utf8_decode($string);
    }
    
    /**
     * Method to be triggered when a client has sent data
     *
     * @access  public
     * @param   &peer.server.ConnectionEvent event
     */
    function data(&$event) {
      $header= unpack(
        'Nmagic/cvmajor/cvminor/ctype/ctran/Nlength', 
        substr($event->data, 0, 12)
      );
      
      if (0x3c872747 != $header['magic']) {
        var_dump(addcslashes($event->data, "\0..\17"));
        $this->answer($event->stream, 0x0007 /* REMOTE_MSG_ERROR */, 'Magic number mismatch');
        return;
      }

      switch ($header['type']) {
        case 0x0000: // REMOTE_MSG_INIT
          $this->answerWithValue($event->stream, TRUE);
          break;
        
        case 0x0001: // REMOTE_MSG_LOOKUP
          $offset= 12;
          $name= $this->readString($event->data, $offset);
          // DEBUG Console::writeLine('! Lookup -> ', $name);
          
          if ('xp/demo/Roundtrip' == $name) {
            $this->answerWithBytes($event->stream, 0x0005, new ByteCountedString('I:3479658:{s:46:"net.xp_framework.beans.stateless.RoundtripHome";}'));
            break;
          }
          Console::writeLine('"', urlencode($name), '" not bound');
          $this->answerWithException($event->stream, new IllegalArgumentException($name));
          break;
        
        case 0x0002:  // REMOTE_MSG_CALL
          $oid= unpack('Nzero/Noid', substr($event->data, 12, 8));
        
          $offset= 20;
          $method= $this->readString($event->data, $offset);
          $offset+= 2;  // ??
          $args= Serializer::valueOf($this->readString($event->data, $offset));
          
          // DEBUG Console::writeLine('! Call -> ', xp::stringOf($oid), '::', $method, ' ', xp::stringOf($args));
          
          if ('create' == $method) {
            $this->answerWithBytes($event->stream, 0x0005, new ByteCountedString('I:3479659:{s:42:"net.xp_framework.beans.stateless.Roundtrip";}'));
            break;
          }
          
          if ('echoArray' == $method && is_string($args->values[0])) {
            $this->answerWithException($event->stream, new IllegalArgumentException($method));
            break;
          }
          
          // echoXXX hardcoded
          $this->answerWithValue($event->stream, $args->values[0]);
          break;
        
        default:
          $this->answer($event->stream, 0x0007 /* REMOTE_MSG_ERROR */, 'Unknown type');
      }
    }
    
    /**
     * Method to be triggered when a client disconnects
     *
     * @access  public
     * @param   &peer.server.ConnectionEvent event
     */
    function disconnected(&$event) { 
    }
    
    /**
     * Method to be triggered when a communication error occurs
     *
     * @access  public
     * @param   &peer.server.ConnectionEvent event
     */
    function error(&$event) { 
    }
  }
  
  // {{{ main
  with ($server= &new Server($argv[1], 6448)); {
    $server->addListener(new RemoteInvocationListener());
    $server->init();
    $server->service();
    $server->shutdown();
  }
  // }}}
?>
