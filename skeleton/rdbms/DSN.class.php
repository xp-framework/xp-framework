<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * DSN
   *
   * DSN examples:
   * <pre>
   *   type://username:password@host:port/database/table
   * </pre>
   */
  class DSN extends Object {
    var 
      $parts    = array();
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string str
     */
    function __construct($str) {
      $this->parts= parse_url($str);
      $this->parts['dsn']= $str;
      parent::__construct();
    }
  }
?>
