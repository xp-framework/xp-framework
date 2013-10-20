<?php namespace net\xp_framework\unittest\core;

use io\File;
use io\FileUtil;
use unittest\TestCase;
use xml\DomXSLProcessor;
use lang\ResourceProvider;

/**
 * Test resource provider functionality
 *
 * @see   xp://lang.ResourceProvider
 */
class ResourceProviderXslIntegrationTest extends TestCase {

  /**
   * Skips tests if XSL Extension is not loaded
   */
  #[@beforeClass]
  public static function verifyXSLExtensionLoaded() {
    if (!extension_loaded('xsl')) {
      throw new \unittest\PrerequisitesNotMetError('XSL Extension not loaded', NULL, array('loaded'));
    }
  }

  /**
   * Test these resources can be used within eg.
   * DomXSLProcessor, and that relative includes
   * will be properly resolved.
   *
   */
  #[@test]
  public function fileAsXslFile() {
    $proc= new DomXSLProcessor();
    $style= new \DOMDocument();
    $style->load('res://net/xp_framework/unittest/core/resourceprovider/two/ModuleOne.xsl');
    
    $proc->setXSLDoc($style);
    $proc->setXmlBuf('<document/>');
    $proc->run();

    $this->assertTrue(0 < strpos($proc->output(), 'I\'ve been called.'));
    $this->assertTrue(0 < strpos($proc->output(), 'I have been called, too.'));
  }

  /**
   * Test that relative inclusion of xsl files within an
   * xsl file that was provided by ResourceProvider does
   * not work.
   *
   * This is not wanted behaviour, actually - but we'd like
   * to check for this explicitely, so any change in this 
   * faulty behavior will be automatically detected some 
   * time in the future.
   */
  #[@test]
  public function fileAsXslFileWithRelativeIncludeDoesNotWork() {
    $t= NULL;
    $proc= new DomXSLProcessor();
    $style= new \DOMDocument();
    $style->load('res://net/xp_framework/unittest/core/resourceprovider/two/IncludingStylesheet.xsl');

    $proc->setXSLDoc($style);
    $proc->setXmlBuf('<document/>');
    $proc->run();

    $this->assertTrue(FALSE !== strpos($proc->output(), 'Include has been called.'));
  }
}
