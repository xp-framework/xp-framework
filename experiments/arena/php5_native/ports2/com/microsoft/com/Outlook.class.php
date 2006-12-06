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
  class Outlook extends COMObject {

    /**
     * Constructor
     *
     * @access  public
     */    
    public function __construct() {
      parent::__construct('Outlook.Application');
    }
  }
?>
