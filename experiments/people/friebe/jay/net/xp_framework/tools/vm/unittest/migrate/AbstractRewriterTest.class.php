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
      
      $this->template= '<?php class AbstractRewriterTest extends Object { function test() { '.
        '/* < */ %s /* > */'.
      '}} ?>';

      $names->current->qualifiedName= $this->getClassName();
      $m= &new MethodDoc();
      $m->name= 'test';
      $names->current->methods= array(&$m);
      $this->rewriter= &new SourceRewriter();
      $this->rewriter->setNameMapping($names);
    }
    
    /**
     * Setup method
     *
     * @access  protected
     * @param   string expect expected sourcecode after rewriting occurs
     * @param   string original sourcecode
     */
    function assertRewritten($expect, $origin) {
      try(); {
        $out= $this->rewriter->rewrite(token_get_all(sprintf($this->template, $origin)));
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      
      $relevant= substr($out, $p= strpos($out, '/* < */')+ 8, strpos($out, '/* > */')- $p- 1);
      $this->assertEquals($expect, $relevant);
    }
  }
?>
