<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * (Insert class' description here)
   *
   * @ext      extensiom
   * @see      reference
   * @purpose  purpose
   */
  class ConnectionEvent extends Object {
    var
      $type     = '',
      $stream   = NULL,
      $data     = NULL;
      
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function __construct($type, &$stream, $data= NULL) {
      $this->type= $type;
      $this->stream= &$stream;
      $this->data= $data;
      parent::__construct();
    }
  }
?>
