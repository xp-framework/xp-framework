<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'util.PropertySourceFactory'
  );

  /**
   * Test property source factory clas
   *
   * @see      xp://util.PropertySourceFactory
   * @purpose  Unit test
   */
  class PropertySourceFactoryTest extends TestCase {
  
    /**
     * Test for simple file system path
     *
     */
    #[@test]
    public function filesystemPath() {
      $this->assertClass(PropertySourceFactory::forUri('/some/path'), 'util.FilesystemPropertySource');
    }
    
    /**
     * Test for XAR reference
     *  
     */
    #[@test]
    public function pathToXar() {
      $this->assertClass(PropertySourceFactory::forUri('/some/archive.xar'), 'util.ArchivePropertySource');
    }
  }
?>
