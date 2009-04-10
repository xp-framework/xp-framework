<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('text.StringUtil');
  
  /**
   * Generates random codes that can be used for coupons etc.
   * The codes are not guaranteed to be unique although they usually
   * will:)
   *
   * @purpose  Generator
   */
  class RandomCodeGenerator extends Object {
    public
      $length   = 0;
      
    /**
     * Constructor
     *
     * @param   int length default 16
     */
    public function __construct($length= 16) {
      $this->length= $length;
      
    }
    
    /**
     * Generate
     *
     * @return  string
     */
    public function generate() {
      $uniq= str_shuffle(strtr(uniqid(microtime(), TRUE), ' .', 'gh'));
      while (strlen($uniq) > $this->length) {
        $uniq= StringUtil::delete($uniq, rand(0, strlen($uniq)));
      }
      
      return $uniq;
    }
  }
?>
