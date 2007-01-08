<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$
 */

  uses('generic+xp://Filter');

  /**
   * I/O filter
   *
   * @purpose  Generics demonstration
   */
  class URIContainsFilter extends Object implements Filter<IOElement> {
    protected
      $substring= '';
    
    /**
     * Constructor
     *
     * @param string substring
     */
    public function __construct($substring) {
      $this->substring= $substring;
    }
    
    /**
     * Returns TRUE if a given subject should be accepted, FALSE otherwise.
     *
     * @param   io.collections.IOElement subject
     * @return  bool
     */
    public function accept(IOElement $subject) {
      return (bool)strstr($subject->getURI(), $this->substring);
    }
  }
?>
