<?php
/* This class is part of the XP framework
 *
 * $Id: JnlpResource.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace com::sun::webstart::jnlp;

  /**
   * Represents a JNLP resource
   *
   * @see      xp://com.sun.webstart.JnlpDocument
   * @purpose  Abstract base class
   */
  class JnlpResource extends lang::Object {
  
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
