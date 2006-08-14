<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'remote.protocol.ByteCountedString', 
    'remote.protocol.Serializer',
    'remote.protocol.RemoteInterfaceMapping',
    'remote.server.deploy.Deployer',
    'remote.server.ContainerManager',
    'remote.server.ServerHandler'
  );
  
  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class EascProtocol extends Object {
    var
      $serializer       = NULL,
      $containerManager =  NULL;

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function __construct() {
      $this->serializer= &new Serializer();
      $this->serializer->mapping('I', new RemoteInterfaceMapping());
      
      $this->objects= &new Hashmap();
      $this->objectOIDs= &new Hashmap();
      
      // Perform deployment
      $this->containerManager= &new ContainerManager();
      
      try(); {
        $deployer= &new Deployer();
        $bc= &$deployer->deployBean(XPClass::forName('net.xp_framework.beans.stateless.RoundtripBean'), $this->containerManager);
      } if (catch('Exception', $e)) {
        return throw($e);
      }
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
      
      // Console::writeLine('Header: ', addcslashes($header, "\0..\37!@\177..\377"));
      $stream->write($header);
      // Console::writeLine('Body: ', xp::stringOf($bcs));
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
        new ByteCountedString($this->serializer->representationOf($value))
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
        new ByteCountedString($this->serializer->representationOf($value))
      );
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function answerWithMessage(&$stream, &$m) {
      $ctx= array();
      $ctx[RIH_OBJECTS_KEY]= &$this->objects;
      $ctx[RIH_OIDS_KEY]= &$this->objectOIDs;
      
      $this->answerWithBytes(
        $stream,
        $m->getType(),
        new ByteCountedString($this->serializer->representationOf($m->getValue(), $ctx))
      );
    }

    function readBytes(&$sock, $num) {
      $return= '';
      while (strlen($return) < $num) {
        if (0 == strlen($buf= $sock->readBinary($num - strlen($return)))) return;
        $return.= $buf;
      }
      return $return;
    }
    
    /**
     * Handle client connect
     *
     * @access  public
     * @param   &peer.Socket
     */
    function handleConnect(&$socket) { }

    /**
     * Handle client disconnect
     *
     * @access  public
     * @param   &peer.Socket
     */
    function handleDisconnect(&$socket) { }
  
    /**
     * Handle client data
     *
     * @access  public
     * @param   &peer.Socket
     * @return  mixed
     */
    function handleData(&$socket) {
      $header= unpack(
        'Nmagic/cvmajor/cvminor/ctype/ctran/Nlength', 
        $this->readBytes($socket, 12)
      );

      if (0x3c872747 != $header['magic']) {
        $this->answer($socket, 0x0007 /* REMOTE_MSG_ERROR */, 'Magic number mismatch');
        return NULL;
      }
      
      $impl= &new ServerHandler();
      $impl->setSerializer($this->serializer);
      return $impl->handle($socket, $this, $header['type'], $this->readBytes($socket, $header['length']));
    }
    
    /**
     * Handle I/O error
     *
     * @access  public
     * @param   &peer.Socket
     * @param   &lang.Exception e
     */
    function handleError(&$socket, &$e) { }

  } implements(__FILE__, 'peer.server.Protocol');
?>
