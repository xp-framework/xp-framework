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
    protected static $base;
    protected $fixture;

    /**
     * Creates a new file (in the temporary directory)
     *
     * @param  string $name
     * @param  string $contents
     */
    protected static function newFile($name, $contents) {
      $file= new File(self::$base, $name);
      $path= new Folder($file->getPath());
      $path->exists() || $path->create();

      FileUtil::setContents($file, $contents);
    }

    /**
     * Defines a type
     *
     * @param  string $type class type, either "interface" or "class"
     * @param  string $name fully qualified class name
     */
    protected static function newType($type, $name) {
      if (FALSE === ($p= strrpos($name, '.'))) {
        $class= $name;
        $path= $name;
        $ns= '';
      } else {
        $class= substr($name, $p + 1);
        $path= strtr($name, '.', DIRECTORY_SEPARATOR);
        $ns= 'namespace '.strtr(substr($name, 0, $p), '.', '\\').';';
      }

      self::newFile($path.xp::CLASS_FILE_EXT, sprintf(
        '<?php %s %s %s extends \lang\Object { }',
        $ns,
        $type,
        $class
      ));
    }

    /**
     * Defines fixture classes in a temp dir
     */
    #[@beforeClass]
    public static function defineClasses() {
      self::$base= new Folder(System::tempDir(), 'fsclt');
      self::$base->create();

      self::newType('class', 'FSCLT1');
      self::newType('class', 'net.xp_framework.unittest.reflection.FSCLT2');
      self::newFile('FSCLT1.txt', 'This is not a class');
    }

    /**
     * Creates fixture.
     */
    public function setUp() {
      $this->fixture= new FileSystemClassLoader(self::$base->getURI());
    }

    /**
     * Removes temp dir created in `defineClasses()`.
     */
    #[@afterClass]
    public static function removeTempDir() {
      self::$base->unlink();
    }

    /**
     * Compose a path from a list of elements
     *
     * @param  string... args
     * @return string
     */
    protected function compose() {
      return implode(DIRECTORY_SEPARATOR, array_map(
        function($e) { return rtrim($e, DIRECTORY_SEPARATOR); },
        func_get_args()
      ));
    }

    #[@test]
    public function from_a_relative_path_in_root() {
      $this->assertEquals(
        $this->fixture->loadClass('FSCLT1'),
        $this->fixture->classFromUri('FSCLT1.class.php')
      );
    }

    #[@test]
    public function from_a_relative_path() {
      $this->assertEquals(
        $this->fixture->loadClass('net.xp_framework.unittest.reflection.FSCLT2'),
        $this->fixture->classFromUri($this->compose('net', 'xp_framework', 'unittest', 'reflection', 'FSCLT2.class.php'))
      );
    }

    #[@test]
    public function from_an_absolute_path_in_root() {
      $this->assertEquals(
        $this->fixture->loadClass('FSCLT1'),
        $this->fixture->classFromUri($this->compose(self::$base->getURI(), 'FSCLT1.class.php'))
      );
    }

    #[@test]
    public function from_an_absolute_path() {
      $this->assertEquals(
        $this->fixture->loadClass('net.xp_framework.unittest.reflection.FSCLT2'),
        $this->fixture->classFromUri($this->compose(self::$base->getURI(), 'net', 'xp_framework', 'unittest', 'reflection', 'FSCLT2.class.php'))
      );
    }

    #[@test]
    public function from_an_absolute_path_not_inside_cl_base() {
      $this->assertNull($this->fixture->classFromUri($this->compose(NULL, 'FSCLT1.class.php')));
    }

    #[@test]
    public function from_non_class_file() {
      $this->assertNull($this->fixture->classFromUri('FSCLT1.txt'));
    }
  }
?>
