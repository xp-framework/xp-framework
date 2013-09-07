<?php namespace net\xp_framework\unittest\core;

use unittest\TestCase;
use lang\RuntimeOptions;


/**
 * TestCase
 *
 * @see      xp://lang.RuntimeOptions
 * @purpose  Unittest
 */
class RuntimeOptionsTest extends TestCase {

  #[@test]
  public function switchAccessors() {
    $options= new RuntimeOptions();
    $this->assertFalse($options->getSwitch('q'));
    $options->withSwitch('q');
    $this->assertTrue($options->getSwitch('q'));
  }

  #[@test]
  public function getSetting() {
    $options= new RuntimeOptions();
    $this->assertNull($options->getSetting('enable_dl'));
  }

  #[@test]
  public function getSettingWithDefault() {
    $options= new RuntimeOptions();
    $this->assertEquals(0, $options->getSetting('enable_dl', 0));
  }

  #[@test]
  public function settingAccessors() {
    $options= new RuntimeOptions();
    $this->assertNull($options->getSetting('memory_limit'));
    $options->withSetting('memory_limit', array('128M'));
    $this->assertEquals(array('128M'), $options->getSetting('memory_limit'));
  }

  #[@test]
  public function settingAccessorsStringOverload() {
    $options= new RuntimeOptions();
    $this->assertNull($options->getSetting('memory_limit'));
    $options->withSetting('memory_limit', '128M');
    $this->assertEquals(array('128M'), $options->getSetting('memory_limit'));
  }

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

  #[@test]
  public function chainingSwitch() {
    $options= new RuntimeOptions();
    $this->assertTrue($options === $options->withSwitch('q'));
  }

  #[@test]
  public function chainingSetting() {
    $options= new RuntimeOptions();
    $this->assertTrue($options === $options->withSetting('enable_dl', 0));
  }

  #[@test]
  public function getClassPath() {
    $options= new RuntimeOptions();
    $this->assertEquals(array(), $options->getClassPath());
  }

  #[@test]
  public function withClassPath() {
    $options= new RuntimeOptions();
    $options->withClassPath(array('/opt/xp/lib/mysql-1.0.0.xar'));
    $this->assertEquals(array('/opt/xp/lib/mysql-1.0.0.xar'), $options->getClassPath());
  }

  #[@test]
  public function withClassPathOverload() {
    $options= new RuntimeOptions();
    $options->withClassPath('/opt/xp/lib/mysql-1.0.0.xar');
    $this->assertEquals(array('/opt/xp/lib/mysql-1.0.0.xar'), $options->getClassPath());
  }

  #[@test]
  public function argumentsOnEmptyOptions() {
    $options= new RuntimeOptions();
    $this->assertEquals(array(), $options->asArguments());
  }

  #[@test]
  public function argumentsWithSwitch() {
    $options= new RuntimeOptions(); 
    $options->withSwitch('q');
    $this->assertEquals(array('-q'), $options->asArguments());
  }

  #[@test]
  public function argumentsWithSetting() {
    $options= new RuntimeOptions(); 
    $options->withSetting('enable_dl', 0);
    $this->assertEquals(array('-denable_dl=0'), $options->asArguments());
  }

  #[@test]
  public function argumentsWithMultiSetting() {
    $options= new RuntimeOptions(); 
    $options->withSetting('extension', array('php_xsl.dll', 'php_sybase_ct.dll'));
    $this->assertEquals(
      array('-dextension=php_xsl.dll', '-dextension=php_sybase_ct.dll'), 
      $options->asArguments()
    );
  }

  #[@test]
  public function argumentsWithEmptyMultiSetting() {
    $options= new RuntimeOptions(); 
    $options->withSetting('extension', array());
    $this->assertEquals(array(), $options->asArguments());
  }

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

  #[@test]
  public function classPathDoesntAppearInArguments() {
    $options= new RuntimeOptions(); 
    $options->withClassPath('/opt/xp/lib/mysql-1.0.0.xar');
    $this->assertEquals(array(), $options->asArguments());
  }
}
