<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'xp.compiler.io.FileManager',
    'xp.compiler.types.TypeReference'
  );

  /**
   * TestCase
   *
   * @see      xp://xp.compiler.io.FileManager
   */
  class FileManagerTest extends TestCase {
    protected $fixture= NULL;
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->fixture= new FileManager();
    }
    
    /**
     * Creates a new type reference
     *
     * @param   string name qualified name
     * @return  xp.compiler.types.Types
     */
    protected function newType($name) {
      return new TypeReference(new TypeName($name));
    }
    
    /**
     * Assertion helper
     *
     * @param   string expected
     * @return  io.File target
     * @throws  unittest.AssertionFailedError
     */
    protected function assertTarget($expected, File $target) {
      $this->assertEquals(
        create(new File(strtr($expected, '/', DIRECTORY_SEPARATOR)))->getURI(),
        str_replace(rtrim(realpath('.'), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR, '', $target->getURI())
      );
    }
    
    /**
     * Test getTarget() method
     *
     */
    #[@test]
    public function target() {
      $this->assertTarget(
        'de/thekid/demo/Value'.xp::CLASS_FILE_EXT,
        $this->fixture->getTarget($this->newType('de.thekid.demo.Value'))
      );
    }

    /**
     * Test getTarget() method
     *
     */
    #[@test]
    public function targetWithOutputFolder() {
      $this->assertTarget(
        'build/de/thekid/demo/Value'.xp::CLASS_FILE_EXT,
        $this->fixture->withOutput(new Folder('build'))->getTarget($this->newType('de.thekid.demo.Value'))
      );
    }

    /**
     * Test getTarget() method
     *
     */
    #[@test]
    public function targetWithSource() {
      $source= new FileSource(new File('src/de/thekid/demo/Value.xp'));
      $this->assertTarget(
        'src/de/thekid/demo/Value'.xp::CLASS_FILE_EXT,
        $this->fixture->getTarget($this->newType('de.thekid.demo.Value'), $source)
      );
    }

    /**
     * Test getTarget() method
     *
     */
    #[@test]
    public function targetWithSourceWithoutPackage() {
      $source= new FileSource(new File('src/Value.xp'));
      $this->assertTarget(
        'src/Value'.xp::CLASS_FILE_EXT,
        $this->fixture->getTarget($this->newType('de.thekid.demo.Value'), $source)
      );
    }

    /**
     * Test getTarget() method
     *
     */
    #[@test]
    public function targetWithSourceMismatchingPackage() {
      $source= new FileSource(new File('src/com/thekid/demo/Value.xp'));
      $this->assertTarget(
        'src/com/thekid/demo/Value'.xp::CLASS_FILE_EXT,
        $this->fixture->getTarget($this->newType('de.thekid.demo.Value'), $source)
      );
    }
  }
?>
