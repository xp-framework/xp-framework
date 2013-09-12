<?php namespace net\xp_framework\unittest\reflection;

/**
 * Represents the base underlying the given class loader implementation
 *
 * @see   xp://net.xp_framework.unittest.reflection.ClassFromUriTest
 */
abstract class ClassFromUriBase extends \lang\Object {

  /**
   * Lifecycle: Initializes this base
   *
   * @param  var initializer A function to execute inside created base
   */
  public function initialize($initializer) {
    $this->create();
    $initializer($this);
  }

  /**
   * Lifecycle: Creates this base
   */
  public abstract function create();

  /**
   * Lifecycle: Deletes this base
   */
  public abstract function delete();

  /**
   * Returns the path for this URI base
   *
   * @return string
   */
  public abstract function path();

  /**
   * Creates a new file (in this underlying base)
   *
   * @param  string $name
   * @param  string $contents
   */
  public abstract function newFile($name, $contents);

  /**
   * Defines a type
   *
   * @param  string $type class type, either "interface" or "class"
   * @param  string $name fully qualified class name
   */
  public function newType($type, $name) {
    if (false === ($p= strrpos($name, '.'))) {
      $class= $name;
      $path= $name;
      $ns= '';
    } else {
      $class= substr($name, $p + 1);
      $path= strtr($name, '.', DIRECTORY_SEPARATOR);
      $ns= 'namespace '.strtr(substr($name, 0, $p), '.', '\\').';';
    }

    $this->newFile($path.\xp::CLASS_FILE_EXT, sprintf(
      '<?php %s %s %s extends \lang\Object { }',
      $ns,
      $type,
      $class
    ));
  }
}
