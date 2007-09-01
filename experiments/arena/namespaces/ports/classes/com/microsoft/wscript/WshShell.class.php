<?php
/* This class is part of the XP framework
 *
 * $Id: WshShell.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace com::microsoft::wscript;

  ::uses('com.microsoft.com.COMObject');
 
  /**
   * WScript.Shell wrapper
   * 
   * @see      http://msdn.microsoft.com/library/en-us/script56/html/wsObjWshShell.asp
   * @ext      com
   * @purpose  Singleton for shell object
   * @platform Windows
   */
  class WshShell extends com::microsoft::com::COMObject {

    /**
     * Constructor
     *
     */    
    public function __construct() {
      parent::__construct('WScript.Shell');
    }
    
    /**
     * Retrieves instance of this object
     *
     * @return  &com.microsoft.wscript.WshShell
     */
    public static function getInstance() {
      static $instance= NULL;
      
      if (!$instance) $instance= new WshShell();
      return $instance;
    }
  }
?>
