<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Chained Exception
   *
   * @purpose   Exception base class
   * @test      xp://net.xp_framework.unittest.core.ChainedExceptionTest
   * @see       http://mindprod.com/jgloss/chainedexceptions.html
   * @see       http://www.jguru.com/faq/view.jsp?EID=1026405  
   */
  class ChainedException extends XPException {
    public
      $cause    = NULL;

    /**
     * Constructor
     *
     * @param   string message
     * @param   lang.Throwable cause
     */
    public function __construct($message, $cause) {
      parent::__construct($message);
      $this->cause= $cause;
    }

    /**
     * Set cause
     *
     * @param   lang.Throwable cause
     */
    public function setCause($cause) {
      $this->cause= $cause;
    }

    /**
     * Get cause
     *
     * @return  lang.Throwable
     */
    public function getCause() {
      return $this->cause;
    }
    
    /**
     * Return string representation of this exception
     *
     * @return  string
     */
    public function toString() {
      $s= $this->compoundMessage()."\n";
      $tt= $this->getStackTrace();
      $t= sizeof($tt);
      for ($i= 0; $i < $t; $i++) {
        $s.= $tt[$i]->toString(); 
      }
      if (!$this->cause) return $s;
      
      $loop= $this->cause;
      while ($loop) {

        // String of cause
        $s.= 'Caused by '.$loop->compoundMessage()."\n";

        // Find common stack trace elements
        $lt= $loop->getStackTrace();
        for ($ct= $cc= sizeof($lt)- 1, $t= sizeof($tt)- 1; $ct > 0, $t > 0; $cc--, $t--) {
          if (!$lt[$cc]->equals($tt[$t])) break;
        }

        // Output uncommon elements only and one line how many common elements exist!
        for ($i= 0; $i < $cc; $i++) {
          $s.= xp::stringOf($lt[$i]); 
        }
        if ($cc != $ct) $s.= '  ... '.($ct - $cc + 1)." more\n";
        
        $loop= $loop instanceof ChainedException ? $loop->cause : NULL;
      }
      
      return $s;
    }
  }
?>
