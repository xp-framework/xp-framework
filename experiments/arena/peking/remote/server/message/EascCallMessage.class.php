<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'remote.server.message.EascMessage',
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
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function handle($listener, $data) {
      $oid= unpack('Nzero/Noid', substr($data, 0, 8));
      $p= $listener->context[RIH_OBJECTS_KEY]->get($oid['oid']);

      $offset= 8;
      $method= $this->readString($data, $offset);
      
      $offset+= 2;  // ?
      $args= $listener->serializer->valueOf(new SerializedData($this->readString($data, $offset)), $l, $listener->context);
      try {
        $result= call_user_func_array(array($p, $method), $args->values);
        $this->setValue($result);
      } catch (Exception $e) {
        throw($e);
      }
    }
  }
?>
