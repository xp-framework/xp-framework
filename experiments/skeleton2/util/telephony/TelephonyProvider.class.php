<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'util.telephony.TelephonyAddress',
    'util.telephony.TelephonyAddressParser',
    'util.telephony.TelephonyTerminal',
    'util.telephony.TelephonyCall',
    'util.telephony.TelephonyException'
  );

  /**
   * Abstract base class for a telephony provider
   * 
   * Example (using the STLI driver):
   * <code>
   *   uses(
   *     'ch.ecma.StliConnection', 
   *     'peer.Socket', 
   *     'util.log.Logger',
   *     'util.log.FileAppender',
   *     'util.cmd.ParamString'
   *   );
   * 
   *   $p= new ParamString();
   *   if (4 != $p->count) {
   *     printf("Usage: %s server:port <from> <to>\n", basename($p->value(0)));
   *     exit();
   *   }
   *   list($server, $port)= explode(':', $p->value(1));
   * 
   *   $l= Logger::getInstance();
   *   $cat= $l->getCategory();
   *   $cat->addAppender(new FileAppender('php://stderr'));
   * 
   *   $c= new StliConnection(new Socket($server, $port));
   *   $c->setTrace($cat);
   *   try(); {
   *     $c->connect();
   *     $term= $c->getTerminal($c->getAddress('int:'.$p->value(2)));
   *     $call= $c->createCall($term, $c->getAddress('ext:'.$p->value(3)));
   *     $c->releaseTerminal($term);
   *     $c->close();
   *   } if (catch('Exception', $e)) {
   *     $e->printStackTrace();
   *     exit;
   *   }
   * 
   *   printf("Done\n");
   * </code>
   *
   * @purpose Provides an interface to telephony
   * @see     http://java.sun.com/products/jtapi/jtapi-1.3/html/overview-summary.html
   */
  class TelephonyProvider extends Object {
    public
      $cat  = NULL;
    
    public
      $_addressParserDefaults= array();
      
    /**
     * Set a LogCategory for tracing communication
     *
     * @access  public
     * @param   &util.log.LogCategory cat a LogCategory object to which communication
     *          information will be passed to or NULL to stop tracing
     * @throws  IllegalArgumentException in case a of a type mismatch
     */
    public function setTrace(&$cat) {
      if (NULL !== $cat && !is_a($cat, 'LogCategory')) {
        throw (new IllegalArgumentException('Argument passed is not a LogCategory'));
      }
      
      $this->cat= $cat;
    }
    
    /**
     * Trace function
     *
     * @access  protected
     * @param   mixed* arguments
     */
    protected function trace() {
      if (NULL == $this->cat) return;
      $args= func_get_args();
      call_user_func_array(array($this->cat, 'debug'), $args);
    }
    
    /**
     * Connect and initiate the communication
     *
     * @access  public
     */
    public function connect() { }

    /**
     * Close connection and end the communication
     *
     * @access  public
     */
    public function close() { }
    
    /**
     * Retrieve an address
     *
     * @access  public  
     * @param   string number
     * @return  &util.telephony.TelephonyAddress 
     */
    public function getAddress($number) { 
      try {
        $p= new TelephonyAddressParser($this->_addressParserDefaults);
        $ta= $p->parseNumber ($number);
      } catch (FormatException $e) {
        throw  ($e);
      }
      
      return $ta;
    }
    
    /**
     * Create a call
     *
     * @access  public
     * @param   &util.telephony.TelephonyTerminal terminal
     * @param   &util.telephony.TelephonyAddress destination
     * @throws  IllegalArgumentException in case of parameter type mismatch
     * @return  &util.telephony.TelephonyCall a call object
     */
    public function createCall(&$terminal, &$destination) {
      if (!is_a($terminal, 'TelephonyTerminal')) {
        trigger_error('type: '.gettype($terminal), E_USER_WARNING);
        throw (new IllegalArgumentException('Terminal parameter is not a TelephonyTerminal'));
      }
      if (!is_a($destination, 'TelephonyAddress')) {
        trigger_error('type: '.gettype($destination), E_USER_WARNING);
        throw (new IllegalArgumentException('Destination parameter is not a TelephonyAddress'));
      }
      return NULL;
    }
    
    /**
     * Get terminal
     *
     * @access  public
     * @param   &util.telephony.TelephonyAddress address
     * @throws  IllegalArgumentException in case of parameter type mismatch
     */
    public function getTerminal(&$address) { 
      if (!is_a($address, 'TelephonyAddress')) {
        trigger_error('type: '.gettype($address), E_USER_WARNING);
        throw (new IllegalArgumentException('Address parameter is not a TelephonyAddress'));
      }
      //if (TEL_ADDRESS_INTERNAL !== $address->getType()) {
      //  return throw(new IllegalArgumentException('Terminals can only have internal addresses'));
      //}
      return NULL;
    }
    
    /**
     * Release terminal
     *
     * @access  public
     * @param   &util.telephony.TelephonyTerminal terminal
     * @return  bool success
     */
    public function releaseTerminal(&$terminal) {
      if (!is_a($terminal, 'TelephonyTerminal')) {
        trigger_error('type: '.gettype($terminal), E_USER_WARNING);
        throw (new IllegalArgumentException('Terminal parameter is not a TelephonyTerminal'));
      }
      return TRUE;
    }
  }
?>
