<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Comparator');

  /**
   * The Collator class performs locale-sensitive String comparison. 
   *
   * @see      reference
   * @purpose  purpose
   */
  class Collator extends Object implements Comparator {
    public static $instance= NULL;
    public
      $locale = '';
     
    /**
     * Constructor
     *
     * @access  protected
     * @param   string locale
     */ 
    protected function __construct($locale) {
      
      $this->locale= $locale;
    }
    
    /**
     * Gets the Collator for the desired locale.
     *
     * @access  public
     * @param   &util.Locale locale
     * @return  &text.Collator
     */
    public function getInstance(Locale $locale) {
      $id= $locale->hashCode();
      if (!isset(self::$instance[$id])) {
        self::$instance[$id]= new Collator($locale->toString());
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
    public function compare($a, $b) {
      setlocale(LC_COLLATE, $this->locale);
      return strcoll($a, $b);
    }
  
  }
?>
