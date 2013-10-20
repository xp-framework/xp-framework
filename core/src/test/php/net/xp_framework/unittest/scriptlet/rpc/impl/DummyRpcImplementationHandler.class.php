<?php namespace net\xp_framework\unittest\scriptlet\rpc\impl;

/**
 * Dummy class for unittest
 */
class DummyRpcImplementationHandler extends \lang\Object {

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
    return true;
  }

  /**
   * Dummy method
   *
   * @return  string
   */
  #[@webmethod]
  public function giveMeFault() {
    throw new \util\ServiceException(403, 'This is a intentionally caused exception.');
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
    if (!is_string($string) && !$string instanceof \lang\types\String) throw new \lang\IllegalArgumentException('String not string');
    if (!is_int($int)) throw new \lang\IllegalArgumentException('Int not Int');
    if (!is_array($array)) throw new \lang\IllegalArgumentException('Array not array');
    if (!is_array($struct)) throw new \lang\IllegalArgumentException('Struct not struct');
    
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
    if ('Störung in Düsseldorf' !== $string) {
      throw new \lang\IllegalArgumentException('Invalid encoding: "'.$string.'"');
    }
    
    return $string;
  }
}
