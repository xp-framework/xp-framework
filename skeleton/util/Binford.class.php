<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('peer.Header');
  
  /**
   * This class has more power
   *
   * @purpose Die Klasse für mehr Power
   * @see     http://www.binford.de/
   */
  class Binford extends Object { 
    var 
      $poweredBy= 6100;
    
    /**
     * Set the power
     *
     * @access  public
     * @param   int p power
     * @throws  IllegalArgumentException in case the parameter p contains an illegal value
     */
    function setPoweredBy($p) {
      if (!($x= log10($p / 6.1)) || (floor($x) != $x)) {
        return throw(new IllegalArgumentException($p.' not allowed'));
      }
      $this->poweredBy= $p;
    }
   
    /**
     * Retreive the power
     *
     * @access  public
     * @return  int power
     */
    function getPoweredBy() {
      return $this->poweredBy;
    }
    
    /**
     * Retreive header suited for HTTP/Mail
     *
     * Example:
     * <pre>
     *   X-Binford: 6100 (more power)
     * </pre>
     *
     * @access  public
     * @return  &peer.Header
     */
    function getHeader() {
      return new Header('X-Binford', $this->poweredBy.' (more power)');
    }
  }
?>
