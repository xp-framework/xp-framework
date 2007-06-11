<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('remote.beans.BeanInterface');

  /**
   * Interface for all home interfaces
   *
   * @purpose  Home Interface
   */
  interface HomeInterface extends BeanInterface {
  
    /**
     * Create method
     *
     * @return  remote.beans.Bean
     */
    public function create();
  }
?>
