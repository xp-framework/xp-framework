<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Date');

  /**
   * Test Class
   *
   * @purpose  Fixture for MethodInvocationTest
   */
  class TestClass extends Object {

    /**
     * Method with var-args
     *
     * @access  public
     * @param   string statement
     * @param   mixed* args
     * @return  string
     */
    function format($statement) {
      $args= func_get_args();
      return vsprintf($statement, array_slice($args, 1));
    }

    /**
     * Method with one typed-array argument
     *
     * @access  public
     * @param   string[] names
     */
    function setNames($names) {
      $this->names= $names;
    }

    /**
     * Method with a typed-hash argument
     *
     * @access  public
     * @param   array<string, &util.Date> hash
     * @param   string operation one of 'isAfter' / 'isBefore'
     * @param   &util.Date compare
     * @return  array<string, &util.Date> filtered hash
     */
    function filter($hash, $operation, &$compare) {
      foreach (array_keys($hash) as $key) {
        if ($hash[$key]->{$operation}($compare)) unset($hash[$key]);
      }
      return $hash;
    }

    /**
     * Method with two primitive arguments
     *
     * @access  public
     * @param   int a
     * @param   int b
     * @return  int
     */
    function add($a, $b) {
      return $a + $b;
    }
  
    /**
     * Method with one argument
     *
     * @access  public
     * @param   &util.Date date
     */
    function setDate(&$date) {
      $this->date= &$date;
    }
    
    /**
     * A method without arguments
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return $this->getClassName();
    }
  
  }
?>
