<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.telephony.TelephonyProvider');

  // Response codes
  define('STLI_INIT_RESPONSE',      'error_ind SUCCESS STLI Version "%d"');
  define('STLI_BYE_RESPONSE',       'error_ind SUCCESS BYE');
  define('STLI_MON_START_RESPONSE', 'error_ind SUCCESS MonitorStart');
  define('STLI_MON_STOP_RESPONSE',  'error_ind SUCCESS MonitorStop');
  define('STLI_MAKECALL_RESPONSE',  'error_ind SUCCESS MakeCall');
  
  // Supported protocol versions
  define('STLI_VERSION_2',      2);

  /**
   * STLI Client
   *
   * <quote>
   * STLI stands for "Simple Telephony Interface". The TeamCall Server and the client 
   * application can communicate by using this protocol, similar to the communication 
   * between a webserver and a client. We designed STLI to provide basic, but easy to 
   * implement CTI functionalities. STLI is a time saving and cost effective opportunity 
   * to implement CTI functions in every TCP/IP application, including scripting 
   * languages like Perl or Python. A detailed documentation about the STLI interface is
   * part of every ilink TeamCall Server distribution package. 
   * </quote>
   *
   * @purpose  Provide an interface to STLI server
   * @see      http://www.ilink.de/home/de/cti/products/TeamCallServer/
   * @see      http://www.ecma.ch/ecma1/STAND/ECMA-180.HTM
   * @see      http://www.ecma.ch/ecma1/STAND/ECMA-179.HTM
   * @see      http://www.ecma.ch/ecma1/STAND/ECMA-217.HTM
   * @see      http://www.ecma.ch/ecma1/STAND/ECMA-218.HTM
   * @see      http://www.ecma.ch/ecma1/STAND/ECMA-269.HTM
   * @see      http://www.ecma.ch/ecma1/STAND/ECMA-285.HTM
   * @see      http://www.ecma.ch/ecma1/STAND/ecma-323.htm
   */
  class StliConnection extends TelephonyProvider {
    var
      $sock        = NULL,
      $version    = 0,
      
      // The prefix is a constant that depends on your telephony system
      $prefix   = array(
        TEL_CALL_INTERNATIONAL   => '0',
        TEL_CALL_NATIONAL        => '0',
        TEL_CALL_CITY            => '0',
        TEL_CALL_INTERNAL        => ''
      );

    /**
     * Constructor. 
     * Takes a peer.Socket object as argument, use as follows:
     * <code>
     *   // [...]
     *   $c= &new StliClient(new Socket($stliServer, $stliPort));
     *   // [...]
     * </code>
     *
     * @access  public
     * @param   &peer.Socket sock
     */
    function __construct(&$sock, $parserDefaults, $version= STLI_VERSION_2) {
      $this->sock= &$sock;
      $this->version= $version;
      $this->_addressParserDefaults= $parserDefaults;
      parent::__construct();
    }
    
    /**
     * Set dialing prefix
     *
     * @access  public
     * @param   string type one of the TEL_CALL_* constants
     * @param   value the value for the dialing prefix
     */
    function setPrefix($type, $value) {
      $this->prefix[$type]= $value;
    }
    
    /**
     * Retrieve dialing prefix
     *
     * @access  public
     * @param   string type one of the TEL_CALL_* constants
     * @return  value the value for the specified dialing prefix type
     */
    function getPrefix($type) {
      return $this->prefix[$type];
    }
    
    /**
     * Add prefix to phone number where necessary. Also make up
     * the resulting string to a form that can be given to the
     * stli server
     *
     * @access  public
     * @param   &util.telephony.TelephonyAddress callee
     * @param   &util.telephony.TelephonyAddress destination
     * @return  string number
     */    
    function applyPrefix(&$callee, &$destination) {
      $callCategory= $destination->getCallCategory ($callee);

      $this->trace ('Call category: ', $callCategory);
      $this->trace ('Number base: ', $destination->getNumber ($callCategory));

      $nr= str_replace (
        '+',
        '00',
        $this->getPrefix ($callCategory).$destination->getNumber ($callCategory)
      );
      
      $this->trace ('Calling number: ', $nr);
      return $nr;
    }
    
    /**
     * Set the protocol version. This can only be done *prior* to connecting to
     * the server!
     *
     * @access  public
     * @param   int version
     * @throws  IllegalStateException in case already having connected
     */
    function setVersion($version) { 
      if ($this->sock->isConnected()) return throw(new IllegalStateException(
        'Cannot set version after already having connected'
      ));    
      $this->version= $version;
    }

    /**
     * Private helper function
     *
     * @access  private
     */
    function _sockcmd() {
      $args= func_get_args();
      $write= vsprintf($args[0], array_slice($args, 1));
      $this->trace('>>>', $write);
      $this->sock->write($write."\n");
      $read= chop($this->sock->read());
      $this->trace('<<<', $read);
      return $read;
    }
    
    /**
     * Private helper function
     *
     * @access  private
     */
    function _expect($expect, $have) {
      if ($expect !== $have) {
        return throw(new TelephonyException(sprintf(
          'Protocol error: Expecting "%s", have "%s"', $expect, $have
        )));
      }
      
      return $have;
    }

    /**
     * Connect and initiate the communication
     *
     * @access  public
     * @return  mixed the return code of the socket's connect method
     * @throws  util.telephony.TelephonyException in case a protocol error occurs
     */
    function connect() {
      if (FALSE === ($ret= $this->sock->connect())) return FALSE;
      
      // Send initialization string and check response
      return $this->_expect(
        sprintf(STLI_INIT_RESPONSE, $this->version),
        $this->_sockcmd('STLI;Version=%d', $this->version)
      );
    }

    /**
     * Close connection and end the communication
     *
     * @access  public
     * @return  mixed the return code of the socket's close method
     * @throws  util.telephony.TelephonyException in case a protocol error occurs
     */
    function close() {
      if (FALSE === $this->_expect(
        STLI_BYE_RESPONSE,
        $this->_sockcmd('BYE')
      )) return FALSE;
      
      return $this->sock->close();
    }
    
    /**
     * Retrieve an address
     *
     * @access  public  
     * @param   string number
     * @return  &util.telephony.TelephonyAddress 
     */
    function &getAddress($number) {
      $a= &parent::getAddress($number);
      return $a;
    }
    
    /**
     * Create a call
     *
     * @access  public
     * @param   &util.telephony.TelephonyTerminal terminal
     * @param   &util.telephony.TelephonyAddress destination
     * @return  &util.telephony.TelephonyCall a call object
     */
    function &createCall(&$terminal, &$destination) {
      if (FALSE === parent::createCall($terminal, $destination)) return FALSE;
      
      if (FALSE === $this->_expect(
        STLI_MAKECALL_RESPONSE,
        $this->_sockcmd('MakeCall %s %s', 
          $terminal->getAttachedNumber(), 
          $this->applyPrefix ($terminal->address, $destination)
      ))) return FALSE;
      
      return new TelephonyCall($terminal->address, $destination);
    }
    
    /**
     * Get terminal
     *
     * @access  public
     * @param   &util.telephony.TelephonyAddress address
     */
    function &getTerminal(&$address) {
      if (FALSE === parent::getTerminal($address)) return FALSE;
      if (FALSE === $this->_expect(
        STLI_MON_START_RESPONSE,
        $this->_sockcmd('MonitorStart %s', $address->getExt())
      )) return FALSE;
      return new TelephonyTerminal($address);
    }

    /**
     * Release terminal
     *
     * @access  public
     * @param   &util.telephony.TelephonyTerminal terminal
     */
    function releaseTerminal($terminal) {
      if (FALSE === parent::releaseTerminal($terminal)) return FALSE;
      if (FALSE === $this->_expect(
        STLI_MON_STOP_RESPONSE,
        $this->_sockcmd('MonitorStop %s', $terminal->getAttachedNumber())
      )) return FALSE;
      
      return TRUE;
    }
  }
?>
