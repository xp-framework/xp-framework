<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents a JNLP resource
   *
   * @see      xp://com.sun.webstart.JnlpDocument
   * @purpose  Abstract base class
   */
  class JnlpResource extends Object {
  
    /**
     * Get name
     *
     * @return  string
     */
    public function getTagName() { }

    /**
     * Get attributes
     *
     * @return  array
     */
    public function getTagAttributes() { }
  }
?>
