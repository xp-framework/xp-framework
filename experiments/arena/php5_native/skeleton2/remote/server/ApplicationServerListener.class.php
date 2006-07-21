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
  class ApplicationServerListener extends ConnectionListener {
    public
      $serializer       = NULL,
      $containerManager =  NULL;

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function __construct() {
      $this->serializer= &new Serializer();
      $this->serializer->mapping('I', new RemoteInterfaceMapping());
      
      $this->objects= &new Hashmap();
      $this->objectOIDs= &new Hashmap();
      
      // Perform deployment
      $this->containerManager= &new ContainerManager();
      
      try {
        $deployer= &new Deployer();
        $bc= &$deployer->deployBean(XPClass::forName('net.xp_framework.beans.stateless.RoundtripBean'), $this->containerManager);
      } catch (Exception $e) {
        throw($e);
      }
    }      

    /**
     * Method to be triggered when a client connects
     *
     * @access  public
     * @param   &peer.server.ConnectionEvent event
     */
    public function connected(&$event) {
      // debug Console::writeLine('Hello ', $event->toString());
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function answer(&$stream, $type, $data) {
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
    public function answerWithBytes(&$stream, $type, &$bcs) {
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
    public function answerWithValue(&$stream, $value) {
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
    public function answerWithException(&$stream, $e) {
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
    public function answerWithMessage(&$stream, &$m) {
      $ctx= array();
      $ctx[RIH_OBJECTS_KEY]= &$this->objects;
      $ctx[RIH_OIDS_KEY]= &$this->objectOIDs;
      
      $this->answerWithBytes(
        $stream,
        $m->getType(),
        new ByteCountedString($this->serializer->representationOf($m->getValue(), $ctx))
      );
    }
    
    /**
     * Method to be triggered when a client has sent data
     *
     * @access  public
     * @param   &peer.server.ConnectionEvent event
     */
    public function data(&$event) {
      $impl= &new ServerHandler();
      $impl->setSerializer($this->serializer);
      return $impl->handle($this, $event);
    }
    
    /**
     * Method to be triggered when a client disconnects
     *
     * @access  public
     * @param   &peer.server.ConnectionEvent event
     */
    public function disconnected(&$event) { 
    }
    
    /**
     * Method to be triggered when a communication error occurs
     *
     * @access  public
     * @param   &peer.server.ConnectionEvent event
     */
    public function error(&$event) { 
    }
  }
?>
