<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase', 'net.xp_framework.unittest.reflection.ClassFromUriBase');

  /**
   * TestCase for classloading
   */
  abstract class ClassFromUriTest extends TestCase {
    protected static $base;
    protected $fixture;

    /**
     * Creates fixture
     *
     * @return   lang.IClassLoader
     */
    protected abstract function newFixture();

    /**
     * Creates underlying base for class loader, e.g. a directory or a .XAR file
     *
     * @return  net.xp_framework.unittest.reflection.ClassFromUriBase
     */
    protected static function baseImpl() {
      raise('lang.MethodNotImplementedException', 'Implement in subclass!', __FUNCTION__);
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

      self::$base->newFile($path.xp::CLASS_FILE_EXT, sprintf(
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
      self::$base= static::baseImpl();
      self::$base->create();

      self::newType('class', 'CLT1');
      self::newType('class', 'net.xp_framework.unittest.reflection.CLT2');
      self::$base->newFile('CLT1.txt', 'This is not a class');
    }

    /**
     * Creates fixture.
     */
    public function setUp() {
      $this->fixture= $this->newFixture();
    }

    /**
     * Removes base
     */
    #[@afterClass]
    public static function cleanUp() {
      self::$base->delete();
    }

    /**
     * Compose a path from a list of elements
     *
     * @param  var... args either strings or a ClassFromUriBase instance
     * @return string
     */
    protected function compose() {
      $base= self::$base;
      return implode(DIRECTORY_SEPARATOR, array_map(
        function($e) use($base) {
          return $base->equals($e) ? $base->path() : rtrim($e, DIRECTORY_SEPARATOR);
        },
        func_get_args()
      ));
    }

    #[@test]
    public function from_a_relative_path_in_root() {
      $this->assertEquals(
        $this->fixture->loadClass('CLT1'),
        $this->fixture->classFromUri('CLT1.class.php')
      );
    }

    #[@test]
    public function from_a_relative_path() {
      $this->assertEquals(
        $this->fixture->loadClass('net.xp_framework.unittest.reflection.CLT2'),
        $this->fixture->classFromUri($this->compose('net', 'xp_framework', 'unittest', 'reflection', 'CLT2.class.php'))
      );
    }

    #[@test]
    public function from_a_relative_path_with_dot() {
      $this->assertEquals(
        $this->fixture->loadClass('CLT1'),
        $this->fixture->classFromUri($this->compose('.', 'CLT1.class.php'))
      );
    }

    #[@test]
    public function from_a_relative_path_with_dot_dot() {
      $this->assertEquals(
        $this->fixture->loadClass('CLT1'),
        $this->fixture->classFromUri($this->compose('net', 'xp_framework', '..', '..', 'CLT1.class.php'))
      );
    }

    #[@test]
    public function from_a_relative_path_with_multiple_directory_separators() {
      $this->assertEquals(
        $this->fixture->loadClass('CLT1'),
        $this->fixture->classFromUri($this->compose('.', NULL, 'CLT1.class.php'))
      );
    }

    #[@test]
    public function from_an_absolute_path_in_root() {
      $this->assertEquals(
        $this->fixture->loadClass('CLT1'),
        $this->fixture->classFromUri($this->compose(self::$base, 'CLT1.class.php'))
      );
    }

    #[@test]
    public function from_an_absolute_path() {
      $this->assertEquals(
        $this->fixture->loadClass('net.xp_framework.unittest.reflection.CLT2'),
        $this->fixture->classFromUri($this->compose(self::$base, 'net', 'xp_framework', 'unittest', 'reflection', 'CLT2.class.php'))
      );
    }

    #[@test]
    public function from_an_absolute_path_not_inside_cl_base() {
      $this->assertNull($this->fixture->classFromUri($this->compose(NULL, 'CLT1.class.php')));
    }

    #[@test]
    public function from_non_class_file() {
      $this->assertNull($this->fixture->classFromUri('CLT1.txt'));
    }

    #[@test]
    public function from_directory() {
      $this->assertNull($this->fixture->classFromUri($this->compose(self::$base, 'net', 'xp_framework')));
    }

    #[@test]
    public function from_non_existant_file() {
      $this->assertNull($this->fixture->classFromUri($this->compose(self::$base, 'NonExistant.File')));
    }
  }
?>
