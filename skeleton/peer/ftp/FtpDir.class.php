<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * FTP directory
   *
   * @see      xp://peer.ftp.FtpConnection
   * @purpose  Represent an FTP directory
   */
  class FtpDir extends Object {
    var
      $name     = '';
      
    var
      $_hdl     = NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     * @param   resource hdl default NULL
     */
    function __construct($name, $hdl= NULL) {
      $this->name= $name;
      $this->_hdl= $hdl;
      parent::__construct();
    }
    
    /**
     * Get entries (iterative function)
     *
     * @access  public
     * @return  string entry or FALSE to indicate EOL
     */
    function getEntry() {
      static $entries;
      
      if (!isset($entries)) {
        $entries= ftp_nlist($this->_hdl, $this->name);
        $entry= $entries[0];
      } else {
        if (FALSE === ($entry= next($entries))) {
          reset($entries);
        }
      }
      
      return str_replace('//', '/', $entry);
    }
  }
?>
