<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  abstract class InetAddress extends Object {
    protected
      $addr = NULL;

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public abstract function getAddress();
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public abstract function isLoopback();
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public abstract function inSubnet($net);    
  }
?>
