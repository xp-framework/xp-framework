<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.log.Traceable',
    'xml.xmlrpc.XmlRpcFaultException'
  );

  /**
   * Base class for XML-RPC transports.
   *
   * @purpose  Base class.
   */
  class XmlRpcTransport extends Object {
    var
      $cat  = NULL;
      
    /**
     * Set trace for debugging
     *
     * @access  public
     * @param   &util.log.LogCategory cat
     */
    function setTrace(&$cat) {
      $this->cat= &$cat;
    }
 
    /**
     * Send XML-RPC message
     *
     * @access  public
     * @param   &xml.xmlrpc.XmlRpcMessage message
     * @return  &scriptlet.HttpScriptletResponse
     */
    function &send(&$message) { }
    
    /**
     * Retrieve a XML-RPC message.
     *
     * @access  public
     * @param   &xml.xmlrpc.XmlRpcResponse response
     * @return  &xml.xmlrpc.XmlRpcMessage
     */
    function &retrieve(&$response) { }    
  } implements(__FILE__, 'util.log.Traceable');
?>
