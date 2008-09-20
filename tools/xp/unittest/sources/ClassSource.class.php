<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.unittest.sources.AbstractSource', 'io.File');

  /**
   * Source that load tests from a class filename
   *
   * @purpose  Source implementation
   */
  class ClassSource extends xp·unittest·sources·AbstractSource {
    protected
      $testClass= NULL;
    
    /**
     * Constructor
     *
     * @param   lang.XPClass testClass
     */
    public function __construct(XPClass $testClass) {
      $this->testClass= $testClass;
    }

    /**
     * Get all test classes
     *
     * @return  util.collections.HashTable<lang.XPClass, lang.types.ArrayList>
     */
    public function testClasses() {
      $tests= new HashTable();
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
