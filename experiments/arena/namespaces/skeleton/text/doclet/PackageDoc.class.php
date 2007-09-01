<?php
/* This class is part of the XP framework
 *
 * $Id: PackageDoc.class.php 9106 2007-01-03 17:43:17Z friebe $
 */

  namespace text::doclet;
 
  uses('text.doclet.Doc');

  /**
   * Represents an XP package.
   *
   * @purpose  Documents a package
   */
  class PackageDoc extends Doc {

    /**
     * Constructor
     *
     */
    public function __construct($name= ) {
      $this->name= $name;
    }

    /**
     * Returns a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'<'.$this->name.'>';
    }

    /**
     * Returns a hashcode for this object
     *
     * @return  string
     */
    public function hashCode() {
      return $this->getClassName().$this->name;
    }
  }
?>
