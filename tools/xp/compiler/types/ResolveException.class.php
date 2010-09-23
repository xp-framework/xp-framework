<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.ChainedException');

  /**
   * Indicates resolution of a name failed
   *
   */
  class ResolveException extends ChainedException {
    protected $kind;
    
    /**
     * Constructor
     *
     * @param   string message
     * @param   int kind
     * @param   lang.Throwable cause
     */
    public function __construct($message, $kind, Throwable $cause) {
      parent::__construct($message, $cause);
      $this->kind= $kind;
    }
    
    /**
     * Get kind
     *
     * @return  int
     */
    public function getKind() {
      return $this->kind;
    }
  }
?>
