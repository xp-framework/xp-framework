<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
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
    public function __construct($name= NULL) {
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

    /**
     * Returns the package this class is contained in
     *
     * @return  text.doclet.PackageDoc or NULL if this is a top-level package
     */
    public function containingPackage() {
      if (FALSE === ($p= strrpos($this->name, '.'))) return NULL;
      return $this->root->packageNamed(substr($this->name, 0, $p));
    }
  }
?>
