<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'xp.compiler.checks.ArrayAccessVerification',
    'xp.compiler.ast.ArrayAccessNode',
    'xp.compiler.ast.VariableNode',
    'xp.compiler.ast.IntegerNode',
    'xp.compiler.ast.StringNode',
    'xp.compiler.ast.VariableNode',
    'xp.compiler.ast.InstanceCreationNode',
    'xp.compiler.types.MethodScope'
  );

  /**
   * TestCase
   *
   * @see      xp://xp.compiler.checks.ArrayAccessVerification
   */
  class ArrayAccessVerificationTest extends TestCase {
    protected $fixture= NULL;
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->fixture= new ArrayAccessVerification();
    }
    
    /**
     * Wrapper around verify
     *
     * @param   xp.compiler.ast.Node call
     * @return  var
     */
    protected function verify(xp·compiler·ast·Node $target) {
      return $this->fixture->verify(new ArrayAccessNode($target, new IntegerNode(0)), new MethodScope());
    }
    
    /**
     * Test array access on a string[]
     *
     */
    #[@test]
    public function stringArray() {
      $this->assertNull(
        $this->verify(new ArrayNode(array('type' => new TypeName('string[]'), 'values' => array())))
      );
    }

    /**
     * Test array access on a [:string]
     *
     */
    #[@test]
    public function stringMap() {
      $this->assertNull(
        $this->verify(new MapNode(array('type' => new TypeName('[:string]'), 'elements' => array())))
      );
    }

    /**
     * Test array access on an int primitive
     *
     */
    #[@test]
    public function int() {
      $this->assertEquals(
        array('T305', 'Using array-access on unsupported type xp.compiler.types.TypeName(int)'),
        $this->verify(new IntegerNode())
      );
    }

    /**
     * Test array access on an undeclared variable (type: var)
     *
     */
    #[@test]
    public function undeclared() {
      $this->assertEquals(
        array('T203', 'Array access (var)[0] verification deferred until runtime'),
        $this->verify(new VariableNode('undeclared'))
      );
    }

    /**
     * Test array access on a string primitive
     *
     */
    #[@test]
    public function string() {
      $this->assertNull(
        $this->verify(new StringNode())
      );
    }

    /**
     * Test array access on an object that supports indexers
     *
     */
    #[@test]
    public function arrayList() {
      $this->assertNull(
        $this->verify(new InstanceCreationNode(array('type' => new TypeName('lang.types.ArrayList'))))
      );
    }

    /**
     * Test array access on an interface that supports indexers
     *
     */
    #[@test]
    public function anonymousIListInstance() {
      $this->assertNull(
        $this->verify(new InstanceCreationNode(array('type' => new TypeName('util.collections.IList'), 'body' => array(
          // Implementation missing, irrelevant to this test
        ))))
      );
    }

    /**
     * Test array access on an object that does not support indexers
     *
     */
    #[@test]
    public function object() {
      $this->assertEquals(
        array('T305', 'Type lang.Object does not support offset access'),
        $this->verify(new InstanceCreationNode(array('type' => new TypeName('lang.Object'))))
      );
    }
  }
?>
