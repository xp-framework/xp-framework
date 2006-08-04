<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Chained Exception
   *
   * @purpose  Exception base class
   * @see      http://mindprod.com/jgloss/chainedexceptions.html
   * @see      http://www.jguru.com/faq/view.jsp?EID=1026405  
   */
  class ChainedException extends XPException {
    public
      $cause    = NULL;

    /**
     * Constructor
     *
     * @access  public
     * @param   string message
     * @param   &lang.Throwable cause
     */
    public function __construct($message, &$cause) {
      parent::__construct($message);
      $this->cause= &$cause;
    }

    /**
     * Set cause
     *
     * @access  public
     * @param   &lang.Throwable cause
     */
    public function setCause(&$cause) {
      $this->cause= &$cause;
    }

    /**
     * Get cause
     *
     * @access  public
     * @return  &lang.Throwable
     */
    public function &getCause() {
      return $this->cause;
    }
    
    /**
     * Return string representation of this exception
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      return parent::toString().($this->cause
        ? 'Caused by '.$this->cause->toString() 
        : ''
      );
    }
  }
?>
