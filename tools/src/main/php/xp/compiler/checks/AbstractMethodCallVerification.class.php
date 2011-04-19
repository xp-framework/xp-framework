<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.checks.Check', 'xp.compiler.types.TypeInstance');

  /**
   * Verifies method calls
   *
   * @see     xp://xp.compiler.checks.MethodCallVerification
   * @see     xp://xp.compiler.checks.StaticMethodCallVerification
   */
  abstract class AbstractMethodCallVerification extends Object implements Check {

    /**
     * Executes this check
     *
     * @param   xp.compiler.types.TypeName 
     * @param   string name method name
     * @param   xp.compiler.types.Scope scope
     * @return  string[] error or NULL
     */
    protected function verifyMethod($type, $name, $scope) {

      // Verify target method exists
      $target= new TypeInstance($scope->resolveType($type));
      if (!$target->hasMethod($name)) {
        return array('T404', 'No such method '.$name.'() in '.$target->name());
      }
      
      // Verify visibility
      $method= $target->getMethod($name);
      if (!($method->modifiers & MODIFIER_PUBLIC)) {
        $enclosing= $scope->resolveType($scope->declarations[0]->name);
        if (
          ($method->modifiers & MODIFIER_PRIVATE && !$enclosing->equals($target)) ||
          ($method->modifiers & MODIFIER_PROTECTED && !($enclosing->equals($target) || $enclosing->isSubclassOf($target)))
        ) {
          return array('T403', sprintf(
            'Invoking %s %s::%s() from %s',
            implode(' ', Modifiers::namesOf($method->modifiers)),
            $target->name(),
            $method->name,
            $enclosing->name()
          ));
        }
      }
    }
  }
?>
