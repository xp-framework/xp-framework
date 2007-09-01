<?php
/* This class is part of the XP framework
 *
 * $Id: RequestData.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace peer::http;

  /**
   * RequestData
   *
   * @see      xp://peer.http.HttpRequest#setParameters
   * @purpose  Pass request data directly to
   */
  class RequestData extends lang::Object {
    public
      $data = '';

    /**
     * Constructor
     *
     * @param   string buf
     */
    public function __construct($buf) {
      $this->data= $buf;
      
    }
    
    /**
     * Retrieve data
     *
     * @return  string
     */
    public function getData() {
      return $this->data;
    }
  }
?>
