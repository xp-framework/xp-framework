<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * RequestData
   *
   * @see      xp://peer.http.HttpRequest#setParameters
   * @purpose  Pass request data directly to
   */
  class RequestData extends Object {
    var
      $data = '';

    /**
     * Constructor
     *
     * @access  public
     * @param   string buf
     */
    function __construct($buf) {
      $this->data= $buf;
      parent::__construct();
    }
    
    /**
     * Retrieve data
     *
     * @access  public
     * @return  string
     */
    function getData() {
      return $this->data;
    }
  }
?>
