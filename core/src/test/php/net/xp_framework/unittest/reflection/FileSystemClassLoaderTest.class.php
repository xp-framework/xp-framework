<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase', 'lang.FileSystemClassLoader', 'io.File', 'io.FileUtil', 'io.Folder', 'lang.System');

  /**
   * TestCase for classloading
   */
  class FileSystemClassLoaderTest extends TestCase {
    protected static $temp;

    /**
     * Defines a type
     *
     * @param  string $type class type, either "interface" or "class"
     * @param  string $name fully qualified class name
     */
    protected static function define($type, $name) {
      if (FALSE === ($p= strrpos($name, '.'))) {
        $class= $name;
        $path= $name;
        $ns= '';
      } else {
        $class= substr($name, $p);
        $path= strtr($name, '.', DIRECTORY_SEPARATOR);
        $ns= 'namespace '.strtr(substr($name, 0, $p), '.', '\\').';';
      }

      FileUtil::setContents(
        new File(self::$temp, $path.xp::CLASS_FILE_EXT),
        sprintf('<?php %s %s %s extends \lang\Object { }', $ns, $type, $name)
      );
    }

    /**
     * Defines fixture classes in a temp dir
     */
    #[@beforeClass]
    public static function defineClasses() {
      self::$temp= new Folder(System::tempDir(), 'fsclt');
      self::$temp->create();

      self::define('class', 'FSCLT1');
    }

    /**
     * Removes temp dir created in `defineClasses()`.
     */
    #[@afterClass]
    public static function removeTempDir() {
      self::$temp->unlink();
    }

    #[@test]
    public function from_a_relative_path() {
      $cl= new FileSystemClassLoader(self::$temp->getURI());
      $this->assertEquals(
        $cl->loadClass('FSCLT1'),
        $cl->classFromUri('FSCLT1.class.php')
      );
    }

    #[@test]
    public function from_an_absolute_path() {
      $cl= new FileSystemClassLoader(self::$temp->getURI());
      $this->assertEquals(
        $cl->loadClass('FSCLT1'),
        $cl->classFromUri(self::$temp->getURI().'FSCLT1.class.php')
      );
    }

    #[@test]
    public function from_an_absolute_path_not_inside_cl_base() {
      $cl= new FileSystemClassLoader(self::$temp->getURI());
      $this->assertNull(
        $cl->classFromUri(DIRECTORY_SEPARATOR.'FSCLT1.class.php')
      );
    }
  }
?>
