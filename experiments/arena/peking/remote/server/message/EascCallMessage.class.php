<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('remote.server.message.EascMessage');

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
     * @access  
     * @param   
     * @return  
     */
    function handle(&$listener, $data) {
      $oid= unpack('Nzero/Noid', substr($data, 0, 8));
      $p= &$listener->context[RIH_OBJECTS_KEY]->get($oid['oid']);
      
      $offset= 8;
      $method= $this->readString($data, $offset);
      
      $offset+= 2;  // ?
      $args= $listener->serializer->valueOf($this->readString($data, $offset), $l, $listener->context);
      try(); {
        $result= call_user_func_array(array(&$p, $method), $args->values);
        $this->setValue($result);
      } if (catch('Exception', $e)) {
        return throw($e);
      }
    }
  }
?>
