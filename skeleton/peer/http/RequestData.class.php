<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * RequestData
   *
   * @see      xp://peer.http.HttpRequest#setParamaters
   * @purpose  Pass request data directly to
   */
  class RequestData extends Object {
  
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     */
    function __construct($buf) {
      $this->data= $buf;
      parent::__construct();
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @return  
     */
    function getData() {
      return $this->data;
    }
  }
?>
