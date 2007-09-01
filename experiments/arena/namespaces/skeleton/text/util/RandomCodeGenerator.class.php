<?php
/* This class is part of the XP framework
 *
 * $Id: RandomCodeGenerator.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace text::util;

  uses('text.StringUtil');
  
  /**
   * Generates random codes that can be used for coupons etc.
   * The codes are not guaranteed to be unique although they usually
   * will:)
   *
   * @purpose  Generator
   */
  class RandomCodeGenerator extends lang::Object {
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
        text::StringUtil::delete($uniq, rand(0, strlen($uniq)));
      }
      
      return $uniq;
    }
  }
?>
