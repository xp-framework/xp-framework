<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('text.doclet.Doc');

  /**
   * Represents an XP package.
   *
   * @test     xp://net.xp_framework.unittest.text.doclet.PackageDocTest
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
     * Returns the source file name this doc was parsed from.
     *
     * @return  string
     */
    public function sourceFile() {
      return $this->root->findPackage($this->qualifiedName);
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
     * Returns whether this package contains another package.
     *
     * @param   text.doclet.PackageDoc other
     * @return  bool
     */
    public function contains(PackageDoc $other) {
      if (FALSE === ($p= strrpos($other->name, '.'))) return FALSE;
      return 0 === strncmp($this->name, $other->name, $p);
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
