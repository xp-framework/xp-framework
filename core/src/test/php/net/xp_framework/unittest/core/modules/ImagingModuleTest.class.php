<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'lang.reflect.Module'
  );

  /**
   * TestCase
   *
   */
  class ImagingModuleTest extends TestCase {
    protected static $loader= NULL;
    protected $fixture= NULL;
  
    /**
     * Register imaging module path. This will actually trigger loading it.
     *
     */
    #[@beforeClass]
    public static function registerPath() {
      self::$loader= ClassLoader::getDefault()->registerPath(dirname(__FILE__).'/imaging');
    }

    /**
     * Remove imaging module path.
     *
     */
    #[@afterClass]
    public static function removePath() {
      ClassLoader::getDefault()->removeLoader(self::$loader);
    }
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->fixture= Module::forName('imaging');
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function moduleName() {
      $this->assertEquals('imaging', $this->fixture->getName());
    }
  }
?>
