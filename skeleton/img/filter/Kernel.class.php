<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Kernel
   *
   * Can be constructed from a string in the following form:
   * <pre>
   *  [[ a, b, c ], [ d, e, f ], [ g, h, i ]]
   * </pre>
   *
   * @test     xp://net.xp_framework.unittest.img.KernelTest
   * @see      xp://img.filter.ConvolveFilter
   * @purpose  3x3 kernel
   */
  class Kernel extends Object {
    var
      $matrix= array();
    
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed arg either a string or float[3][3]
     * @throws  lang.IllegalArgumentException in case the given argument is invalid
     */
    function __construct($arg) {
      if (is_string($arg)) {
        if (!(preg_match_all('/\[[0-9, .-]+\]/', $arg, $matches, PREG_SET_ORDER))) {
          return throw(new IllegalArgumentException($arg));
        }
        foreach ($matches as $i => $match) {
          $row= array_map('floatval', explode(',', trim($match[0], '[]')));
          if (3 != sizeof($row)) {
            return throw(new IllegalArgumentException('Row #'.$i.' must be of size 3, given '.sizeof($row)));
          }
          $this->matrix[]= $row;
        }
      } else {
        foreach ((array)$arg as $i => $row) {
          if (3 != sizeof($row)) {
            return throw(new IllegalArgumentException('Row #'.$i.' must be of size 3, given '.sizeof($row)));
          }
          $this->matrix[]= array_map('floatval', $row);
        }
      }
      if (3 != sizeof($this->matrix)) {
        return throw(new IllegalArgumentException('Matrix must be of size 3, given '.sizeof($this->matrix)));
      }
    }
    
    /**
     * Retrieve matrix
     *
     * @access  public
     * @return  float[3][3]
     */
    function getMatrix() {
      return $this->matrix;
    }
  }
?>
