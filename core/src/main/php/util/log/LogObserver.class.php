<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.log.Logger', 'util.log.BoundLogObserver');

  /**
   * Observer interface
   *
   * @see      xp://util.Observable
   * @purpose  Interface
   */
  class LogObserver extends Object implements BoundLogObserver {
    public $cat= NULL;
    
    /**
     * Creates a new log observer with a given log category.
     *
     * @param   util.log.LogCategory cat
     */
    public function __construct(LogCategory $cat) {
      $this->cat= $cat;
    }

    /**
     * Retrieve instance bound to log category.
     *
     * @param   string arg
     * @return  util.log.LogObserver
     */
    public static function instanceFor($arg) {
      static $inst= array();
      
      if (!isset($inst[$arg])) {
        $inst[$arg]= new self(Logger::getInstance()->getCategory($arg));
      }
      
      return $inst[$arg];
    }
  
    /**
     * Update method
     *
     * @param   util.Observable obs
     * @param   var arg default NULL
     */
    public function update($obs, $arg= NULL) {
      $this->cat->debug($arg);
    }
  } 
?>
