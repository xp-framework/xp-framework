<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('com.microsoft.com.COMObject');

  /**
   * WScript.Shell wrapper
   * 
   * @see      http://msdn.microsoft.com/library/en-us/script56/html/wsObjWshShell.asp
   * @ext      com
   * @purpose  Singleton for shell object
   * @platform Windows
   */
  class WshShell extends COMObject {
    protected static $instance= NULL;

    /**
     * Constructor
     *
     * @access  public
     */    
    public function __construct() {
      parent::__construct('WScript.Shell');
    }
    
    /**
     * Retrieves instance of this object
     *
     * @model   static
     * @access  public
     * @return  &com.microsoft.wscript.WshShell
     */
    public static function getInstance() {
      if (!self::$instance) self::$instance= new WshShell();
      return self::$instance;
    }
  }
?>
