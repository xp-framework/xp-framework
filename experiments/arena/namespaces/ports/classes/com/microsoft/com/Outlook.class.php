<?php
/* This class is part of the XP framework
 *
 * $Id: Outlook.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace com::microsoft::com;

  ::uses('com.microsoft.com.COMObject');

  /**
   * Excel COM API
   *
   * @ext      com
   */
  class Outlook extends COMObject {

    /**
     * Constructor
     *
     */    
    public function __construct() {
      parent::__construct('Outlook.Application');
    }
  }
?>
