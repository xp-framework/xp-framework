<?php
/* This class is part of the XP framework
 *
 * $Id: Excel.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace com::microsoft::com;

  ::uses('com.microsoft.com.COMObject');

  /**
   * Excel COM API
   *
   * @see      http://msdn.microsoft.com/library/officedev/off2000/xltocobjectmodelapplication.htm
   * @ext      com
   */
  class Excel extends COMObject {

    /**
     * Constructor
     *
     */    
    public function __construct() {
      parent::__construct('Excel.Application');
    }
  }
?>
