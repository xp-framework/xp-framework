<?php
/* This file is part of the XP framework
 *
 * $Id$
 */

  uses(
    'io.File',
    'io.FileUtil',
    'unittest.TestCase',
    'lang.ResourceProvider'
  );

  /**
   * Test resource provider functionality
   *
   * @see  xp://lang.ResourceProvider
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
      $this->assertEquals('Foobar', trim(FileUtil::getContents(new File('res://net/xp_framework/unittest/core/resourceprovider/one/Dummy.txt'))));
    }

    /**
     * Nonexisting URIs should lead to exception
     *
     */
    #[@test, @expect('io.FileNotFoundException')]
    public function loadingNonexistantFile() {
      $this->assertEquals('Foobar', trim(FileUtil::getContents(new File('res://one/Dummy.txt'))));
    }
  }
?>
