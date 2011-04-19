<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xp.compiler.ast.Node');

  /**
   * Represents an annotation
   *
   * Example:
   * <code>
   *   [@Deprecated('Use Type instead')]
   * </code>
   *
   * <ul>
   *   <li>Deprecated is the type</li>
   *   <li>Parameters is the a map ("default" : "Use Type instead")</li>
   * </ul>
   *
   * @test    xp://net.xp_lang.tests.syntax.xp.AnnotationTest
   */
  class AnnotationNode extends xp·compiler·ast·Node {
    public $type       = NULL;
    public $parameters = array();
  }
?>
