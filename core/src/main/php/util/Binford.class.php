<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('peer.Header');
  
  /**
   * This class has more power
   *
   * @purpose  Add power to an application
   * @see      http://www.binford.de/
   */
  class Binford extends Object { 
    public 
      $poweredBy= 0;

    /**
     * Constructor
     *
     * @param   int power default 6100
     * @throws  lang.IllegalArgumentException in case power contains an illegal value
     */
    public function __construct($poweredBy= 6100) {
      $this->setPoweredBy($poweredBy);
    }
    
    /**
     * Set the power
     *
     * @param   int p power
     * @throws  lang.IllegalArgumentException in case the parameter p contains an illegal value
     */
    public function setPoweredBy($p) {
      if (!($x= log10($p / 6.1)) || (floor($x) != $x)) {
        throw new IllegalArgumentException($p.' not allowed');
      }
      $this->poweredBy= $p;
    }
   
    /**
     * Retrieve the power
     *
     * @return  int power
     */
    public function getPoweredBy() {
      return $this->poweredBy;
    }
    
    /**
     * Retrieve string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'('.$this->poweredBy.')';
    }

    /**
     * Returns whether another object is equal to this object.
     *
     * @param   lang.Generic cmp
     * @return  bool
     */
    public function equals($cmp) {
      return $cmp instanceof self && $this->poweredBy == $cmp->poweredBy;
    }

    /**
     * Retrieve header suited for HTTP/Mail
     *
     * Example:
     * <pre>
     *   X-Binford: 6100 (more power)
     * </pre>
     *
     * @return  peer.Header
     */
    public function getHeader() {
      return new Header('X-Binford', $this->poweredBy.' (more power)');
    }
  }
?>
