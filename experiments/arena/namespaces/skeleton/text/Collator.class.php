<?php
/* This class is part of the XP framework
 *
 * $Id: Collator.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace text;

  uses('util.Locale', 'util.Comparator');

  /**
   * The Collator class performs locale-sensitive String comparison. 
   *
   * @purpose  Comparator
   */
  class Collator extends lang::Object implements util::Comparator {
    protected static 
      $instance     = array();

    public
      $locale = '';
     
    /**
     * Constructor
     *
     * @param   string locale
     */ 
    protected function __construct($locale) {
      $this->locale= $locale;
    }
    
    /**
     * Gets the Collator for the desired locale.
     *
     * @param   util.Locale locale
     * @return  text.Collator
     */
    public static function getInstance($locale) {
      $id= $locale->hashCode();
      if (!isset(self::$instance[$id])) {
        self::$instance[$id]= new self($locale->toString());
      }
      return self::$instance[$id];
    } 

    /**
     * Compares its two arguments for order. Returns a negative integer, 
     * zero, or a positive integer as the first argument is less than, 
     * equal to, or greater than the second.
     *
     * @param   string a
     * @param   string b
     * @return  int
     */
    public function compare($a, $b) {
      setlocale(LC_COLLATE, $this->locale);
      return strcoll($a, $b);
    }
  
  } 
?>
