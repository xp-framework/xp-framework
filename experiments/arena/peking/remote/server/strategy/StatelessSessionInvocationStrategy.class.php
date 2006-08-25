<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'remote.reflect.InterfaceUtil',
    'lang.reflect.Proxy'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class StatelessSessionInvocationStrategy extends Object {
    var
      $poolSize = 1;

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function &invoke(&$instance, $method, $args) {
      if (!$method) return FALSE;
      
      $ret= $method->invoke($instance, $args);
      return $ret;
    }
  }
?>
