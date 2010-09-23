<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.ast.Node', 'xp.compiler.types.Scope');
  
  /**
   * Verifies a given node
   *
   */
  interface Check {

    /**
     * Return node this check works on
     *
     * @return  lang.XPClass<? extends xp.compiler.ast.Node>
     */
    public function node();

    /**
     * Return whether this check is to be run deferred
     *
     * @return  bool
     */
    public function defer();
    
    /**
     * Execute this check
     *
     * @param   xp.compiler.ast.Node node
     * @param   xp.compiler.types.Scope scope
     * @return  bool
     */
    public function verify(xp·compiler·ast·Node $node, Scope $scope);
  }
?>
