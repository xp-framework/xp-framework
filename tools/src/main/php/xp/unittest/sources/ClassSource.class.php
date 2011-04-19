<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.unittest.sources.AbstractSource');

  /**
   * Source that load tests from a class filename
   *
   * @purpose  Source implementation
   */
  class ClassSource extends xp·unittest·sources·AbstractSource {
    protected $testClass= NULL;
    protected $method= NULL;
    
    /**
     * Constructor
     *
     * @param   lang.XPClass testClass
     * @param   string method default NULL
     */
    public function __construct(XPClass $testClass, $method= NULL) {
      $this->testClass= $testClass;
      $this->method= $method;
    }

    /**
     * Get all test cases
     *
     * @param   var[] arguments
     * @return  unittest.TestCase[]
     */
    public function testCasesWith($arguments) {
      if (NULL === $this->method) {
        return $this->testCasesInClass($this->testClass, $arguments);
      }
      
      return array($this->testClass->getConstructor()->newInstance(array_merge(
        (array)$this->method, 
        (array)$arguments
      )));
    }

    /**
     * Creates a string representation of this source
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'['.$this->testClass->toString().']';
    }
  }
?>
