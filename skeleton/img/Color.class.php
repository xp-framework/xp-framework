<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  /**
   * Color class
   *
   * @test  xp://net.xp_framework.unittest.img.ColorTest
   * @see   xp://img.Image
   */
  class Color extends Object {
    public
      $red      = 0,
      $green    = 0,
      $blue     = 0;
      
    public
      $handle     = NULL;
    
    /**
     * Constructor
     *
     * @param   mixed a string containing the hexadecimal format or
     *          three ints (red, green blue)
     */
    public function __construct() {
      $a= func_get_args();
      if (is_string($a[0])) {
        sscanf(ltrim($a[0], '#'), '%2x%2x%2x', $this->red, $this->green, $this->blue);
      } else {
        list($this->red, $this->green, $this->blue)= $a;
      }
    }
    
    /**
     * Get RGB value as hexadecimal string (e.g. #990000)
     *
     * @return  string HTML-style color
     */
    public function toHex() {
      return '#'.dechex($this->red).dechex($this->green).dechex($this->blue);
    }
    
    /**
     * Returns a string representation of this color
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        '%s@(%03d, %03d, %03d)',
        $this->getClassName(),
        $this->red,
        $this->green,
        $this->blue
      );
    }
  }
?>
