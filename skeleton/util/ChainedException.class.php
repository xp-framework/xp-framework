<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'util';

  /**
   * Chained Exception
   *
   * Note: This class will be removed in the near future and is provided 
   * solely as means to ensure code using the old util.ChainedException 
   * class will not bail at the uses() statement when upgraded to 
   * 5.6.0-RELEASE.
   * <code>
   *   uses('util.ChainedException');
   *
   *   throw new ChainedException(...);
   * </code>
   * The above will acutally throw a lang.ChainedException!
   *
   * @purpose   BC
   * @see       xp://lang.ChainedException
   * @deprecated Use lang.ChainedException instead
   */
  class util·ChainedException extends XPException {
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
      $t= sizeof($this->trace);
      for ($i= 0; $i < $t; $i++) {
        $s.= $this->trace[$i]->toString(); 
      }
      if (!$this->cause) return $s;
      
      $loop= $this->cause;
      while ($loop) {

        // String of cause
        $s.= 'Caused by '.$loop->compoundMessage()."\n";

        // Find common stack trace elements
        for ($ct= $cc= sizeof($loop->trace)- 1, $t= sizeof($this->trace)- 1; $ct > 0, $t > 0; $cc--, $t--) {
          if (!$loop->trace[$cc]->equals($this->trace[$t])) break;
        }

        // Output uncommon elements only and one line how many common elements exist!
        for ($i= 0; $i < $cc; $i++) {
          $s.= xp::stringOf($loop->trace[$i]); 
        }
        if ($cc != $ct) $s.= '  ... '.($ct - $cc + 1)." more\n";
        
        $loop= $loop instanceof ChainedException ? $loop->cause : NULL;
      }
      
      return $s;
    }
  }
?>
