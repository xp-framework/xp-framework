<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'remote.protocol.XpProtocolConstants',
    'remote.server.message.EascMessageFactory',
    'lang.reflect.Proxy'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class ServerHandler extends Object {
      
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setSerializer(&$serializer) {
      $this->serializer= &$serializer;
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
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function writeResponse(&$stream, $type, $buffer) {
      $bcs= &new ByteCountedString($buffer);
      
      $header= pack(
        'Nc4Na*',
        DEFAULT_PROTOCOL_MAGIC_NUMBER,
        1,  // versionMajor
        0,  // versionMinor
        $type,
        FALSE,
        strlen($buffer)
      );
      
      $stream->write($header);
      $stream->write($buffer);
      $stream->flush();
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function handle(&$listener, &$event) {
      $type= $event->data[0];
      $data= $event->data[1];
      
      try(); {
        $handler= &EascMessageFactory::forType($type);
        $handler->handle($listener, $event);

        $response= &EascMessageFactory::forType(REMOTE_MSG_VALUE);
        $response->setValue($handler->getValue());

      } if (catch('Exception', $e)) {
        $response= &EascMessageFactory::forType(REMOTE_MSG_VALUE);
        $response->setValue($e);
      }

      $listener->answerWithMessage($event->stream, $response);
      return;
    }
  }
?>
