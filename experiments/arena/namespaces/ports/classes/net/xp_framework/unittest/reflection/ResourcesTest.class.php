<?php
/* This class is part of the XP framework
 *
 * $Id: ResourcesTest.class.php 10296 2007-05-08 19:22:33Z friebe $ 
 */

  namespace net::xp_framework::unittest::reflection;

  ::uses('unittest.TestCase');

  /**
   * TestCase for resource loading
   *
   * @see      xp://lang.ClassLoader
   * @purpose  Unittest
   */
  class ResourcesTest extends unittest::TestCase {

    static function __static() {
      lang::ClassLoader::registerLoader(new lang::archive::ArchiveClassLoader(
        new lang::archive::ArchiveReader(dirname(__FILE__).'/lib/three-and-four.xar')
      ));
    }

    /**
     * Helper method for getResource() and getResourceAsStream()
     *
     * @param   string contents
     */
    protected function assertManifestFile($contents) {
      $this->assertEquals(
        "[runnable]\nmain-class=\"remote.server.impl.ApplicationServer\"",
        trim($contents)
      );
    }
    
    /**
     * Tests findResource() method
     *
     */
    #[@test]
    public function findResource() {
      $this->assertClass(
        lang::ClassLoader::getDefault()->findResource('META-INF/manifest.ini'),
        'lang.archive.ArchiveClassLoader'
      );
    }

    /**
     * Tests getResource() method
     *
     */
    #[@test]
    public function getResource() {
      $this->assertManifestFile(lang::ClassLoader::getDefault()->getResource('META-INF/manifest.ini'));
    }

    /**
     * Tests getResourceAsStream() method
     *
     */
    #[@test]
    public function getResourceAsStream() {
      $stream= lang::ClassLoader::getDefault()->getResourceAsStream('META-INF/manifest.ini');
      $this->assertSubClass($stream, 'io.Stream');
      $this->assertManifestFile($stream->read($stream->size()));
    }

    /**
     * Tests getResource() method
     *
     */
    #[@test, @expect('lang.ElementNotFoundException')]
    public function nonExistantResource() {
      lang::ClassLoader::getDefault()->getResource('::DOES-NOT-EXIST::');
    }
  }
?>
