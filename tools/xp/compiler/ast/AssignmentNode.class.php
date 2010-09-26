<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.compiler.ast.Node');

  /**
   * Represents an assignment
   *
   * Examples:
   * <code>
   *   $a= 5;     // variable: a, op: =, expression: 5
   *   $a+= 10;   // variable: a, op: +=, expression: 10
   * </code>
   *
   * Operator may be one of:
   * <ul>
   *   <li>=   : Assignment</li>
   *   <li>+=  : Addition</li>
   *   <li>-=  : Subtraction</li>
   *   <li>*=  : Multiplication</li>
   *   <li>/=  : Division</li>
   *   <li>%=  : Modulo</li>
   *   <li>~=  : Concatenation</li>
   *   <li>|=  : Or</li>
   *   <li>&=  : And</li>
   *   <li>^=  : XOr</li>
   *   <li>>>= : Shift-Right</li>
   *   <li><<= : Shift-Left</li>
   * </li>
   *
   * @test    xp://net.xp_lang.tests.syntax.xp.AssignmentTest
   */
  class AssignmentNode extends xp·compiler·ast·Node {
    public $variable = NULL;
    public $op = NULL;
    public $expression = NULL;
  }
?>
