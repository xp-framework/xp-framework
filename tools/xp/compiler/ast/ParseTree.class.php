<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.ast.TypeDeclarationNode', 'xp.compiler.ast.Node');

  /**
   * (Insert class' description here)
   *
   * @purpose  Value object
   */
  class ParseTree extends Object {
    public
      $package,
      $imports,
      $declaration,
      $origin;

    /**
     * Constructor
     *
     * @param   string package
     * @param   xp.compiler.ast.Node[] imports
     * @param   xp.compiler.ast.TypeDeclarationNode declaration
     */
    public function __construct($package= '', $imports= array(), TypeDeclarationNode $declaration= NULL) {
      $this->package= $package;
      $this->imports= $imports;
      $this->declaration= $declaration;
    }

    /**
     * Creates a string representation of this node.
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        "%s(package %s)@{\n".
        "  imports     : %s\n".
        "  declaration : %s\n".
        "}",
        $this->getClassName(), 
        $this->package ? $this->package->name : '<main>',
        str_replace("\n", "\n  ", xp::stringOf($this->imports)),
        str_replace("\n", "\n  ", $this->declaration->toString())
      );
    }
  }
?>
