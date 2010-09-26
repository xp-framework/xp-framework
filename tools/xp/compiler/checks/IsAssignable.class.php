<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.checks.Check', 'xp.compiler.ast.AssignmentNode');

  /**
   * Check whether a node is writeable - that is: can be the left-hand
   * side of an assignment
   *
   */
  class IsAssignable extends Object implements Check {

    /**
     * Return node this check works on
     *
     * @return  lang.XPClass<? extends xp.compiler.ast.Node>
     */
    public function node() {
      return XPClass::forName('xp.compiler.ast.AssignmentNode');
    }

    /**
     * Return whether this check is to be run deferred
     *
     * @return  bool
     */
    public function defer() {
      return FALSE;
    }
    
    /**
     * Check whether a node is writeable - that is: can be the left-hand
     * side of an assignment
     *
     * @param   xp.compiler.ast.Node node
     * @return  bool
     */
    protected function isWriteable($node) {
      return (
        $node instanceof VariableNode || 
        $node instanceof ArrayAccessNode || 
        $node instanceof MemberAccessNode ||
        $node instanceof StaticMemberAccessNode ||
        $node instanceof DynamicVariableReferenceNode
      );
    }
    
    /**
     * Executes this check
     *
     * @param   xp.compiler.ast.Node node
     * @param   xp.compiler.types.Scope scope
     * @return  bool
     */
    public function verify(xp·compiler·ast·Node $node, Scope $scope) {
      $a= cast($node, 'xp.compiler.ast.AssignmentNode');
      if (!$this->isWriteable($a->variable)) {
        return array('A403', 'Cannot assign to '.($a->variable instanceof Generic ? $a->variable->getClassName() : xp::stringOf($a->variable)).'s');
      }
    }
  }
?>
