<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  define('STLI_INIT_RESPONSE',  'error_ind SUCCESS STLI Version "%d"');
  define('STLI_BYE_RESPONSE',   'error_ind SUCCESS BYE');

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
   * @see	   http://www.ilink.de/home/de/cti/products/TeamCallServer/
   * @see	   http://www.ecma.ch/ecma1/STAND/ECMA-180.HTM
   * @see	   http://www.ecma.ch/ecma1/STAND/ECMA-179.HTM
   * @see      http://www.ecma.ch/ecma1/STAND/ECMA-217.HTM
   * @see	   http://www.ecma.ch/ecma1/STAND/ECMA-218.HTM
   * @see	   http://www.ecma.ch/ecma1/STAND/ECMA-269.HTM
   * @see	   http://www.ecma.ch/ecma1/STAND/ECMA-285.HTM
   * @see	   http://www.ecma.ch/ecma1/STAND/ecma-323.htm
   * @see      http://java.sun.com/products/jtapi/jtapi-1.3/html/javax/telephony/package-summary.html#FEATURES
   */
  class StliConnection extends Object {
    var
      $sock	    = NULL,
      $cat      = NULL,
      $version	= 2;

    /**
     * Constructor. 
     * Takes a peer.Socket object as argument, use as follows:
     * <code>
     *   // [...]
     *   $c= &new StliClient(new Socket($stliServer, $stliPort));
     *   // [...]
     * </code>
     *
     * @access	public
     * @param	&peer.Socket sock
     */
    function __construct(&$sock) {
      $this->sock= &$sock;
      parent::__construct();
    }
    
    /**
     * Set a LogCategory for tracing communication
     *
     * @access  public
     * @param   &util.log.LogCategory cat a LogCategory object to which communication
     *          information will be passed to or NULL to stop tracing
     * @throws  IllegalArgumentException in case a of a type mismatch
     */
    function &setTrace(&$cat) {
      if (NULL !== $cat && !is_a($cat, 'LogCategory')) {
        return throw(new IllegalArgumentException('Argument passed is not a LogCategory'));
      }
      
      $this->cat= &$cat;
    }

    /**
     * Set the protocol version. This can only be done *prior* to connecting to
     * the server!
     *
     * @access	public
     * @param	int version
     * @throws	IllegalStateException in case already having connected
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
     * @access	private
     */
    function _sockcmd() {
      $args= func_get_args();
      $write= vsprintf($args[0], array_slice($args, 1))."\n";
      $this->cat && $this->cat->info('>>>', $write);
      $this->sock->write($write);
      $read= chop($this->sock->read());
      $this->cat && $this->cat->info('<<<', $read);
      return $read;
    }

    /**
     * Connect and initiate the communication
     *
     * @access	public
     * @return	mixed the return code of the socket's connect method
     * @throws  FormatExcpetion in case a protocol error occurs
     */
    function connect() {
      $ret= $this->sock->connect();
      
      // Send initialization string and check response
      $expect= sprintf(STLI_INIT_RESPONSE, $this->version);
      if ($expect !== ($r= $this->_sockcmd('STLI;Version=%d', $this->version))) {
        return throw(new FormatException(sprintf(
          'Protocol error: Expecting "%s", got "%s"', $expect, $r
        )));
      }
      return $ret;
    }

    /**
     * Close connection and end the communication
     *
     * @access	public
     * @return	mixed the return code of the socket's close method
     * @throws  FormatExcpetion in case a protocol error occurs
     */
    function close() {
      if (STLI_BYE_RESPONSE !== ($r= $this->_sockcmd('BYE'))) {
        return throw(new FormatException(sprintf(
          'Protocol error: Expecting "%s", got "%s"', STLI_BYE_RESPONSE, $r
        )));
      }
      return $this->sock->close();
    }
  }
?>
