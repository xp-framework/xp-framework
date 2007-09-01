<?php
/* This class is part of the XP framework
 *
 * $Id: KernelTest.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace net::xp_framework::unittest::img;

  ::uses('unittest.TestCase', 'img.filter.Kernel');

  /**
   * Tests the kernel class
   *
   * @see      xp://img.filter.Kernel
   * @purpose  Unit Test
   */
  class KernelTest extends unittest::TestCase {
  
    /**
     * Tests creating a kernel from an array
     *
     */
    #[@test]
    public function fromArray() {
      $matrix= array(
        array(-1.0, -1.0, -1.0), 
        array(-1.0, 16.0, -1.0), 
        array(-1.0, -1.0, -1.0)
      );

      $k= new img::filter::Kernel($matrix);
      $this->assertEquals($matrix, $k->getMatrix());
    }

    /**
     * Tests creating a kernel from a string
     *
     */
    #[@test]
    public function fromString() {
      $string= '[[-1.0, -1.0, -1.0], [-1.0, 16.0, -1.0], [-1.0, -1.0, -1.0]]';
      $matrix= array(
        array(-1.0, -1.0, -1.0), 
        array(-1.0, 16.0, -1.0), 
        array(-1.0, -1.0, -1.0)
      );

      $k= new img::filter::Kernel($string);
      $this->assertEquals($matrix, $k->getMatrix());
    }

    /**
     * Tests creating a kernel from an array of size 0
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function wrongArraySize() {
      new img::filter::Kernel(array());
    }

    /**
     * Tests creating a kernel from an array where one row has more
     * than three values.
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function wrongRowSizeInArray() {
      $matrix= array(
        array(-1.0, -1.0, -1.0), 
        array(-1.0, 16.0, -1.0, 6100), 
        array(-1.0, -1.0, -1.0)
      );
      new img::filter::Kernel($matrix);
    }

    /**
     * Tests creating a kernel from a string with illegal syntax
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function illegalStringSyntax() {
      new img::filter::Kernel('@@SYNTAX-ERROR@@');
    }

    /**
     * Tests creating a kernel from a string with where one row has 
     * less than three values.
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function wrongRowSizeInString() {
      new img::filter::Kernel('[[-1.0, -1.0, -1.0], [-1.0, -1.0], [-1.0, -1.0, -1.0]]');
    }
  }
?>
