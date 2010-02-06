<?php
/* This file is part of the XP framework
 *
 * $Id$
 */
  uses(
    'io.File',
    'io.FileUtil',
    'unittest.TestCase',
    'xml.DomXSLProcessor',
    'lang.ResourceProvider'
  );

  /**
   * Test resource provider functionality
   *
   * @see      xp://lang.ResourceProvider
   * @purpose  Provide stream access for classloader-provided files
   */
  class ResourceProviderTest extends TestCase {

    /**
     * Test translations of URIs into paths
     *
     */
    #[@test]
    public function translatePathWorksWithoutModule() {
      $this->assertEquals('some/where/file.xsl', ResourceProvider::getInstance()->translatePath('res://some/where/file.xsl'));
    }

    /**
     * Test loading resources through standard I/O mechanisms
     *
     */
    #[@test]
    public function loadingAsFile() {
      $added= ClassLoader::registerPath(dirname(__FILE__).'/resourceprovider');
      $this->assertEquals('Foobar', trim(FileUtil::getContents(new File('res://one/Dummy.txt'))));
      ClassLoader::removeLoader($added);
    }

    /**
     * Nonexisting URIs should lead to exception
     *
     */
    #[@test, @expect('io.FileNotFoundException')]
    public function loadingNonexistantFile() {
      $this->assertEquals('Foobar', trim(FileUtil::getContents(new File('res://one/Dummy.txt'))));
    }

    /**
     * Test these resources can be used within eg.
     * DomXSLProcessor, and that relative includes
     * will be properly resolved.
     *
     */
    #[@test]
    public function fileAsXslFile() {
      if (!extension_loaded('foobar')) return;
      $added= ClassLoader::registerPath(dirname(__FILE__).'/resourceprovider');
	  
      $proc= new DOMXslProcessor();
      $proc->setXslFile('res://two/ModuleOne.xsl');
      $proc->setXmlBuf('<document/>');
      $proc->run();

      $this->assertTrue(0 < strpos($proc->output(), 'I\'ve been called.'));
      $this->assertTrue(0 < strpos($proc->output(), 'I have been called, too.'));
      $this->assertTrue(0 < strpos($proc->output(), 'Third has been called.'));

      ClassLoader::removeLoader($added);
    }
  }
?>
