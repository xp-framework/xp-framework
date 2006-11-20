<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'util.Date',
    'info.binford6100.Date',
    'de.thekid.util.ObjectComparator'
  );

  /**
   * Tests RFC #0037
   *
   * @see      rfc://0037
   * @purpose  Unit test
   */
  class FullyQualifiedTest extends TestCase {

    /**
     * Tests that util.Date and info.binford6100.Date can coexist
     *
     * @access  public
     */
    #[@test]
    function dateClassesCanCoexist() {
      $bd= &new info·binford6100·Date();
      $ud= &new Date();

      $this->assertEquals('info.binford6100.Date', $bd->getClassName());
      $this->assertEquals('util.Date', $ud->getClassName());
    }

    /**
     * Tests that XPClass::forName() returns distinct class
     * objects for the util.Date and info.binford6100.Date
     * classes.
     *
     * @access  public
     */
    #[@test]
    function classObjectsAreNotEqual() {
      $bc= &XPClass::forName('info.binford6100.Date');
      $uc= &XPClass::forName('util.Date');
      $this->assertNotEquals($bc, $uc);
    }

    /**
     * Tests that XPClass::forName() loads fully qualified classes 
     * correctly.
     *
     * @access  public
     */
    #[@test]
    function dynamicallyLoaded() {
      $class= &XPClass::forName('de.thekid.List');
      $this->assertEquals('de.thekid.List', $class->getName());
      $instance= &$class->newInstance();
      $this->assertEquals('de.thekid.List@{}', $instance->toString());
    }

    /**
     * Tests that XPClass::forName() loads fully qualified classes 
     * correctly.
     *
     * @access  public
     */
    #[@test]
    function interfaceImplemented() {
      $class= &XPClass::forName('de.thekid.util.ObjectComparator');
      $interfaces= $class->getInterfaces();
      $this->assertEquals(1, sizeof($interfaces)) &&
      $this->assertEquals('de.thekid.util.Comparator', $interfaces[0]->getName());
    }
  }
?>
