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
      $poweredBy= 6100;
    
    /**
     * Set the power
     *
     * @access  public
     * @param   int p power
     * @throws  lang.IllegalArgumentException in case the parameter p contains an illegal value
     */
    public function setPoweredBy($p) {
      if (!($x= log10($p / 6.1)) || (floor($x) != $x)) {
        throw (new IllegalArgumentException($p.' not allowed'));
      }
      $this->poweredBy= $p;
    }
   
    /**
     * Retrieve the power
     *
     * @access  public
     * @return  int power
     */
    public function getPoweredBy() {
      return $this->poweredBy;
    }
    
    /**
     * Retrieve header suited for HTTP/Mail
     *
     * Example:
     * <pre>
     *   X-Binford: 6100 (more power)
     * </pre>
     *
     * @access  public
     * @return  &peer.Header
     */
    public function getHeader() {
      return new Header('X-Binford', $this->poweredBy.' (more power)');
    }
  }
?>
