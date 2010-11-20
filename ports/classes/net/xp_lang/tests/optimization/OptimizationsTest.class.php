<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'xp.compiler.ast.StringNode',
    'xp.compiler.optimize.Optimizations',
    'xp.compiler.types.MethodScope'
  );

  /**
   * TestCase for Optimizations class
   *
   * @see   xp://xp.compiler.optimize.Optimizations
   */
  class OptimizationsTest extends TestCase {
    protected static $optimization;
    
    static function __static() {
      self::$optimization= newinstance('xp.compiler.optimize.Optimization', array(), '{
        public function node() { 
          return XPClass::forName("xp.compiler.ast.StringNode"); 
        }

        public function optimize(xp·compiler·ast·Node $in, Scope $scope, Optimizations $optimizations) {
          return new StringNode("Optimized: ".$in->value);
        }
      }');
    }
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->fixture= new Optimizations();
      $this->scope= new MethodScope();
    }
    
    /**
     * Test optimize()
     *
     */
    #[@test]
    public function withoutOptimization() {
      $this->assertEquals(
        new StringNode('Test'), 
        $this->fixture->optimize(new StringNode('Test'), $this->scope)
      );
    }
    
    /**
     * Tests add() and optimize()
     *
     */
    #[@test]
    public function withOptimization() {
      $this->fixture->add(self::$optimization);
      $this->assertEquals(
        new StringNode('Optimized: Test'), 
        $this->fixture->optimize(new StringNode('Test'), $this->scope)
      );
    }

    /**
     * Test clear() and optimize()
     *
     */
    #[@test]
    public function clearOptimizations() {
      $this->fixture->add(self::$optimization);
      $this->fixture->clear();
      $this->assertEquals(
        new StringNode('Test'), 
        $this->fixture->optimize(new StringNode('Test'), $this->scope)
      );
    }
  }
?>
