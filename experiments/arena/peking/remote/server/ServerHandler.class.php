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
   * Server handler
   *
   * @purpose  handler
   */
  class ServerHandler extends Object {
      
    /**
     * Set serializer
     *
     * @param   remote.protocol.Serializer serializer
     */
    public function setSerializer($serializer) {
      $this->serializer= $serializer;
    }  
  
    /**
     * Extract a string out of packed data
     *
     * @param   string data
     * @param   int offset
     * @return  string
     */
    public function readString($data, &$offset) {
      $string= '';
      do {
        $ctl= unpack('nlength/cnext', substr($data, $offset, 4));
        $string.= substr($data, $offset+ 3, $ctl['length']);
        $offset+= $ctl['length']+ 1;
      } while ($ctl['next']);

      return utf8_decode($string);
    }
    
    /**
     * Write response
     *
     * @param   io.Stream stream
     * @param   int type
     * @param   string buffer
     */
    public function writeResponse($stream, $type, $buffer) {
      $bcs= new ByteCountedString($buffer);
      
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
     * Handle incoming data
     *
     * @param   peer.Socket socket
     * @param   peer.server.ServerProtocol protocol
     * @param   int type
     * @param   string data
     */
    public function handle($socket, $protocol, $type, $data) {
      try {
        $handler= EascMessageFactory::forType($type);
        $handler->handle($protocol, $data);

        $response= EascMessageFactory::forType(REMOTE_MSG_VALUE);
        $response->setValue($handler->getValue());

      } catch (XPException $e) {
        $response= EascMessageFactory::forType(REMOTE_MSG_VALUE);
        $response->setValue($e);
      }

      $protocol->answerWithMessage($socket, $response);
      return;
    }
  }
?>
