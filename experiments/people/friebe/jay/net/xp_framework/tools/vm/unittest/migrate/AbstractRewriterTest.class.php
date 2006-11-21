<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'unittest.TestCase',
    'text.doclet.ClassDoc',
    'net.xp_framework.tools.vm.util.NameMapping',
    'net.xp_framework.tools.vm.util.SourceRewriter'
  );

  /**
   * Base class for rewriter tests. Does not contain any tests itself
   * but will set up a rewriter and provide a assertRewritten() utility
   * method.
   *
   * @purpose  Base class
   */
  class AbstractRewriterTest extends TestCase {
    var
      $rewriter= NULL;

    /**
     * Setup method
     *
     * @access  public
     */
    function setUp() {
      $names= &new NameMapping();
      $names->addMapping('date', 'util.Date');
      $names->addMapping('object', 'lang.Object');
      $names->setNamespaceSeparator('.');
      $names->setCurrentClass(new ClassDoc());
      
      $this->template= '<?php class AbstractRewriterTest extends Object { 
        function expression() { /* e< */ %1$s /* >e */ }
        /* m< */ %2$s /* >m */ }
      } ?>';

      $names->current->qualifiedName= $this->getClassName();
      $m= &new MethodDoc();
      $m->name= 'expression';
      $names->current->methods= array(&$m);
      $this->rewriter= &new SourceRewriter();
      $this->rewriter->setNameMapping($names);
    }

    /**
     * Assert an expression is rewritten
     *
     * @access  protected
     * @param   string expect expected sourcecode after rewriting occurs
     * @param   string original sourcecode
     */
    function assertExpressionRewritten($expect, $origin) {
      try(); {
        $out= $this->rewriter->rewrite(token_get_all(sprintf($this->template, $origin, NULL)));
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      
      $relevant= substr($out, $p= strpos($out, '/* e< */')+ 9, strpos($out, '/* >e */')- $p- 1);
      $this->assertEquals($expect, $relevant);
    }
    
    /**
     * Assert a method is rewritten
     *
     * @access  protected
     * @param   string expect expected sourcecode after rewriting occurs
     * @param   string method
     * @param   array tags
     * @param   string original sourcecode
     */
    function assertMethodRewritten($expect, $method, $tags, $origin) {
      $m= &new MethodDoc();
      $m->name= $method;
      $this->rewriter->names->current->methods[]= &$m;

      $origin= 'function '.$method.$origin;
      try(); {
        $out= $this->rewriter->rewrite(token_get_all(sprintf($this->template, NULL, $origin)));
      } if (catch('Exception', $e)) {
        return throw($e);
      }

      $relevant= substr($out, $p= strpos($out, '/* m< */')+ 9, strpos($out, '/* >m */')- $p- 1);
      $this->assertEquals($expect, $relevant);
    }
  }
?>
