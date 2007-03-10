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
    protected
      $rewriter= NULL;

    /**
     * Setup method
     *
     */
    public function setUp() {
      $names= new NameMapping();
      $names->addMapping('Date', 'util.Date');
      $names->addMapping('Object', 'lang.Object');
      $names->setNamespaceSeparator('.');
      $names->setCurrentClass(new ClassDoc());
      
      $this->template= '<?php class AbstractRewriterTest extends Object { 
        public function expression() { /* e< */ %1$s /* >e */ }
        /* m< */ %2$s /* >m */ }
      } ?>';

      $names->current->qualifiedName= $this->getClassName();
      $m= new MethodDoc();
      $m->name= 'expression';
      $names->current->methods= array($m);
      $this->rewriter= new SourceRewriter();
      $this->rewriter->setNameMapping($names);
    }

    /**
     * Assert an expression is rewritten
     *
     * @param   string expect expected sourcecode after rewriting occurs
     * @param   string original sourcecode
     */
    protected function assertExpressionRewritten($expect, $origin) {
      $out= $this->rewriter->rewrite(token_get_all(sprintf($this->template, $origin, NULL)));
      $relevant= substr($out, $p= strpos($out, '/* e< */')+ 9, strpos($out, '/* >e */')- $p- 1);
      $this->assertEquals($expect, $relevant);
    }
    
    /**
     * Assert a method is rewritten
     *
     * @param   string expect expected sourcecode after rewriting occurs
     * @param   string method
     * @param   string modifiers
     * @param   array tags
     * @param   string original sourcecode
     */
    protected function assertMethodRewritten($expect, $modifiers, $method, $tags, $origin) {
      $m= new MethodDoc();
      $m->name= $method;
      
      $tm= TagletManager::getInstance();
      foreach ($tags as $tag => $values) {
        $kind= ltrim($tag, '@');
        $m->detail['tags'][$kind]= array();
        foreach ($values as $value) {
          $m->detail['tags'][$kind][]= &$tm->make($m, $kind, $value);
        }
      }
      
      $this->rewriter->names->current->methods[]= &$m;

      $origin= $modifiers.' function '.$method.$origin;
      $out= $this->rewriter->rewrite(token_get_all(sprintf($this->template, NULL, $origin)));
      $relevant= substr($out, $p= strpos($out, '/* m< */')+ 9, strpos($out, '/* >m */')- $p- 1);
      $this->assertEquals($expect, $relevant);
    }

    /**
     * Assert a class is rewritten
     *
     * @param   string expect expected sourcecode after rewriting occurs
     * @param   string type class type, one of "interface" or "class"
     * @param   string name fully qualified class name
     * @param   string extends default NULL fully qualified class name
     * @param   string[] implements default array() fully qualified class names
     */
    protected function assertClassRewritten($expect, $type, $name, $extends= NULL, $implements= array()) {
      $this->rewriter->names->current->qualifiedName= $name;
      
      if ($extends) {
        $this->rewriter->names->addMapping(xp::reflect($extends), $extends);
      }
      if ($implements) foreach ($implements as $interface) {
        $this->rewriter->names->addMapping(xp::reflect($interface), $interface);
      }
      
      $out= trim($this->rewriter->rewrite(token_get_all(sprintf(
        '<?php %s %s%s%s { } ?>',
        $type,
        xp::reflect($name),
        $extends ? ' extends '.xp::reflect($extends) : '',
        $implements ? ' implements '.implode(', ', array_map(array('xp', 'reflect'), $implements)) : ''
      ))));

      $this->assertEquals($expect, preg_replace('#[\r\n\s]+#', ' ', $out));
    }
  }
?>
