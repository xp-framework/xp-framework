<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.log.Logger', 'rdbms.DBObserver');

  /**
   * Observer interface
   *
   * @see      xp://util.Observable
   * @purpose  Interface
   */
  class LogObserver extends Object implements DBObserver {
    public
      $cat=   NULL;

    /**
     * Retrieve instance bound to log category.
     *
     * @param   string arg
     * @return  util.log.LogObserver
     */
    public static function instanceFor($arg) {
      static $inst= array();
      
      if (!isset ($inst[$arg])) {
        $log= Logger::getInstance();
        $inst[$arg]= new LogObserver();
        $inst[$arg]->cat= $log->getCategory($arg);
      }
      
      return $inst[$arg];
    }
  
    /**
     * Update method
     *
     * @param   util.Observable obs
     * @param   mixed arg default NULL
     */
    public function update($obs, $arg= NULL) {
      $this->cat->debug($arg);
    }
  } 
?>
