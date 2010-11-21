<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.optimize.Optimization', 'xp.compiler.ast.Visitor');

  /**
   * Base class for inlining
   *
   * Only inlines one-line methods such as:
   * <code>
   *   T() { return [EXPR]; } 
   * </code>
   *
   * @see   http://en.wikipedia.org/wiki/Inline_expansion
   * @see   xp://xp.compiler.checks.IsInlineable
   * @test  xp://net.xp_lang.tests.optimization.InliningOptimizationTest
   */
  abstract class InliningOptimization extends Object implements Optimization {
    protected static $rewriter= NULL;
    protected $protect= array();
    
    static function __static() {
      self::$rewriter= ClassLoader::defineClass('InliningOptimization··Rewriter', 'xp.compiler.ast.Visitor', array(), '{
        protected $replacements;

        public function __construct($replacements) {
          $this->replacements= $replacements;
        }

        protected function visitVariable(VariableNode $node) {
          return isset($this->replacements[$node->name])
            ? $this->replacements[$node->name]
            : $node
          ;
        }
      }');
    }
    
    /**
     * Returns whether this is a local call
     *
     * @param   xp.compiler.types.Scope scope
     * @param   xp.compiler.types.TypeName type
     * @return  bool
     */
    protected function isLocal($scope, $type) {
      return $scope->declarations[0]->name->name === $scope->resolveType($type)->name();
    }
    
    /**
     * Inline if possible
     *
     * @param   xp.compiler.ast.Node call either a MethodCallNode or a StaticMethodCallNode
     * @param   xp.compiler.types.Scope scope
     * @param   xp.compiler.optimize.Optimizations optimizations
     * @return  xp.compiler.ast.Node inlined
     */
    public function inline($call, $scope, $optimizations) {
      $key= $scope->declarations[0]->name->compoundName().$call->name;
      if (isset($this->protect[$key])) {
        // DEBUG Console::writeLine('**Recursion** Not inlining ', $key, ' from inside ', $scope->getClassName().'::'.$scope->name, ': ', $this->protect);
        return $call;
      }
      
      // Find candidate and rewrite body. 
      foreach ($scope->declarations[0]->body as $member) {
        if (
          $member instanceof MethodNode && 
          MODIFIER_INLINE & $member->modifiers && 
          $member->name === $call->name &&
          1 == sizeof($member->body) &&
          $member->body[0] instanceof ReturnNode
        ) {
          $replacements= array();
          foreach ($member->parameters as $i => $parameter) {
            $replacements[$parameter['name']]= $call->arguments[$i];
          }
          
          // DEBUG Console::writeLine('Inlining ', $key, ' from inside ', $scope->getClassName().'::'.$scope->name);
          $this->protect[$key]= TRUE;
          $call= $optimizations->optimize(self::$rewriter->newInstance($replacements)->visitOne($member->body[0]->expression), $scope);
          unset($this->protect[$key]);
        }
      }
      
      // Not inlineable
      return $call;
    }
  }
?>
