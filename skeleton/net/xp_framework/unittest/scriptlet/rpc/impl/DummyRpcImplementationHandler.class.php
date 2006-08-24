<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Dummy class for unittest
   *
   * @purpose  Dummy implementation
   */
  class DummyRpcImplementationHandler extends Object {
  
    /**
     * Dummy method
     *
     * @access  public
     * @return  string
     */
    #[@webmethod]
    function getImplementationName() {
      return $this->getClassName();
    }
    
    /**
     * Non-web-invokeable method.
     *
     * @access  public
     * @return  bool
     */    
    function methodExistsButIsNotAWebmethod() {
      return TRUE;
    }

    /**
     * Dummy method
     *
     * @access  public
     * @return  string
     */
    #[@webmethod]
    function giveMeFault() {
      return throw(new ServiceException(403, 'This is a intentionally caused exception.'));
    }
    
    /**
     * Method which checks for the types it receives in a hardcoded
     * manner
     *
     * @access  public
     * @param   string string
     * @param   int int
     * @param   array array
     * @param   array struct
     * @return  array
     */
    #[@webmethod]
    function checkMultipleParameters($string, $int, $array, $struct) {
      if (!is_string($string)) return throw(new IllegalArgumentException('String not string'));
      if (!is_int($int)) return throw(new IllegalArgumentException('Int not Int'));
      if (!is_array($array)) return throw(new IllegalArgumentException('Array not array'));
      if (!is_array($struct)) return throw(new IllegalArgumentException('Struct not struct'));
      
      return array(
        $string,
        $int,
        $array,
        $struct
      );
    }
    
    /**
     * Method which returns what it gets
     *
     * @access  public
     * @param   mixed
     * @return  mixed
     */
    #[@webmethod]
    function passBackMethod() {
      $args= func_get_args();
      return $args;
    }
  }
?>
