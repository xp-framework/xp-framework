<?php
/* This class is part of the XP framework
 *
 * $Id: HomeInterface.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace remote::beans;

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
