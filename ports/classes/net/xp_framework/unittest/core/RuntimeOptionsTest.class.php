<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'lang.RuntimeOptions'
  );

  /**
   * TestCase
   *
   * @see      xp://lang.RuntimeOptions
   * @purpose  Unittest
   */
  class RuntimeOptionsTest extends TestCase {

    /**
     * Test getSwitch() and withSwitch() methods
     *
     */
    #[@test]
    public function switchAccessors() {
      $options= new RuntimeOptions();
      $this->assertFalse($options->getSwitch('q'));
      $options->withSwitch('q');
      $this->assertTrue($options->getSwitch('q'));
    }

    /**
     * Test getSetting()
     *
     */
    #[@test]
    public function getSetting() {
      $options= new RuntimeOptions();
      $this->assertNull($options->getSetting('enable_dl'));
    }

    /**
     * Test getSetting()
     *
     */
    #[@test]
    public function getSettingWithDefault() {
      $options= new RuntimeOptions();
      $this->assertEquals(0, $options->getSetting('enable_dl', 0));
    }

    /**
     * Test getSetting() and withSetting() methods
     *
     */
    #[@test]
    public function settingAccessors() {
      $options= new RuntimeOptions();
      $this->assertNull($options->getSetting('memory_limit'));
      $options->withSetting('memory_limit', array('128M'));
      $this->assertEquals(array('128M'), $options->getSetting('memory_limit'));
    }

    /**
     * Test withSetting() method overloaded for strings
     *
     */
    #[@test]
    public function settingAccessorsStringOverload() {
      $options= new RuntimeOptions();
      $this->assertNull($options->getSetting('memory_limit'));
      $options->withSetting('memory_limit', '128M');
      $this->assertEquals(array('128M'), $options->getSetting('memory_limit'));
    }

    /**
     * Test adding settings
     *
     */
    #[@test]
    public function addSetting() {
      $options= new RuntimeOptions();
      $options->withSetting('extension', 'php_xsl.dll', TRUE);
      $options->withSetting('extension', 'php_sybase_ct.dll', TRUE);
      $this->assertEquals(
        array('php_xsl.dll', 'php_sybase_ct.dll'), 
        $options->getSetting('extension')
      );
    }

    /**
     * Test overwriting settings
     *
     */
    #[@test]
    public function overwritingSetting() {
      $options= new RuntimeOptions();
      $options->withSetting('extension', 'php_xsl.dll');
      $options->withSetting('extension', 'php_sybase_ct.dll');
      $this->assertEquals(
        array('php_sybase_ct.dll'), 
        $options->getSetting('extension')
      );
    }

    /**
     * Test withSwitch() method
     *
     */
    #[@test]
    public function chainingSwitch() {
      $options= new RuntimeOptions();
      $this->assertTrue($options === $options->withSwitch('q'));
    }

    /**
     * Test withSetting() method
     *
     */
    #[@test]
    public function chainingSetting() {
      $options= new RuntimeOptions();
      $this->assertTrue($options === $options->withSetting('enable_dl', 0));
    }

    /**
     * Test getClassPath() method
     *
     */
    #[@test]
    public function getClassPath() {
      $options= new RuntimeOptions();
      $this->assertEquals(array(), $options->getClassPath());
    }

    /**
     * Test withClassPath() method
     *
     */
    #[@test]
    public function withClassPath() {
      $options= new RuntimeOptions();
      $options->withClassPath(array('/opt/xp/lib/mysql-1.0.0.xar'));
      $this->assertEquals(array('/opt/xp/lib/mysql-1.0.0.xar'), $options->getClassPath());
    }

    /**
     * Test withClassPath() method
     *
     */
    #[@test]
    public function withClassPathOverload() {
      $options= new RuntimeOptions();
      $options->withClassPath('/opt/xp/lib/mysql-1.0.0.xar');
      $this->assertEquals(array('/opt/xp/lib/mysql-1.0.0.xar'), $options->getClassPath());
    }

    /**
     * Test asArguments() method
     *
     */
    #[@test]
    public function argumentsOnEmptyOptions() {
      $options= new RuntimeOptions();
      $this->assertEquals(array(), $options->asArguments());
    }

    /**
     * Test asArguments() method
     *
     */
    #[@test]
    public function argumentsWithSwitch() {
      $options= new RuntimeOptions(); 
      $options->withSwitch('q');
      $this->assertEquals(array('-q'), $options->asArguments());
    }

    /**
     * Test asArguments() method
     *
     */
    #[@test]
    public function argumentsWithSetting() {
      $options= new RuntimeOptions(); 
      $options->withSetting('enable_dl', 0);
      $this->assertEquals(array('-denable_dl=0'), $options->asArguments());
    }

    /**
     * Test asArguments() method
     *
     */
    #[@test]
    public function argumentsWithMultiSetting() {
      $options= new RuntimeOptions(); 
      $options->withSetting('extension', array('php_xsl.dll', 'php_sybase_ct.dll'));
      $this->assertEquals(
        array('-dextension=php_xsl.dll', '-dextension=php_sybase_ct.dll'), 
        $options->asArguments()
      );
    }

    /**
     * Test asArguments() method
     *
     */
    #[@test]
    public function argumentsWithEmptyMultiSetting() {
      $options= new RuntimeOptions(); 
      $options->withSetting('extension', array());
      $this->assertEquals(array(), $options->asArguments());
    }

    /**
     * Test asArguments() method
     *
     */
    #[@test]
    public function arguments() {
      $options= create(new RuntimeOptions())
        ->withSwitch('q')
        ->withSwitch('n')
        ->withSetting('enable_dl', 1)
        ->withSetting('extension', array('php_xsl.dll', 'php_sybase_ct.dll'))
      ;
      $this->assertEquals(
        array('-q', '-n', '-denable_dl=1', '-dextension=php_xsl.dll', '-dextension=php_sybase_ct.dll'), 
        $options->asArguments()
      );
    }

    /**
     * Test asArguments() method
     *
     */
    #[@test]
    public function classPathDoesntAppearInArguments() {
      $options= new RuntimeOptions(); 
      $options->withClassPath('/opt/xp/lib/mysql-1.0.0.xar');
      $this->assertEquals(array(), $options->asArguments());
    }
  }
?>
