<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'xml.soap.SOAPMessage',
    'util.log.Traceable'
  );

  /**
   * Basic SOAP-Client
   *
   * Example:
   * <code>
   *   $s= new SOAPClient(new SOAPHTTPTransport('<URL>'), '<URN>');
   *   try(); {
   *     $return= $s->invoke('<METHOD>', <PARAMETERS>);
   *   } if (catch('Exception', $e)) {
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   *   var_dump($return);
   * </code>
   * 
   * @purpose  Generic SOAP client base class
   */
  class SOAPClient extends Object implements Traceable {
    public 
      $transport    = NULL,
      $action       = '';
    
    /**
     * Constructor
     *
     * @access  public
     * @param   &xml.soap.transport.SOAPTransport transport a SOAP transport
     * @param   string action Action
     */
    public function __construct(&$transport, $action) {
      $this->transport= $transport;
      $this->action= $action;
      
    }
    
    /**
     * Set trace for debugging
     *
     * @access  public
     * @param   &util.log.LogCategory cat
     */
    public function setTrace(&$cat) {
      $this->transport->setTrace($cat);
    }
    
    /**
     * Invoke method call
     *
     * @access  public
     * @param   string method name
     * @param   mixed vars
     * @return  mixed answer
     * @throws  lang.IllegalArgumentException
     * @throws  xml.soap.SOAPFaultException
     */
    public function invoke() {
      if (!is_a($this->transport, 'SOAPTransport')) throw (new IllegalArgumentException(
        'transport must be a xml.soap.transport.SOAPTransport'
      ));
      
      $args= func_get_args();
      
      $this->answer= new SOAPMessage();
      $this->message= new SOAPMessage();
      $this->message->create($this->action, array_shift($args));
      $this->message->setData($args);

      // Send
      if (FALSE == ($response= $this->transport->send($this->message))) return FALSE;
      
      // Response
      if (FALSE == ($this->answer= $this->transport->retrieve($response))) return FALSE;
      
      $data= $this->answer->getData();
      return sizeof($data) == 1 ? $data[0] : $data;
    }
  }
?>
