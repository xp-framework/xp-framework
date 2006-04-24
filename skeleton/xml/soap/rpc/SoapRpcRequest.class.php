<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'scriptlet.rpc.AbstractRpcRequest',
    'xml.soap.SOAPMessage'
  );
  
  /**
   * Wraps SOAP Rpc Router request
   *
   * @see xml.soap.rpc.SoapRpcRouter
   * @see scriptlet.HttpScriptletRequest
   */
  class SoapRpcRequest extends AbstractRpcRequest {

    /**
     * Retrieve SOAP message from request
     *
     * @access  public
     * @return  &xml.soap.SOAPMessage message object
     */
    function &getMessage() {
      $m= &SOAPMessage::fromString($this->getData());
      list(
        $class, 
        $method
      )= explode('#', str_replace('"', '', $this->getHeader('SOAPAction')));
      
      $m->setClass($class);
      $m->setMethod($method);
      return $m;
    }
  }
?>
