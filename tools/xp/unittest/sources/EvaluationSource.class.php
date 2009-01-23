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
     * Get all test classes
     *
     * @return  util.collections.HashTable<lang.XPClass, lang.types.ArrayList>
     */
    public function testClasses() {
      $tests= create('new util.collections.HashTable<lang.XPClass, lang.types.ArrayList>()');
      $tests->put($this->testClass, new ArrayList());
      return $tests;
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
