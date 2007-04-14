<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('ant.task.AntTask');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class AntTstampTask extends AntTask {
  
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function run(AntEnvironment $env) {
      $env->put('DSTAMP', Date::now()->toString('Ymd'));
      $env->put('TSTAMP', Date::now()->toString('hi'));
      $env->put('TODAY', Date::now()->toString('M d Y'));
    }
    
  }
?>
