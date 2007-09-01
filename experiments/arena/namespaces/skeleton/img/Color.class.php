<?php
/* This class is part of the XP framework
 *
 * $Id: Color.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace img;
 
  /**
   * Color class
   *
   * @see xp://img.Image
   */
  class Color extends lang::Object {
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
     * @param   string h a string in the format RRGGBB (may contain a leading "#")
     */
    public function fromHex($h) {
      if ('#' == $h{0}) $h= substr($h, 1);
      $this->red= hexdec(substr($h, 0, 2));
      $this->green= hexdec(substr($h, 2, 2));
      $this->blue= hexdec(substr($h, 4, 2));
    }
    
    /**
     * Get RGB value as hexidecimal string (e.g. #990000)
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
