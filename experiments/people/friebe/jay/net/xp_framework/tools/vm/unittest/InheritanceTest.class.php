<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'util.profiling.unittest.TestCase',
    'net.xp_framework.tools.vm.VNode'
  );

  /**
   * Tests inheritance
   *
   * @purpose  Unit Test
   */
  class InheritanceTest extends TestCase {

    /**
     * Finds a class and returns its declaration. Returns NULL if the 
     * specified class cannot be found.
     *
     * The following classes exist:
     * <ul>
     *   <li>Object - the base class, with toString() and equals() methods</li>
     *   <li>MailAddress - extends Object and overwrites toString()</li>
     * </ul>
     *
     * @access  protected
     * @return  &net.xp_framework.tools.vm.nodes.ClassDeclarationNode
     */
    function findClassDeclaration($name) {
      static $classes= array(); empty($classes) && $classes= array(
        'Object' => new ClassDeclarationNode(
          'Object',
          NULL,
          NULL,
          array(
            new MethodDeclarationNode(
              'toString',
              array(),
              'string',
              array(),
              MODIFIER_PUBLIC,
              array(),
              array()
            ),
            new MethodDeclarationNode(
              'equals',
              array(new ParameterNode('cmp', 'Object', NULL)),
              'bool',
              array(),
              MODIFIER_PUBLIC,
              array(),
              array()
            ),
          ),
          MODIFIER_PUBLIC,
          array()
        ),
        'MailAddress' => new ClassDeclarationNode(
          'MailAddress',
          'Object',
          NULL,
          array(
            new MethodDeclarationNode(
              'toString',
              array(),
              'string',
              array(),
              MODIFIER_PUBLIC,
              array(),
              array()
            )
          ),
          MODIFIER_PUBLIC,
          array()
        ),
        'ExternalMailAddress' => new ClassDeclarationNode(
          'ExternalMailAddress',
          'MailAddress',
          NULL,
          array(
            new MethodDeclarationNode(
              'toString',
              array(),
              'string',
              array(),
              MODIFIER_PUBLIC,
              array(),
              array()
            )
          ),
          MODIFIER_PUBLIC,
          array()
        ),
      );
      
      if (!isset($classes[$name])) return NULL;
      
      // Mark statements as declared in this class. 
      // TBD: Maybe the AST compiler should do this for us?
      for ($i= 0, $s= sizeof($classes[$name]->statements); $i < $s; $i++) {
        $classes[$name]->statements[$i]->declared || $classes[$name]->statements[$i]->declared= $name;
      }
      return $classes[$name];
    }

    /**
     * Returns Object class declaration
     *
     * @access  protected
     * @return  &net.xp_framework.tools.vm.nodes.ClassDeclarationNode
     */
    function &objectClassDeclaration() {
      return $this->findClassDeclaration('Object');
    }
    
    /**
     * Finds a method declared in a given class
     *
     * @access  protected
     * @return  &net.xp_framework.tools.vm.nodes.MethodDeclarationNode
     */
    function &findDeclaredMethod(&$declaration, $name) {
      for ($i= 0, $s= sizeof($declaration->statements); $i < $s; $i++) {
        if (
          is('InvokeableDeclarationNode', $declaration->statements[$i]) &&
          $declaration->statements[$i]->name == $name
        ) return $declaration->statements[$i];
      }

      return NULL;
    }
    
    /**
     * Returns the inherited version of the given class declaration
     *
     * @access  protected
     * @param   &net.xp_framework.tools.vm.nodes.ClassDeclarationNode declaration
     * @return  &net.xp_framework.tools.vm.nodes.ClassDeclarationNode
     * @throws  lang.ClassNotFoundException
     */
    function &inherited(&$declaration) {
    
      // Return the class declaration itself if no parent class exists
      if (!$declaration->extends) return $declaration;
      
      $extends= $declaration->extends;
      do {

        // Find and verify parent class exists
        if (!$parent= &$this->findClassDeclaration($extends)) {
          return throw(new ClassNotFoundException('Cannot find class "'.$declaration->extends.'"'));
        }

        // Inherit members
        for ($i= 0, $s= sizeof($parent->statements); $i < $s; $i++) switch (TRUE) {
          case is('InvokeableDeclarationNode', $parent->statements[$i]): {
            if (!$this->findDeclaredMethod($declaration, $parent->statements[$i]->name)) {
              $declaration->statements[]= &$parent->statements[$i];
            }
            break;
          }

          default: {
            return throw(new IllegalArgumentException(
              'Unknown member '.xp::typeOf($parent->statements[$i])
            ));
          }
        }
      } while ($extends= $parent->extends);
      
      return $declaration;
    }
    
    /**
     * Assert a given method exists in the given declaration
     *
     * @access  protected
     * @param   string spec
     * @param   &net.xp_framework.tools.vm.nodes.ClassDeclarationNode declaration
     * @throws  util.profiling.unittest.AssertionFailedError
     */
    function assertHasMethod($spec, &$declaration) {
      preg_match('/(.+)\(([^\)]+)?\) : (.+)/', $spec, $matches);
      $qualified= $declaration->name.'::'.$matches[1].'()';
      
      // Check if method exists
      if (!$method= &$this->findDeclaredMethod($declaration, $matches[1])) return $this->fail(
        'Method '.$qualified.' cannot be found', 
        NULL, 
        'exists'
      );
      
      // Check declaration
      if ($matches[3] != $method->declared) return $this->fail(
        $qualified.' declared incorrectly', 
        $method->declared, 
        $matches[3]
      );
    }

    /**
     * Tests invoking the inherited() method on a class without a parent
     * class returns the class itself.
     *
     * @access  public
     */
    #[@test]
    function inheritedObjectClass() {
      with ($class= &$this->objectClassDeclaration()); {
        $this->assertEquals($class, $this->inherited($class));
      }
    }

    /**
     * Tests invoking the inherited() method on a class with a non-
     * existant parent class throws an exception.
     *
     * @access  public
     */
    #[@test, @expect('lang.ClassNotFoundException')]
    function inheritedBrokenParentClass() {
      $this->inherited(new ClassDeclarationNode(
        'BrokenParentClass',
        '@@NON-EXISTANT-CLASS@@',
        NULL,
        array(),
        MODIFIER_PUBLIC,
        array()
      ));
    }

    /**
     * Tests inheriting ExternalMailAddress from MailAddress. The following 
     * should happen:
     * <ol>
     *   <li>The ExternalMailAddress::toString() method is untouched</li>
     *   <li>The Object::equals() method is inherited</li>
     * </ol>
     *
     * @access  public
     */
    #[@test]
    function inheritedExternalMailAddressClass() {
      with ($inherited= &$this->inherited($this->findClassDeclaration('ExternalMailAddress'))); {
        $this->assertHasMethod('toString() : ExternalMailAddress', $inherited);
        $this->assertHasMethod('equals() : Object', $inherited);
      }
    }

    /**
     * Tests inheriting MailAddress from Object. The following should happen:
     * <ol>
     *   <li>The MailAddress::toString() method is untouched</li>
     *   <li>The Object::equals() method is inherited</li>
     * </ol>
     *
     * @access  public
     */
    #[@test]
    function inheritedMailAddressClass() {
      with ($inherited= &$this->inherited($this->findClassDeclaration('MailAddress'))); {
        $this->assertHasMethod('toString() : MailAddress', $inherited);
        $this->assertHasMethod('equals() : Object', $inherited);
      }
    }
  }
?>
