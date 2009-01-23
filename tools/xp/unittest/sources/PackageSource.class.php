<?php
/* This Package is part of the XP framework
 *
 * $Id$
 */

  uses('xp.unittest.sources.AbstractSource', 'lang.reflect.Package');

  /**
   * Source that load tests from a package
   *
   * @purpose  Source implementation
   */
  class PackageSource extends xp·unittest·sources·AbstractSource {
    protected
      $package= NULL;
    
    /**
     * Constructor
     *
     * @param   lang.reflect.Package package
     */
    public function __construct(Package $package) {
      $this->package= $package;
    }

    /**
     * Get all test Packagees
     *
     * @return  util.collections.HashTable<lang.XPPackage, lang.types.ArrayList>
     */
    public function testClasses() {
      $tests= create('new util.collections.HashTable<lang.XPClass, lang.types.ArrayList>()');
      foreach ($this->package->getClasses() as $class) {
        if (
          !$class->isSubclassOf('unittest.TestCase') ||
          Modifiers::isAbstract($class->getModifiers())
        ) continue;
        
        $tests->put($class, new ArrayList());
      }
      return $tests;
    }
    
    /**
     * Creates a string representation of this source
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'['.$this->package->toString().']';
    }
  }
?>
