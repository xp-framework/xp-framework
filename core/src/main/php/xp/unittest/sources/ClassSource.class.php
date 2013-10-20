<?php namespace xp\unittest\sources;

/**
 * Source that load tests from a class filename
 */
class ClassSource extends AbstractSource {
  protected $testClass= null;
  protected $method= null;
  
  /**
   * Constructor
   *
   * @param   lang.XPClass testClass
   * @param   string method default NULL
   */
  public function __construct(\lang\XPClass $testClass, $method= null) {
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
    if (null === $this->method) {
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
