<?php namespace net\xp_framework\unittest\img;

use img\filter\Kernel;

/**
 * Tests the kernel class
 *
 * @see  xp://img.filter.Kernel
 */
class KernelTest extends \unittest\TestCase {

  #[@test]
  public function create_from_array() {
    $matrix= array(
      array(-1.0, -1.0, -1.0), 
      array(-1.0, 16.0, -1.0), 
      array(-1.0, -1.0, -1.0)
    );

    $k= new Kernel($matrix);
    $this->assertEquals($matrix, $k->getMatrix());
  }

  #[@test]
  public function create_from_string() {
    $string= '[[-1.0, -1.0, -1.0], [-1.0, 16.0, -1.0], [-1.0, -1.0, -1.0]]';
    $matrix= array(
      array(-1.0, -1.0, -1.0), 
      array(-1.0, 16.0, -1.0), 
      array(-1.0, -1.0, -1.0)
    );

    $k= new Kernel($string);
    $this->assertEquals($matrix, $k->getMatrix());
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function create_from_empty_array() {
    new Kernel(array());
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function create_from_array_with_incorrect_row_size() {
    $matrix= array(
      array(-1.0, -1.0, -1.0), 
      array(-1.0, 16.0, -1.0, 6100), 
      array(-1.0, -1.0, -1.0)
    );
    new Kernel($matrix);
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function create_with_malformed_string() {
    new Kernel('@@SYNTAX-ERROR@@');
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function create_from_string_with_incorrect_row_size() {
    new Kernel('[[-1.0, -1.0, -1.0], [-1.0, -1.0], [-1.0, -1.0, -1.0]]');
  }
}
