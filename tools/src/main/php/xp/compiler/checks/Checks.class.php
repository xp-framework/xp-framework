<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.checks.Check');

  /**
   * Compiler checks
   *
   * @see   xp://xp.compiler.checks.Check
   * @test  xp://net.xp_lang.tests.checks.ChecksTest
   */
  class Checks extends Object {
    protected $impl= NULL;
    
    /**
     * Constructor.
     *
     */
    public function __construct() {
      $this->clear();
    }
    
    /**
     * Add a check
     *
     * @param   xp.compiler.checks.Check impl
     * @param   bool error
     */
    public function add(Check $impl, $error) {
      $this->impl[$impl->defer()][]= array($impl->node(), $impl, $error);
    }

    /**
     * Clear all implementations
     *
     */
    public function clear() {
      $this->impl= array(FALSE => array(), TRUE => array());
    }

    /**
     * Verify a given node
     *
     * @param   xp.compiler.ast.Node in
     * @param   xp.compiler.types.Scope scope
     * @param   var messages
     * @param   bool defer default FALSE
     * @return  bool whether to continue or not
     */
    public function verify(xp·compiler·ast·Node $in, Scope $scope, $messages, $defer= FALSE) {
      $continue= TRUE;
      foreach ($this->impl[$defer] as $impl) {
        if (!$impl[0]->isInstance($in)) continue;
        if (!($message= $impl[1]->verify($in, $scope))) continue;
        if ($impl[2]) {
          $messages->error($message[0], $message[1], $in);
          $continue= FALSE;
        } else {
          $messages->warn($message[0], $message[1], $in);
        }
      }
      return $continue;
    }
  }
?>
