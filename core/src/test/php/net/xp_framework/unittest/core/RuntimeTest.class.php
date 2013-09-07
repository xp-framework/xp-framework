<?php namespace net\xp_framework\unittest\core;

use unittest\TestCase;
use lang\Runtime;

/**
 * TestCase
 *
 * @see   xp://lang.Runtime
 */
class RuntimeTest extends TestCase {
  
  #[@test]
  public function getExecutable() {
    $exe= Runtime::getInstance()->getExecutable();
    $this->assertInstanceOf('lang.Process', $exe);
    $this->assertEquals(getmypid(), $exe->getProcessId());
  }

  #[@test]
  public function standardExtensionAvailable() {
    $this->assertTrue(Runtime::getInstance()->extensionAvailable('standard'));
  }

  #[@test]
  public function nonExistantExtension() {
    $this->assertFalse(Runtime::getInstance()->extensionAvailable(':DOES-NOT-EXIST"'));
  }
 
  #[@test]
  public function startupOptions() {
    $startup= Runtime::getInstance()->startupOptions();
    $this->assertInstanceOf('lang.RuntimeOptions', $startup);
  }

  #[@test]
  public function modifiedStartupOptions() {
    $startup= Runtime::getInstance()->startupOptions();
    $modified= Runtime::getInstance()->startupOptions()->withSwitch('n');
    $this->assertNotEquals($startup, $modified);
  }

  #[@test]
  public function bootstrapScript() {
    $bootstrap= Runtime::getInstance()->bootstrapScript();
    $this->assertTrue(strstr($bootstrap, 'tools') && strstr($bootstrap, '.php'), $bootstrap);
  }

  #[@test]
  public function certainBootstrapScript() {
    $bootstrap= Runtime::getInstance()->bootstrapScript('class');
    $this->assertTrue(strstr($bootstrap, 'tools') && strstr($bootstrap, 'class.php'), $bootstrap);
  }

  #[@test]
  public function mainClass() {
    $main= Runtime::getInstance()->mainClass();
    $this->assertInstanceOf('lang.XPClass', $main);
  }

  #[@test]
  public function parseSetting() {
    $startup= Runtime::parseArguments(array('-denable_dl=0'));
    $this->assertEquals(array('0'), $startup['options']->getSetting('enable_dl'));
  }

  #[@test]
  public function parseSettingToleratesWhitespace() {
    $startup= Runtime::parseArguments(array('-d magic_quotes_gpc=0'));
    $this->assertEquals(array('0'), $startup['options']->getSetting('magic_quotes_gpc'));
  }

  #[@test]
  public function doubleDashEndsOptions() {
    $startup= Runtime::parseArguments(array('-q', '--', 'tools/xar.php'));
    $this->assertEquals(array('-q'), $startup['options']->asArguments());
    $this->assertEquals('tools/xar.php', $startup['bootstrap']);
  }

  #[@test]
  public function scriptEndsOptions() {
    $startup= Runtime::parseArguments(array('-q', 'tools/xar.php'));
    $this->assertEquals(array('-q'), $startup['options']->asArguments());
    $this->assertEquals('tools/xar.php', $startup['bootstrap']);
  }

  #[@test, @expect('lang.FormatException')]
  public function parseUnknownSwtich() {
    Runtime::parseArguments(array('-@'));
  }

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

  #[@test]
  public function parseSwitch() {
    $startup= Runtime::parseArguments(array('-q'));
    $this->assertTrue($startup['options']->getSwitch('q'));
  }

  #[@test]
  public function memoryUsage() {
    $this->assertEquals(
      \lang\Primitive::$INT, 
      \lang\Type::forName(gettype(Runtime::getInstance()->memoryUsage()))
    );
  }

  #[@test]
  public function peakMemoryUsage() {
    $this->assertEquals(
      \lang\Primitive::$INT, 
      \lang\Type::forName(gettype(Runtime::getInstance()->peakMemoryUsage()))
    );
  }

  #[@test]
  public function memoryLimit() {
    $this->assertEquals(
      \lang\Primitive::$INT,
      \lang\Type::forName(gettype(Runtime::getInstance()->memoryLimit()))
    );
  }
}
