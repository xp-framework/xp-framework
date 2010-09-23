<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'xp.compiler.ast.Visitor',
    'xp.compiler.ast.VariableNode',
    'xp.compiler.ast.MemberAccessNode'
  );

  /**
   * Promote all variables used inside a node to member variables except for
   * the ones passed in as excludes, returning all replacements.
   *
   * @test    xp://tests.LocalsToMemberPromoterTest
   */
  class LocalsToMemberPromoter extends Visitor {
    protected $excludes= array('this' => TRUE);
    protected $replacements= array();

    protected static $THIS;
    
    static function __static() {
      self::$THIS= new VariableNode('this');
    }

    /**
     * Visit a variable
     *
     * @param   xp.compiler.ast.Node node
     */
    protected function visitVariable(VariableNode $node) {
      $n= $node->name;
      if (!isset($this->excludes[$n])) {
        $this->replacements['$'.$n]= $node= new MemberAccessNode(self::$THIS, $node->name);
      }
      return $node;
    }

    /**
     * Add a variable to exclude from promotion
     *
     * @param   string name
     */
    public function exclude($name) {
      $this->excludes[$name]= TRUE;
    }

    /**
     * Run
     *
     * @param   xp.compiler.ast.Node nodes
     * @return  array<string, xp.compiler.ast.MemberAccessNode> replaced
     */
    public function promote($node) {
      $this->replacements= array();
      $node= $this->visitOne($node);
      return array('replaced' => $this->replacements, 'node' => $node);
    }
  }
?>
