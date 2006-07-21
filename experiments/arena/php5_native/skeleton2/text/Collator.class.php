<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Locale');

  /**
   * The Collator class performs locale-sensitive String comparison. 
   *
   * @purpose  Comparator
   */
  class Collator extends Object implements Comparator {
    public
      $locale = '';
     
    /**
     * Constructor
     *
     * @access  protected
     * @param   string locale
     */ 
    public function __construct($locale) {
      $this->locale= $locale;
    }
    
    /**
     * Gets the Collator for the desired locale.
     *
     * @access  public
     * @param   &util.Locale locale
     * @return  &text.Collator
     */
    public function &getInstance(&$locale) {
      static $instance= array();
      
      $id= $locale->hashCode();
      if (!isset($instance[$id])) {
        $instance[$id]= new Collator($locale->toString());
      }
      return $instance[$id];
    } 

    /**
     * Compares its two arguments for order. Returns a negative integer, 
     * zero, or a positive integer as the first argument is less than, 
     * equal to, or greater than the second.
     *
     * @access  public
     * @param   &string a
     * @param   &string b
     * @return  int
     */
    public function compare(&$a, &$b) {
      setlocale(LC_COLLATE, $this->locale);
      return strcoll($a, $b);
    }
  
  } 
?>
