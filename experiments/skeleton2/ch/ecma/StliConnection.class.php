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
    public
      $sock       = NULL,
      $version    = 0;

    /**
     * Constructor. 
     * Takes a peer.Socket object as argument, use as follows:
     * <code>
     *   // [...]
     *   $c= new StliClient(new Socket($stliServer, $stliPort));
     *   // [...]
     * </code>
     *
     * @access  public
     * @param   &peer.Socket sock
     * @param   int version default STLI_VERSION_2
     */
    public function __construct(Socket $sock, $version= STLI_VERSION_2) {
      parent::__construct();
      $this->sock= $sock;
      $this->version= $version;
    }
    
    /**
     * Set the protocol version. This can only be done *prior* to connecting to
     * the server!
     *
     * @access  public
     * @param   int version
     * @throws  lang.IllegalStateException in case already having connected
     */
    public function setVersion($version) { 
      if ($this->sock->isConnected()) throw (new IllegalStateException(
        'Cannot set version after already having connected'
      ));    
      $this->version= $version;
    }

    /**
     * Private helper function
     *
     * @access  private
     */
    private function _sockcmd() {
      $args= func_get_args();
      $write= vsprintf($args[0], array_slice($args, 1));
      
      // Write command
      self::trace('>>>', $write);
      $this->sock->write($write."\n");
      
      // Read response
      $read= chop($this->sock->read());
      self::trace('<<<', $read);
      
      return $read;
    }
    
    /**
     * Private helper function
     *
     * @access  private
     */
    private function _expect($expect, $have) {
      if ($expect !== $have) {
        throw (new TelephonyException(sprintf(
          'Protocol error: Expecting "%s", have "%s"', $expect, $have
        )));
        return FALSE;
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
    public function connect() {
      if (FALSE === ($ret= $this->sock->connect())) return FALSE;
      
      // Send initialization string and check response
      return self::_expect(
        sprintf(STLI_INIT_RESPONSE, $this->version),
        self::_sockcmd('STLI;Version=%d', $this->version)
      );
    }

    /**
     * Close connection and end the communication
     *
     * @access  public
     * @return  mixed the return code of the socket's close method
     * @throws  util.telephony.TelephonyException in case a protocol error occurs
     */
    public function close() {
      if (FALSE === self::_expect(
        STLI_BYE_RESPONSE,
        self::_sockcmd('BYE')
      )) return FALSE;
      
      return $this->sock->close();
    }
    
    /**
     * Create a call
     *
     * @access  public
     * @param   &util.telephony.TelephonyTerminal terminal
     * @param   &util.telephony.TelephonyAddress destination
     * @return  &util.telephony.TelephonyCall a call object
     */
    public function createCall(TelephonyTerminal $terminal, TelephonyAddress $destination) {
      if (FALSE === self::_expect(
        STLI_MAKECALL_RESPONSE,
        self::_sockcmd('MakeCall %s %s', 
          $terminal->getAttachedNumber(), 
          $destination->getNumber()
      ))) return NULL;
      
      return new TelephonyCall($terminal->address, $destination);
    }
    
    /**
     * Get terminal
     *
     * @access  public
     * @param   &util.telephony.TelephonyAddress address
     * @return  &util.telephony.TelephonyTerminal
     */
    public function getTerminal(TelephonyAddress $address) {
      return new TelephonyTerminal($address);
    }

    /**
     * Observe a terminal
     *
     * @access  public
     * @param   &util.telephony.TelephonyTerminal terminal
     * @param   bool status TRUE to start observing, FALSE top stop
     * @return  bool success
     */
    public function observeTerminal(TelephonyTerminal $terminal, $status) {
      if ($status) {
        $success= self::_expect(
          STLI_MON_START_RESPONSE,
          self::_sockcmd('MonitorStart %s', $terminal->getAttachedNumber())
        );      
      } else {
        $success= self::_expect(
          STLI_MON_STOP_RESPONSE,
          self::_sockcmd('MonitorStop %s', $terminal->getAttachedNumber())
        );
      }
      return $success;
    }

    /**
     * Release terminal
     *
     * @access  public
     * @param   &util.telephony.TelephonyTerminal terminal
     * @return  bool success
     */
    public function releaseTerminal(TelephonyTerminal $terminal) {
      return TRUE;
    }
  }
?>
