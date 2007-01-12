<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'remote.server.message.EascMessage',
    'remote.server.RemoteObjectMap',
    'remote.protocol.SerializedData'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class EascCallMessage extends EascMessage {

    /**
     * Get type of message
     *
     * @return  int
     */
    public function getType() {
      return REMOTE_MSG_CALL;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function handle($protocol, $data) {
      $oid= unpack('Nzero/Noid', substr($data, 0, 8));
      $p= $protocol->context[RemoteObjectMap::CTX_KEY]->getByOid($oid['oid']);

      $offset= 8;
      $method= $protocol->readString($data, $offset);
      
      $offset+= 2;  // ?
      $args= $protocol->serializer->valueOf(new SerializedData($protocol->readString($data, $offset)), $protocol->context);
      $this->setValue(call_user_func_array(array($p, $method), $args->values));
    }
  }
?>
