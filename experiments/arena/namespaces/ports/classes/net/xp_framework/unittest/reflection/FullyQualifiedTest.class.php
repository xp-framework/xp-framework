<?php
/* This class is part of the XP framework
 *
 * $Id: FullyQualifiedTest.class.php 10296 2007-05-08 19:22:33Z friebe $
 */

  namespace net::xp_framework::unittest::reflection;

  ::uses(
    'unittest.TestCase',
    'util.Date'
  );

  /**
   * Tests RFC #0037
   *
   * @see      rfc://0037
   * @purpose  Unit test
   */
  class FullyQualifiedTest extends unittest::TestCase {

    static function __static() {
      lang::ClassLoader::registerLoader(new lang::archive::ArchiveClassLoader(
        new lang::archive::ArchiveReader(dirname(__FILE__).'/lib/fqcns.xar')
      ));
      lang::XPClass::forName('info.binford6100.Date');
      lang::XPClass::forName('de.thekid.util.ObjectComparator');
    }

    /**
     * Tests that util.Date and info.binford6100.Date can coexist
     *
     */
    #[@test]
    public function dateClassesCanCoexist() {
      $bd= new info·binford6100·Date();
      $ud= new util::Date();

      $this->assertEquals('info.binford6100.Date', $bd->getClassName());
      $this->assertEquals('util.Date', $ud->getClassName());
    }

    /**
     * Tests that XPClass::forName() returns distinct class
     * objects for the util.Date and info.binford6100.Date
     * classes.
     *
     */
    #[@test]
    public function classObjectsAreNotEqual() {
      $bc= lang::XPClass::forName('info.binford6100.Date');
      $uc= lang::XPClass::forName('util.Date');
      $this->assertNotEquals($bc, $uc);
    }

    /**
     * Tests that XPClass::forName() loads fully qualified classes 
     * correctly.
     *
     */
    #[@test]
    public function dynamicallyLoaded() {
      $class= lang::XPClass::forName('de.thekid.List');
      $this->assertEquals('de.thekid.List', $class->getName());
      $instance= $class->newInstance();
      $this->assertEquals('de.thekid.List@{}', $instance->toString());
    }

    /**
     * Tests that XPClass::forName() loads fully qualified classes 
     * correctly.
     *
     */
    #[@test]
    public function interfaceImplemented() {
      $class= lang::XPClass::forName('de.thekid.util.ObjectComparator');
      $interfaces= $class->getInterfaces();
      $this->assertEquals(2, sizeof($interfaces));
      $this->assertEquals('lang.Generic', $interfaces[0]->getName());
      $this->assertEquals('de.thekid.util.Comparator', $interfaces[1]->getName());
    }
  }
?>
