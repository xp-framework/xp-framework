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
   * TODO: Diese Wrapper-Klassen könnten über WSDL generiert werden
   *
   * Anwendungsbeispiel:
   * <pre>
   *   $s= new SOAPClient(
   *     new SOAPHTTPTransport('<URL>'),
   *     '<URN>',
   *     '<METHOD>'
   *   );
   *   try(); {
   *     $return= $s->call(
   *       <PARAMETERS>
   *     );
   *   }
   *   if (catch('Exception', $e)) {
   *     $e->printStackTrace();
   *     exit;
   *   }
   *   var_dump($return);
   * </pre>
   * 
   * @example doc://skeleton/soap/google.php
   */
  class SOAPClient extends Object {
    var 
      $transport,
      $action,
      $method;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   xml.soap.transport.SOAPTransport transport Transport-Objekt
     * @param   string action Action
     * @param   string method Methode
     */
    function __construct(&$transport, $action, $method) {
      $this->transport= &$transport;
      $this->action= $action;
      $this->method= $method;
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
     * Methoden-Aufruf
     *
     * @access  private
     * @param   mixed vars Daten
     * @return  mixed Antwort
     * @throws  IllegalArgumentException, wenn transport ungültig ist
     */
    function _call() {
      if (!is_a($this->transport, 'SOAPTransport')) return throw(new IllegalArgumentException(
        'transport must be a xml.soap.transport.SOAPTransport'
      ));
      
      $params= func_get_args();
    
      $this->answer= &new SOAPMessage();
      $this->message= &new SOAPMessage();
      $this->message->create($this->action, $this->method);
      $this->message->setData($params);

      // Send
      if (FALSE === ($response= &$this->transport->send($this->message))) return FALSE;
      
      // Antwort erhalten
      $this->answer= &$this->transport->retreive($response);
      
      // Daten unserialisieren und zurückgeben
      return is_a($this->answer, 'SOAPMessage') ? $this->answer->getData() : FALSE;
    }
    
    /**
     * Default-Methodenaufruf
     *
     * @access  public
     * @param   mixed vars Daten
     * @return  mixed Antwort
     * @throws  IllegalArgumentException, wenn transport ungültig ist
     */
    function call() {
      $args= func_get_args();
      return call_user_func_array(array(&$this, '_call'), $args);
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
      $args= func_get_args();
      $this->method= array_shift($args);
      $res= call_user_func_array(array(&$this, '_call'), $args);
      return $res ? $res[0] : FALSE;
    }
  }
  
?>
