<?php
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */

  uses('xml.soap.SOAPMessage');
  
  /**
   * Basic SOAP-Client
   *
   * Diese Klasse kann vererbt werden, um "Proxy-Klassen" zu erstellen.
   * Durch Hinzufügen von Methoden, die ihrerseits wieder nur _call() aufrufen,
   * können transparente Wrapper geschaffen werden, die self::method setzen
   * und evtl. auch bereits den richtigen Datentypen setzen.
   *
   * Example:
   * <code>
   *   $s= &new SOAPClient(new SOAPHTTPTransport('<URL>'), '<URN>');
   *   try(); {
   *     $return= $s->invoke('<METHOD>', <PARAMETERS>);
   *   } if (catch('Exception', $e)) {
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   *   var_dump($return);
   * </code>
   * 
   * @ext      overload
   * @purpose  Generic SOAP client base class
   */
  class SOAPClient extends Object {
    var 
      $transport    = NULL,
      $action       = '';
    
    /**
     * Constructor
     *
     * @access  public
     * @param   &xml.soap.transport.SOAPTransport transport a SOAP transport
     * @param   string action Action
     */
    function __construct(&$transport, $action) {
      $this->transport= &$transport;
      $this->action= $action;
      parent::__construct();
    }
    
    /**
     * Set trace for debugging
     *
     * @access  public
     * @param   &util.log.LogCategory cat
     */
    function setTrace(&$cat) {
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
    function invoke() {
      if (!is_a($this->transport, 'SOAPTransport')) return throw(new IllegalArgumentException(
        'transport must be a xml.soap.transport.SOAPTransport'
      ));
      
      $args= func_get_args();
      
      $this->answer= &new SOAPMessage();
      $this->message= &new SOAPMessage();
      $this->message->create($this->action, array_shift($args));
      $this->message->setData($args);

      // Send
      if (FALSE === ($response= &$this->transport->send($this->message))) return FALSE;
      
      // Response
      if (FALSE === ($this->answer= &$this->transport->retreive($response))) return FALSE;
      
      $data= &$this->answer->getData();
      return $data[0];
    }
    
    /**
     * Magic interceptor for member method access
     *
     * @access  magic
     * @param   string name
     * @param   &array args
     * @param   &mixed return
     * @return  bool success
     */
    function __call($name, &$args, &$return) {
      array_unshift($args, $name);
      $return= &call_user_func_array(array($this, 'invoke'), $args);
      return TRUE;
    }
  } overload('SOAPClient');
?>
