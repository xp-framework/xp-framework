<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  /**
   * Color class
   *
   * @see xp://img.Image
   */
  class Color extends Object {
    var
      $red      = 0,
      $green    = 0,
      $blue     = 0;
      
    var
      $handle     = NULL;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed a string containing the hexadecimal format or
     *          three ints (red, green blue)
     */
    function __construct() {
      $a= func_get_args();
      if (is_string($a[0])) {
        $this->fromHex($a[0]);
      } else {
        list(
          $this->red,
          $this->green,
          $this->blue
        )= $a;
      }
    }
    
    /**
     * Set RGB values from hexadecimal string
     *
     * @access  public
     * @param   string h a string in the format RRGGBB (may contain a leading "#")
     */
    function fromHex($h) {
      if ('#' == $h{0}) $h= substr($h, 1);
      $this->red= hexdec(substr($h, 0, 2));
      $this->green= hexdec(substr($h, 2, 2));
      $this->blue= hexdec(substr($h, 4, 2));
    }
    
    /**
     * Get RGB value as hexidecimal string (e.g. #990000)
     *
     * @access  public
     * @return  string HTML-style color
     */
    function toHex() {
      return '#'.dechex($this->red).dechex($this->green).dechex($this->blue);
    }
    
    /**
     * Returns a string representation of this color
     *
     * @access  public
     * @return  string
     */
    function toString() {
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
