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
      $package    = NULL,
      $recursive  = FALSE;
    
    /**
     * Constructor
     *
     * @param   lang.reflect.Package package
     * @param   bool recursive default FALSE
     */
    public function __construct(Package $package, $recursive= FALSE) {
      $this->package= $package;
      $this->recursive= $recursive;
    }
    
    /**
     * Returns a list of all classes inside a given package
     *
     * @param   lang.reflect.Package 
     * @param   bool recursive whether to include subpackages
     * @return  lang.XPClass[]
     */
    protected static function testClassesIn(Package $package, $recursive) {
      $r= array();
      foreach ($package->getClasses() as $class) {
        if (
          !$class->isSubclassOf('unittest.TestCase') ||
          Modifiers::isAbstract($class->getModifiers())
        ) continue;
        
        $r[]= $class;
      }
      if ($recursive) foreach ($package->getPackages() as $package) {
        $r= array_merge($r, self::testClassesIn($package, $recursive));
      }
      return $r;
    }

    /**
     * Get all test Packagees
     *
     * @return  util.collections.HashTable<lang.XPClass, lang.types.ArrayList>
     */
    public function testClasses() {
      $tests= create('new util.collections.HashTable<lang.XPClass, lang.types.ArrayList>()');
      foreach (self::testClassesIn($this->package, $this->recursive) as $class) {
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
      return $this->getClassName().'['.$this->package->getName().($this->recursive ? '.**' : '.*').']';
    }
  }
?>
