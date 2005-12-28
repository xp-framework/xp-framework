<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'util.profiling.unittest.TestCase',
    'util.Date',
    'info.binford6100.Date'
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
  }
?>
