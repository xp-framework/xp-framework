<?php namespace net\xp_framework\unittest\reflection;

use unittest\TestCase;


/**
 * TestCase for resolving classes from URIs using the `loadUri()` method.
 *
 * @see  xp://net.xp_framework.unittest.reflection.ClassFromFileSystemTest
 */
abstract class ClassFromUriTest extends TestCase {
  protected static $base;
  protected $fixture;

  /**
   * Creates underlying base for class loader, e.g. a directory or a .XAR file
   *
   * @return  net.xp_framework.unittest.reflection.ClassFromUriBase
   */
  protected static function baseImpl() {
    raise('lang.MethodNotImplementedException', 'Implement in subclass!', __FUNCTION__);
  }

  /**
   * Creates base and defines fixture classes
   */
  #[@beforeClass]
  public static function createBase() {
    self::$base= static::baseImpl();
    self::$base->initialize(function($self) {
      $self->newType('class', 'CLT1');
      $self->newType('class', 'net.xp_framework.unittest.reflection.CLT2');
      $self->newFile('CLT1.txt', 'This is not a class');
    });
  }

  /**
   * Removes base
   */
  #[@afterClass]
  public static function cleanUp() {
    self::$base->delete();
  }

  /**
   * Creates fixture.
   *
   * @return   lang.IClassLoader
   */
  protected abstract function newFixture();

  /**
   * Initializes fixture member with the results from `newFixture()`.
   */
  public function setUp() {
    $this->fixture= $this->newFixture();
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
  public function provides_a_relative_path_in_root() {
    $this->assertTrue($this->fixture->providesUri('CLT1.class.php'));
  }

  #[@test]
  public function load_from_a_relative_path_in_root() {
    $this->assertEquals(
      $this->fixture->loadClass('CLT1'),
      $this->fixture->loadUri('CLT1.class.php')
    );
  }

  #[@test]
  public function from_a_relative_path() {
    $this->assertEquals(
      $this->fixture->loadClass('net.xp_framework.unittest.reflection.CLT2'),
      $this->fixture->loadUri($this->compose('net', 'xp_framework', 'unittest', 'reflection', 'CLT2.class.php'))
    );
  }

  #[@test]
  public function from_a_relative_path_with_dot() {
    $this->assertEquals(
      $this->fixture->loadClass('CLT1'),
      $this->fixture->loadUri($this->compose('.', 'CLT1.class.php'))
    );
  }

  #[@test]
  public function from_a_relative_path_with_dot_dot() {
    $this->assertEquals(
      $this->fixture->loadClass('CLT1'),
      $this->fixture->loadUri($this->compose('net', 'xp_framework', '..', '..', 'CLT1.class.php'))
    );
  }

  #[@test]
  public function from_a_relative_path_with_multiple_directory_separators() {
    $this->assertEquals(
      $this->fixture->loadClass('CLT1'),
      $this->fixture->loadUri($this->compose('.', null, 'CLT1.class.php'))
    );
  }

  #[@test]
  public function from_an_absolute_path_in_root() {
    $this->assertEquals(
      $this->fixture->loadClass('CLT1'),
      $this->fixture->loadUri($this->compose(self::$base, 'CLT1.class.php'))
    );
  }

  #[@test]
  public function from_an_absolute_path() {
    $this->assertEquals(
      $this->fixture->loadClass('net.xp_framework.unittest.reflection.CLT2'),
      $this->fixture->loadUri($this->compose(self::$base, 'net', 'xp_framework', 'unittest', 'reflection', 'CLT2.class.php'))
    );
  }

  #[@test, @expect('lang.ClassNotFoundException')]
  public function from_an_absolute_path_not_inside_cl_base() {
    $this->fixture->loadUri($this->compose(null, 'CLT1.class.php'));
  }

  #[@test, @expect('lang.ClassNotFoundException')]
  public function from_non_class_file() {
    $this->fixture->loadUri('CLT1.txt');
  }

  #[@test, @expect('lang.ClassNotFoundException')]
  public function from_directory() {
    $this->fixture->loadUri($this->compose(self::$base, 'net', 'xp_framework'));
  }

  #[@test, @expect('lang.ClassNotFoundException')]
  public function from_non_existant_file() {
    $this->fixture->loadUri($this->compose(self::$base, 'NonExistant.File'));
  }
}
