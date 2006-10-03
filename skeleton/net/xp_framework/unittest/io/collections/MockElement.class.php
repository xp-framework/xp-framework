<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents a Mock element
   *
   * @see      xp://net.xp_framework.unittest.io.collections.MockCollection
   * @purpose  Mock object
   */
  class MockElement extends Object {
    var
      $uri= '';

    /**
     * Constructor
     *
     * @access  publid
     * @param   string uri
     */
    function __construct($uri) {
      $this->uri= $uri;
    }

    /**
     * Returns this element's URI
     *
     * @access  public
     * @return  string
     */
    function getURI() { 
      return $this->uri;
    }

    /**
     * Creates a string representation of this object
     *
     * @access  public
     * @return  string
     */
    function toString() { 
      return $this->getClassName().'('.$this->uri.')';
    }

  } implements(__FILE__, 'io.collections.IOElement');
?>
