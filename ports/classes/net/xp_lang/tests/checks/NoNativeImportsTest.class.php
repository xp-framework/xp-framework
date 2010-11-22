<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'xp.compiler.checks.NoNativeImports',
    'xp.compiler.ast.NativeImportNode',
    'xp.compiler.types.CompilationUnitScope'
  );

  /**
   * TestCase
   *
   * @see      xp://xp.compiler.checks.NoNativeImports
   */
  class NoNativeImportsTest extends TestCase {
    protected $fixture= NULL;
    protected $scope= NULL;
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->fixture= new NoNativeImports();
      $this->scope= new CompilationUnitScope();
    }
    
    /**
     * Test importing a function (namespace.function)
     *
     */
    #[@test]
    public function functionImport() {
      $this->assertEquals(
        array('N415', 'Native imports (pcre.preg_match) make code non-portable'), 
        $this->fixture->verify(new NativeImportNode(array('name' => 'pcre.preg_match')), $this->scope)
      );
    }

    /**
     * Test importing on demand (namespace.*) 
     *
     */
    #[@test]
    public function importOnDemand() {
      $this->assertEquals(
        array('N415', 'Native imports (pcre.*) make code non-portable'), 
        $this->fixture->verify(new NativeImportNode(array('name' => 'pcre.*')), $this->scope)
      );
    }
  }
?>
