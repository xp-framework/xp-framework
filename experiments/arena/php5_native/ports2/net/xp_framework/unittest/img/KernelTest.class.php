<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase', 'img.filter.Kernel');

  /**
   * Tests the kernel class
   *
   * @see      xp://img.filter.Kernel
   * @purpose  Unit Test
   */
  class KernelTest extends TestCase {
  
    /**
     * Tests creating a kernel from an array
     *
     * @access  public
     */
    #[@test]
    public function fromArray() {
      $matrix= array(
        array(-1.0, -1.0, -1.0), 
        array(-1.0, 16.0, -1.0), 
        array(-1.0, -1.0, -1.0)
      );

      $k= new Kernel($matrix);
      $this->assertEquals($matrix, $k->getMatrix());
    }

    /**
     * Tests creating a kernel from a string
     *
     * @access  public
     */
    #[@test]
    public function fromString() {
      $string= '[[-1.0, -1.0, -1.0], [-1.0, 16.0, -1.0], [-1.0, -1.0, -1.0]]';
      $matrix= array(
        array(-1.0, -1.0, -1.0), 
        array(-1.0, 16.0, -1.0), 
        array(-1.0, -1.0, -1.0)
      );

      $k= new Kernel($string);
      $this->assertEquals($matrix, $k->getMatrix());
    }

    /**
     * Tests creating a kernel from an array of size 0
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function wrongArraySize() {
      new Kernel(array());
    }

    /**
     * Tests creating a kernel from an array where one row has more
     * than three values.
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function wrongRowSizeInArray() {
      $matrix= array(
        array(-1.0, -1.0, -1.0), 
        array(-1.0, 16.0, -1.0, 6100), 
        array(-1.0, -1.0, -1.0)
      );
      new Kernel($matrix);
    }

    /**
     * Tests creating a kernel from a string with illegal syntax
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function illegalStringSyntax() {
      new Kernel('@@SYNTAX-ERROR@@');
    }

    /**
     * Tests creating a kernel from a string with where one row has 
     * less than three values.
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function wrongRowSizeInString() {
      new Kernel('[[-1.0, -1.0, -1.0], [-1.0, -1.0], [-1.0, -1.0, -1.0]]');
    }
  }
?>
