<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'xp.compiler.checks.ArmTypesAreCloseable',
    'xp.compiler.ast.ArmNode',
    'xp.compiler.ast.AssignmentNode',
    'xp.compiler.ast.VariableNode',
    'xp.compiler.ast.InstanceCreationNode',
    'xp.compiler.ast.ClassNode',
    'xp.compiler.types.TypeDeclarationScope'
  );

  /**
   * TestCase
   *
   * @see      xp://xp.compiler.checks.ArmTypesAreCloseable
   */
  class ArmTypesAreCloseableTest extends TestCase {
    protected $fixture= NULL;
    protected $scope= NULL;
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->fixture= new ArmTypesAreCloseable();
      $this->scope= new TypeDeclarationScope();
      $this->scope->declarations[0]= new ClassNode(MODIFIER_PUBLIC, array(), new TypeName('Test'));
    }
    
    /**
     * Creates a new 
     *
     * @param   string[] types
     * @return  xp.compiler.ast.ArmNode
     */
    protected function newArmNode($types) {
      $assignments= $variables= array();
      foreach ($types as $i => $name) {
        $var= new VariableNode('a'.$i);
        $type= new TypeName($name);
        $assign= new AssignmentNode(array(
          'variable'   => $var,
          'expression' => new InstanceCreationNode(array(
            'type'       => $type,
            'parameters' => array(),
            'body'       => NULL
          )),
          'op'         => '='
        ));
        $assignments[]= $assign;
        $variables[]= $var;
        $this->scope->setType($var, $type);
      }
      return new ArmNode($assignments, $variables, array());
    }

    /**
     * Wrapper around verify
     *
     * @param   xp.compiler.ast.ArmNode field
     * @return  var
     */
    protected function verify(ArmNode $field) {
      return $this->fixture->verify($field, $this->scope);
    }
    
    /**
     * Test io.streams.TextReader
     *
     */
    #[@test]
    public function textReaderIsCloseable() {
      $this->assertNull(
        $this->verify($this->newArmNode(array('io.streams.TextReader')))
      );
    }

    /**
     * Test lang.Object
     *
     */
    #[@test]
    public function objectIsNotCloseable() {
      $this->assertEquals(
        array('A403', 'Type lang.Object for assignment #1 in ARM block is not closeable'),
        $this->verify($this->newArmNode(array('lang.Object')))
      );
    }

    /**
     * Test io.streams.TextReader and lang.Object
     *
     */
    #[@test]
    public function oneIsNotCloseable() {
      $this->assertEquals(
        array('A403', 'Type lang.Object for assignment #2 in ARM block is not closeable'),
        $this->verify($this->newArmNode(array('io.streams.TextReader', 'lang.Object')))
      );
    }
  }
?>
