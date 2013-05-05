<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'lang.Runtime'
  );

  /**
   * TestCase
   *
   * @see   xp://lang.Runtime
   */
  class RuntimeTest extends TestCase {
    
    /**
     * Test getExecutable() method
     *
     */
    #[@test]
    public function getExecutable() {
      $exe= Runtime::getInstance()->getExecutable();
      $this->assertInstanceOf('lang.Process', $exe);
      $this->assertEquals(getmypid(), $exe->getProcessId());
    }

    /**
     * Test extensionAvailable() method
     *
     */
    #[@test]
    public function standardExtensionAvailable() {
      $this->assertTrue(Runtime::getInstance()->extensionAvailable('standard'));
    }

    /**
     * Test extensionAvailable() method
     *
     */
    #[@test]
    public function nonExistantExtension() {
      $this->assertFalse(Runtime::getInstance()->extensionAvailable(':DOES-NOT-EXIST"'));
    }
 
    /**
     * Test startupOptions() method
     *
     */
    #[@test]
    public function startupOptions() {
      $startup= Runtime::getInstance()->startupOptions();
      $this->assertInstanceOf('lang.RuntimeOptions', $startup);
    }

    /**
     * Test modifications made on object returned by startupOptions()
     * do not modify the runtime's startup options themselves.
     *
     */
    #[@test]
    public function modifiedStartupOptions() {
      $startup= Runtime::getInstance()->startupOptions();
      $modified= Runtime::getInstance()->startupOptions()->withSwitch('n');
      $this->assertNotEquals($startup, $modified);
    }

    /**
     * Test bootstrapScript() method
     *
     */
    #[@test]
    public function bootstrapScript() {
      $bootstrap= Runtime::getInstance()->bootstrapScript();
      $this->assertTrue(strstr($bootstrap, 'tools') && strstr($bootstrap, '.php'), $bootstrap);
    }

    /**
     * Test bootstrapScript() method
     *
     */
    #[@test]
    public function certainBootstrapScript() {
      $bootstrap= Runtime::getInstance()->bootstrapScript('class');
      $this->assertTrue(strstr($bootstrap, 'tools') && strstr($bootstrap, 'class.php'), $bootstrap);
    }

    /**
     * Test mainClass() method
     *
     */
    #[@test]
    public function mainClass() {
      $main= Runtime::getInstance()->mainClass();
      $this->assertInstanceOf('lang.XPClass', $main);
    }

    /**
     * Test parse() method
     *
     */
    #[@test]
    public function parseSetting() {
      $startup= Runtime::parseArguments(array('-denable_dl=0'));
      $this->assertEquals(array('0'), $startup['options']->getSetting('enable_dl'));
    }

    /**
     * Test parse() method
     *
     */
    #[@test]
    public function parseSettingToleratesWhitespace() {
      $startup= Runtime::parseArguments(array('-d magic_quotes_gpc=0'));
      $this->assertEquals(array('0'), $startup['options']->getSetting('magic_quotes_gpc'));
    }

    /**
     * Test parse() method
     *
     */
    #[@test]
    public function doubleDashEndsOptions() {
      $startup= Runtime::parseArguments(array('-q', '--', 'tools/xar.php'));
      $this->assertEquals(array('-q'), $startup['options']->asArguments());
      $this->assertEquals('tools/xar.php', $startup['bootstrap']);
    }

    /**
     * Test parse() method
     *
     */
    #[@test]
    public function scriptEndsOptions() {
      $startup= Runtime::parseArguments(array('-q', 'tools/xar.php'));
      $this->assertEquals(array('-q'), $startup['options']->asArguments());
      $this->assertEquals('tools/xar.php', $startup['bootstrap']);
    }

    /**
     * Test parse() method
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function parseUnknownSwtich() {
      Runtime::parseArguments(array('-@'));
    }

    /**
     * Test parse() method
     *
     */
    #[@test]
    public function parseMultiSetting() {
      $startup= Runtime::parseArguments(array(
        '-dextension=php_xsl.dll', 
        '-dextension=php_sybase_ct.dll'
      ));
      $this->assertEquals(
        array('php_xsl.dll', 'php_sybase_ct.dll'), 
        $startup['options']->getSetting('extension')
      );
    }

    /**
     * Test parse() method
     *
     */
    #[@test]
    public function parseSwitch() {
      $startup= Runtime::parseArguments(array('-q'));
      $this->assertTrue($startup['options']->getSwitch('q'));
    }

    /**
     * Test memoryUsage() method
     *
     */
    #[@test]
    public function memoryUsage() {
      $this->assertEquals(
        Primitive::$INT, 
        Type::forName(gettype(Runtime::getInstance()->memoryUsage()))
      );
    }

    /**
     * Test peakMemoryUsage() method
     *
     */
    #[@test]
    public function peakMemoryUsage() {
      $this->assertEquals(
        Primitive::$INT, 
        Type::forName(gettype(Runtime::getInstance()->peakMemoryUsage()))
      );
    }

    /**
     * Test memoryLimit() method
     *
     */
    #[@test]
    public function memoryLimit() {
      $this->assertEquals(
        Primitive::$INT,
        Type::forName(gettype(Runtime::getInstance()->memoryLimit()))
      );
    }
  }
?>
