<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Fluent interface class
   *
   * Usage:
   * <code>
   *   new Expectancy()->method('sayHello')->isInvoked(1)->with(array('World'));
   * </code>
   *
   * @see      http://martinfowler.com/bliki/FluentInterface.html
   * @see      xp://ChainTest
   * @purpose  Utility class for testcase
   */
  class Expectancy extends Object {
    var
      $expects  = array();
  
    /**
     * Sets method
     *
     * @access  public
     * @param   string name
     * @return  &Expectancy
     */
    function &method($name) {
      $this->expects['method']= $name;
      return $this;
    }
    
    /**
     * Sets how many times a method is invoked
     *
     * @access  public
     * @param   int times
     * @return  &Expectancy
     */
    function &isInvoked($times) {
      $this->expects['times']= $times;
      return $this;
    }
    
    /**
     * Sets arguments
     *
     * @access  public
     * @param   mixed[] arguments
     * @return  &Expectancy
     */
    function &with($arguments) {
      $this->expects['arguments']= $arguments;
      return $this;
    }
    
    /**
     * Creates a string representation of this object
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return sprintf(
        'Expectancy(method %s invoked %d times with [%s] as arguments)',
        $this->expects['method'],
        $this->expects['times'],
        implode(', ', array_map(array('xp', 'stringOf'), $this->expects['arguments']))
      );
    }
  }
?>
