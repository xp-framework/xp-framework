<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('com.microsoft.com.COMObject');

  /**
   * Excel COM API
   *
   * @ext      com
   */
  class Excel extends COMObject {

    /**
     * Constructor
     *
     * @access  public
     */    
    function __construct() {
      parent::__construct('Excel.Application');
    }
  }
?>
