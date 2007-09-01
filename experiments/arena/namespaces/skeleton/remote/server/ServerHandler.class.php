<?php
/* This class is part of the XP framework
 *
 * $Id: ServerHandler.class.php 9365 2007-01-25 13:33:56Z friebe $ 
 */

  namespace remote::server;

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
  class ServerHandler extends lang::Object {
      
    /**
     * Set serializer
     *
     * @param   remote.protocol.Serializer serializer
     */
    public function setSerializer($serializer) {
      $this->serializer= $serializer;
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
        $handler= remote::server::message::EascMessageFactory::forType($type);
        $handler->handle($protocol, $data);

        $response= remote::server::message::EascMessageFactory::forType(REMOTE_MSG_VALUE);
        $response->setValue($handler->getValue());

      } catch (lang::Throwable $e) {
        $response= remote::server::message::EascMessageFactory::forType(REMOTE_MSG_EXCEPTION);
        $response->setValue($e);
      }

      $protocol->answerWithMessage($socket, $response);
    }
  }
?>
