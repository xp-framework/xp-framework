<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'org.apache.HttpScriptletRequest',
    'xml.soap.SOAPMessage'
  );
  
  /**
   * Wraps SOAP Rpc Router request
   *
   * @see xml.soap.rpc.SoapRpcRouter
   * @see org.apache.HttpScriptletRequest
   */
  class SoapRpcRequest extends HttpScriptletRequest {
  
    /**
     * Retrieve SOAP message from request
     *
     * @access  public
     * @return  &xml.soap.SOAPMessage message object
     */
    function &getMessage() {
      static $m;
      
      if (!isset($m)) {
        $m= &new SOAPMessage();
        list(
          $m->action, 
          $m->method
        )= explode('#', str_replace('"', '', $this->getHeader('SOAPAction')));
        $m->fromString($this->getData());
      }
      return $m;
    }
  }
?>
