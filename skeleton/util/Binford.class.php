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
    var 
      $poweredBy= 0;

    /**
     * Constructor
     *
     * @access  public
     * @param   int power default 6100
     * @throws  lang.IllegalArgumentException in case power contains an illegal value
     */
    function __construct($poweredBy= 6100) {
      $this->setPoweredBy($poweredBy);
    }
    
    /**
     * Set the power
     *
     * @access  public
     * @param   int p power
     * @throws  lang.IllegalArgumentException in case the parameter p contains an illegal value
     */
    function setPoweredBy($p) {
      if (!($x= log10($p / 6.1)) || (floor($x) != $x)) {
        return throw(new IllegalArgumentException($p.' not allowed'));
      }
      $this->poweredBy= $p;
    }
   
    /**
     * Retrieve the power
     *
     * @access  public
     * @return  int power
     */
    function getPoweredBy() {
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
    function &getHeader() {
      return new Header('X-Binford', $this->poweredBy.' (more power)');
    }
  }
?>
