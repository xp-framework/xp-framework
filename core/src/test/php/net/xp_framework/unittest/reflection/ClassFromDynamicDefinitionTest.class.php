<?php namespace net\xp_framework\unittest\reflection;

use lang\DynamicClassLoader;

/**
 * TestCase for classloading
 *
 * @see  xp://lang.DynamicClassLoader#loadUri
 */
class ClassFromDynamicDefinitionTest extends ClassFromUriTest {

  /**
   * Creates fixture
   *
   * @return   lang.IClassLoader
   */
  protected function newFixture() {
    return DynamicClassLoader::instanceFor('test');
  }

  /**
   * Creates underlying base for class loader, e.g. a directory or a .XAR file
   *
   * @return  net.xp_framework.unittest.reflection.ClassFromUriBase
   */
  protected static function baseImpl() {
    return newinstance('net.xp_framework.unittest.reflection.ClassFromUriBase', array(), '{
      protected $t= NULL;

      public function create() {
        // NOOP
      }

      public function delete() {
        // NOOP
      }

      public function newType($type, $name) {
        if (FALSE === ($p= strrpos($name, "."))) {
          $class= $name;
          $ns= "";
        } else {
          $class= substr($name, $p + 1);
          $ns= "namespace ".strtr(substr($name, 0, $p), ".", "\\\\").";";
        }

        \lang\DynamicClassLoader::instanceFor("test")->setClassBytes($name, sprintf(
          "%s %s %s extends \lang\Object { }",
          $ns,
          $type,
          $class
        ));
      }

      public function newFile($name, $contents) {
        // Not supported
      }

      public function path() {
        return "dyn://";
      }
    }');
  }

  #[@test]
  public function provides_a_relative_path_in_root() {
    $this->assertTrue($this->fixture->providesUri('dyn://CLT1'));
  }

  #[@test]
  public function load_from_a_relative_path_in_root() {
    $this->assertEquals(
      $this->fixture->loadClass('CLT1'),
      $this->fixture->loadUri('dyn://CLT1')
    );
  }

  #[@test]
  public function from_a_relative_path() {
    $this->assertEquals(
      $this->fixture->loadClass('net.xp_framework.unittest.reflection.CLT2'),
      $this->fixture->loadUri('dyn://net.xp_framework.unittest.reflection.CLT2')
    );
  }

  #[@test, @ignore('Not applicablle for DynamicClassLoader\'s URIs')]
  public function from_a_relative_path_with_dot() {
  }

  #[@test, @ignore('Not applicablle for DynamicClassLoader\'s URIs')]
  public function from_a_relative_path_with_dot_dot() {
  }

  #[@test, @ignore('Not applicablle for DynamicClassLoader\'s URIs')]
  public function from_a_relative_path_with_multiple_directory_separators() {
  }

  #[@test, @ignore('Not applicablle for DynamicClassLoader\'s URIs')]
  public function from_an_absolute_path_in_root() {
  }

  #[@test, @ignore('Not applicablle for DynamicClassLoader\'s URIs')]
  public function from_an_absolute_path() {
  }
}
