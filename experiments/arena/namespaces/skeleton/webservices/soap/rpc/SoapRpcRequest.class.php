<?php
/* This class is part of the XP framework
 *
 * $Id: SoapRpcRequest.class.php 10015 2007-04-16 16:36:48Z kiesel $
 */

  namespace webservices::soap::rpc;
 
  uses(
    'scriptlet.rpc.AbstractRpcRequest',
    'webservices.soap.xp.XPSoapMessage'
  );
  
  /**
   * Wraps SOAP Rpc Router request
   *
   * @see webservices.soap.rpc.SoapRpcRouter
   * @see scriptlet.HttpScriptletRequest
   */
  class SoapRpcRequest extends scriptlet::rpc::AbstractRpcRequest {

    /**
     * Retrieve SOAP message from request
     *
     * @return  webservices.soap.xp.XPSoapMessage message object
     */
    public function getMessage() {
      $m= webservices::soap::xp::XPSoapMessage::fromString($this->getData());
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
