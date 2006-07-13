<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  define('INVOCATION_TYPE_HOME',  'invokeHome');
  define('INVOCATION_TYPE_BEAN',  'invoke');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class StatelessSessionBeanContainerInvocationHandler extends Object {
    var
      $oid        = NULL,
      $container  = NULL,
      $type       = NULL;
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setContainer(&$container) {
      $this->container= &$container;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setType($type) {
      $this->type= $type;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setOID($oid) {
      $this->oid= $oid;
    }

    /**
     * Processes a method invocation on a proxy instance and returns
     * the result.
     *
     * @access  public
     * @param   lang.reflect.Proxy proxy
     * @param   string method the method name
     * @param   mixed* args an array of arguments
     * @return  mixed
     */
    function invoke(&$proxy, $method, $args) {
      switch ($this->type) {
        case INVOCATION_TYPE_HOME: {
          return $this->container->{INVOCATION_TYPE_HOME}($method, $args);
        }
        
        case INVOCATION_TYPE_BEAN: {
          return $this->container->{INVOCATION_TYPE_BEAN}($this->oid, $method, $args);
        }
      }
    }
  } implements(__FILE__, 'lang.reflect.InvocationHandler');
?>
