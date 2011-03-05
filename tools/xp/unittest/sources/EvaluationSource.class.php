<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.unittest.sources.AbstractSource', 'io.File');

  /**
   * Source that dynamically creates testcases
   *
   * @purpose  Source implementation
   */
  class EvaluationSource extends xp·unittest·sources·AbstractSource {
    protected static
      $uniqId    = 0;

    protected
      $testClass = NULL;
    
    /**
     * Constructor
     *
     * @param   string bytes method sourcecode
     */
    public function __construct($bytes) {
      $name= 'xp.unittest.DynamicallyGeneratedTestCase·'.(self::$uniqId++);
      $this->testClass= ClassLoader::defineClass($name, 'unittest.TestCase', array(), '{
        #[@test] 
        public function run() { '.$bytes.' }
      }');
    }

    /**
     * Get all test cases
     *
     * @param   var[] arguments
     * @return  unittest.TestCase[]
     */
    public function testCasesWith($arguments) {
      return array($this->testClass->newInstance('run'));
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
