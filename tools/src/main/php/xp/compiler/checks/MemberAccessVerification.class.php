<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.checks.Check', 'xp.compiler.ast.MemberAccessNode', 'xp.compiler.types.TypeInstance');

  /**
   * Verifies method calls
   *
   * @test    xp://net.xp_lang.tests.checks.MemberAccessVerificationTest
   */
  class MemberAccessVerification extends Object implements Check {

    /**
     * Return node this check works on
     *
     * @return  lang.XPClass<? extends xp.compiler.ast.Node>
     */
    public function node() {
      return XPClass::forName('xp.compiler.ast.MemberAccessNode');
    }

    /**
     * Return whether this check is to be run deferred
     *
     * @return  bool
     */
    public function defer() {
      return TRUE;
    }
    
    /**
     * Executes this check
     *
     * @param   xp.compiler.ast.Node node
     * @param   xp.compiler.types.Scope scope
     * @return  bool
     */
    public function verify(xp·compiler·ast·Node $node, Scope $scope) {
      $access= cast($node, 'xp.compiler.ast.MemberAccessNode');

      // Verify type
      // * var: Might have method
      // * primitive, array, map, int: Definitely don't have fields
      $type= $scope->typeOf($access->target);
      if ($type->isVariable()) {
        return array('T203', 'Member access (var).'.$access->name.'() verification deferred until runtime');
      } else if (!$type->isClass()) {
        return array('T305', 'Using member access on unsupported type '.$type->compoundName());
      }

      // Verify target method exists
      $target= new TypeInstance($scope->resolveType($type));
      if ($target->hasField($access->name)) {
        $member= $target->getField($access->name);
      } else if ($target->hasProperty($access->name)) {
        $member= $target->getProperty($access->name);
      } else {
        return array('T404', 'No such field $'.$access->name.' in '.$target->name());
      }
      
      // Verify visibility
      if (!($member->modifiers & MODIFIER_PUBLIC)) {
        $enclosing= $scope->resolveType($scope->declarations[0]->name);
        if (
          ($member->modifiers & MODIFIER_PRIVATE && !$enclosing->equals($target)) ||
          ($member->modifiers & MODIFIER_PROTECTED && !($enclosing->equals($target) || $enclosing->isSubclassOf($target)))
        ) {
          return array('T403', sprintf(
            'Accessing %s %s::$%s from %s',
            implode(' ', Modifiers::namesOf($member->modifiers)),
            $target->name(),
            $member->name,
            $enclosing->name()
          ));
        }
      }
    }
  }
?>
