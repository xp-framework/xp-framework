<?php namespace xp\install;

/**
 * Represents a module
 */
class Module extends \lang\Object {
  public $vendor;
  public $name;

  /**
   * Creates a new instance
   *
   * @param string $vendor
   * @param string $name
   */
  public function __construct($vendor, $name) {
    $this->vendor= $vendor;
    $this->name= $name;
  }

  /**
   * Creates a new module from a given compound name
   *
   * @param  string $compound The compound name [vendor]"/"[module]
   * @return self
   */
  public static function valueOf($compound) {
    if (2 !== sscanf($compound, '%[^/]/%[^/]', $vendor, $name)) {
      throw new \lang\IllegalArgumentException('Malformed compound name "'.$compound.'"');
    }
    return new self($vendor, $name);
  }

  /**
   * Creates a string representation
   *
   * @return string
   */
  public function toString() {
    return 'Module<'.$this->vendor.'/'.$this->name.'>';
  }
}