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
     * @return  string
     */
    #[@webmethod]
    public function getImplementationName() {
      return $this->getClassName();
    }
    
    /**
     * Non-web-invokeable method.
     *
     * @return  bool
     */    
    public function methodExistsButIsNotAWebmethod() {
      return TRUE;
    }

    /**
     * Dummy method
     *
     * @return  string
     */
    #[@webmethod]
    public function giveMeFault() {
      throw(new ServiceException(403, 'This is a intentionally caused exception.'));
    }
    
    /**
     * Method which checks for the types it receives in a hardcoded
     * manner
     *
     * @param   string string
     * @param   int int
     * @param   array array
     * @param   array struct
     * @return  array
     */
    #[@webmethod]
    public function checkMultipleParameters($string, $int, $array, $struct) {
      if (!is_string($string)) throw(new IllegalArgumentException('String not string'));
      if (!is_int($int)) throw(new IllegalArgumentException('Int not Int'));
      if (!is_array($array)) throw(new IllegalArgumentException('Array not array'));
      if (!is_array($struct)) throw(new IllegalArgumentException('Struct not struct'));
      
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
     * @param   mixed
     * @return  mixed
     */
    #[@webmethod]
    public function passBackMethod() {
      $args= func_get_args();
      return $args;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@webmethod]
    public function checkUTF8Content($string) {
      var_dump($string);
      if ('Störung in Düsseldorf' !== $string) {
        throw new IllegalArgumentException('Invalid encoding: "'.$string.'"');
      }
    }
  }
?>
