<?php
/* This class is part of the XP framework
 *
 * $Id: QuotingStrategy.class.php 11510 2009-09-15 15:55:41Z friebe $ 
 */

  /**
   * Quoting strategy
   *
   */
  interface QuotingStrategy {
    
    /**
     * Tests whether quoting is necessary
     *
     * @param   string value
     * @param   string delimiter
     * @param   string quote
     * @return  bool
     */
    public function necessary($value, $delimiter, $quote);
    
  }
?>
