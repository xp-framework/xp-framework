<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'xml.soap.SOAPMessage',
    'xml.QName',
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
      $action       = '',
      $mapping      = array();
    
    /**
     * Constructor
     *
     * @access  public
     * @param   &xml.soap.transport.SOAPTransport transport a SOAP transport
     * @param   string action Action
     */
    public function __construct(SOAPTransport $transport, $action) {
      $this->transport= $transport;
      $this->action= $action;
      
    }
    
    /**
     * Set trace for debugging
     *
     * @access  public
     * @param   &util.log.LogCategory cat
     */
    public function setTrace(LogCategory $cat) {
      $this->transport->setTrace($cat);
    }
    
    /**
     * Register mapping for a qname to a class obkect
     *
     * @access  public
     * @param   &xml.QName qname
     * @param   &lang.XPClass class
     */
    public function registerMapping(QName $qname, XPClass $class) {
      $this->mapping[strtolower($qname->toString())]= $class;
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
      if (!($this->transport instanceof SOAPTransport)) {
        throw (new IllegalArgumentException(
          'Transport must be a xml.soap.transport.SOAPTransport'
        ));
      }
      
      $args= func_get_args();
      
      $this->answer= new SOAPMessage();
      $this->message= new SOAPMessage();
      $this->message->create($this->action, array_shift($args));
      $this->message->setData($args);

      // Send
      if (FALSE == ($response= $this->transport->send($this->message))) return FALSE;
      
      // Response
      if (FALSE == ($this->answer= $this->transport->retrieve($response))) return FALSE;
      
      $data= $this->answer->getData('ENUM', $this->mapping);
      return sizeof($data) == 1 ? $data[0] : $data;
    }
  }
?>
