<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase'
  );

  /**
   * TestCase
   *
   * @see      reference
   * @purpose  purpose
   */
  class ClassLoaderTest extends TestCase {
  
    /**
     * Test
     *
     */
    #[@test]
    public function twoPublicClasses() {
      $this->assertEquals(
        XPClass::forName('util.Binford')->getClassLoader(),
        XPClass::forName('util.cmd.ParamString')->getClassLoader()
      );
    }
  }
?>
