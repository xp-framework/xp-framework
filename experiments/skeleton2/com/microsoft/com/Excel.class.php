<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('com.microsoft.com.COMObject');

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
     * @access  public
     */    
    public function __construct() {
      parent::__construct('Excel.Application');
    }
  }
?>
