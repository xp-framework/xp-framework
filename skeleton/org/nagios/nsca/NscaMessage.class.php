<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  define('NSCA_OK',        0x0000);
  define('NSCA_WARN',      0x0001);
  define('NSCA_ERROR',     0x0002);
  define('NSCA_UNKNOWN',   0x0003);

  /**
   * Represents a single NSCA message
   *
   * @see      xp://org.nagios.nsca.NscaClient#send
   * @purpose  Wrapper
   */
  class NscaMessage extends Object {
    var
      $host         = '',
      $service      = '',
      $status       = 0,
      $information  = '';

    /**
     * Constructor
     *
     * @access  public
     * @param   string host
     * @param   string service
     * @param   int status one of NSCA_* constants
     * @param   string information
     */ 
    function __construct($host, $service, $status, $information) {
      
      $this->host= $host;
      $this->service= $service;
      $this->status= $status;
      $this->information= $information;
    }
    
    /**
     * Retrieve a status' name
     *
     * @model   static
     * @access  public
     * @param   int status
     * @return  string name
     */
    function nameOfStatus($status) {
      static $map= array(
        NSCA_OK      => 'NSCA_OK',     
        NSCA_WARN    => 'NSCA_WARN',   
        NSCA_ERROR   => 'NSCA_ERROR',  
        NSCA_UNKNOWN => 'NSCA_UNKNOWN'
      );
      return $map[$status];
    }

    /**
     * Set Host
     *
     * @access  public
     * @param   string host
     */
    function setHost($host) {
      $this->host= $host;
    }

    /**
     * Get Host
     *
     * @access  public
     * @return  string
     */
    function getHost() {
      return $this->host;
    }

    /**
     * Set Service
     *
     * @access  public
     * @param   string service
     */
    function setService($service) {
      $this->service= $service;
    }

    /**
     * Get Service
     *
     * @access  public
     * @return  string
     */
    function getService() {
      return $this->service;
    }

    /**
     * Set Status
     *
     * @access  public
     * @param   int status one of NSCA_* constants
     */
    function setStatus($status) {
      $this->status= $status;
    }

    /**
     * Get Status
     *
     * @access  public
     * @return  int one of NSCA_* constants
     */
    function getStatus() {
      return $this->status;
    }

    /**
     * Set Information
     *
     * @access  public
     * @param   string information
     */
    function setInformation($information) {
      $this->information= $information;
    }

    /**
     * Get Information
     *
     * @access  public
     * @return  string
     */
    function getInformation() {
      return $this->information;
    }
    
  }
?>
