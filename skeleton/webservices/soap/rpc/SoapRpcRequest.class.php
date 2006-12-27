<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'scriptlet.rpc.AbstractRpcRequest',
    'webservices.soap.SOAPMessage'
  );
  
  /**
   * Wraps SOAP Rpc Router request
   *
   * @see webservices.soap.rpc.SoapRpcRouter
   * @see scriptlet.HttpScriptletRequest
   */
  class SoapRpcRequest extends AbstractRpcRequest {

    /**
     * Retrieve SOAP message from request
     *
     * @return  &webservices.soap.SOAPMessage message object
     */
    public function getMessage() {
      $m= SOAPMessage::fromString($this->getData());
      list(
        $class, 
        $method
      )= explode('#', str_replace('"', '', $this->getHeader('SOAPAction')));
      
      $m->setHandlerClass($class);
      $m->setMethod($method);
      return $m;
    }
  }
?>
