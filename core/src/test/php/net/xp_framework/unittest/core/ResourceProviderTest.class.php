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
      $added= ClassLoader::registerPath(dirname(__FILE__).'/resourceprovider');

      $proc= new DomXSLProcessor();
      $style= new DOMDocument();
      $style->load('res://two/ModuleOne.xsl');
      
      $proc->setXSLDoc($style);
      $proc->setXmlBuf('<document/>');
      $proc->run();

      $this->assertTrue(0 < strpos($proc->output(), 'I\'ve been called.'));
      $this->assertTrue(0 < strpos($proc->output(), 'I have been called, too.'));

      ClassLoader::removeLoader($added);
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
    #[@test, @expect('xml.TransformerException')]
    public function fileAsXslFileWithRelativeIncludeDoesNotWork() {
      $added= ClassLoader::registerPath(dirname(__FILE__).'/resourceprovider');

      $t= NULL;
      try {
        $proc= new DomXSLProcessor();
        $style= new DOMDocument();
        $style->load('res://two/IncludingStylesheet.xsl');

        $proc->setXSLDoc($style);
        $proc->setXmlBuf('<document/>');
        $proc->run();
      } catch (Throwable $t) {
      } finally(); {
        ClassLoader::removeLoader($added);
      
        xp::gc();
        if ($t) throw $t;
      }
    }
  }
?>
