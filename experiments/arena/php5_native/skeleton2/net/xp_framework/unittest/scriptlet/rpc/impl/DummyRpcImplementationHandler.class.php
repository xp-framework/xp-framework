<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Blah
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
    public function getImplementationName() {
      return $this->getClassName();
    }
    
    /**
     * Non-web-invokeable method.
     *
     * @access  public
     * @return  bool
     */    
    public function methodExistsButIsNotAWebmethod() {
      return TRUE;
    }

    /**
     * Dummy method
     *
     * @access  public
     * @return  string
     */
    #[@webmethod]
    public function giveMeFault() {
      throw(new ServiceException(403, 'This is a intentionally caused exception.'));
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
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
     * @access  public
     * @param   mixed
     * @return  mixed
     */
    #[@webmethod]
    public function passBackMethod() {
      $args= func_get_args();
      return $args;
    }
  }
?>
