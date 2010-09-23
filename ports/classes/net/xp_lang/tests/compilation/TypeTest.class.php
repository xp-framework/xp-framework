<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'io.streams.MemoryInputStream',
    'xp.compiler.emit.source.Emitter',
    'xp.compiler.types.TaskScope',
    'xp.compiler.diagnostic.NullDiagnosticListener',
    'xp.compiler.io.FileManager',
    'xp.compiler.task.CompilationTask'
  );

  /**
   * TestCase
   *
   * @see   xp://xp.compiler.types.CompiledType
   */
  class TypeTest extends TestCase {
    protected $scope;
    protected $emitter;
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->emitter= new xp·compiler·emit·source·Emitter();
      $this->scope= new TaskScope(new CompilationTask(
        new FileSource(new File(__FILE__), Syntax::forName('xp')),
        new NullDiagnosticListener(),
        new FileManager(),
        $this->emitter
      ));
    }

    /**
     * Compile class from source and return compiled type
     *
     * @param   string src
     * @return  xp.compiler.types.Types
     */
    protected function compile($src) {
      $r= $this->emitter->emit(
        Syntax::forName('xp')->parse(new MemoryInputStream($src)),
        $this->scope
      );
      return $r->type();
    }

    /**
     * Test name() on compiled type
     *
     */
    #[@test]
    public function name() {
      $this->assertEquals('Person', $this->compile('class Person { }')->name());
    }

    /**
     * Test name() on compiled type
     *
     */
    #[@test]
    public function nameInsidePackage() {
      $this->assertEquals('demo.Person', $this->compile('package demo; class Person { }')->name());
    }

    /**
     * Test name() on compiled type
     *
     */
    #[@test]
    public function packageNameInsidePackage() {
      $this->assertEquals('demo.Person', $this->compile('package demo; package class Person { }')->name());
    }

    /**
     * Test literal() on compiled type
     *
     */
    #[@test]
    public function literal() {
      $this->assertEquals('Person', $this->compile('class Person { }')->literal());
    }

    /**
     * Test literal() on compiled type
     *
     */
    #[@test]
    public function literalInsidePackage() {
      $this->assertEquals('Person', $this->compile('package demo; class Person { }')->literal());
    }

    /**
     * Test literal() on compiled type
     *
     */
    #[@test]
    public function packageLiteralInsidePackage() {
      $this->assertEquals('demo·Person', $this->compile('package demo; package class Person { }')->literal());
    }

    /**
     * Test hasField() on compiled type
     *
     */
    #[@test]
    public function classFieldExists() {
      $t= $this->compile('class Person { public string $name; }');
      $this->assertTrue($t->hasField('name'));
    }

    /**
     * Test getField() on compiled type
     *
     */
    #[@test]
    public function classField() {
      $f= $this->compile('class Person { public string $name; }')->getField('name');
      $this->assertEquals('name', $f->name);
      $this->assertEquals(new TypeName('string'), $f->type);
      $this->assertEquals(MODIFIER_PUBLIC, $f->modifiers);
    }

    /**
     * Test hasProperty() on compiled type
     *
     */
    #[@test]
    public function classPropertyExists() {
      $t= $this->compile('class Person { public string name { get { } set { } } }');
      $this->assertTrue($t->hasProperty('name'));
    }

    /**
     * Test getProperty() on compiled type
     *
     */
    #[@test]
    public function classProperty() {
      $f= $this->compile('class Person { public string name { get { } set { } } }')->getProperty('name');
      $this->assertEquals('name', $f->name);
      $this->assertEquals(new TypeName('string'), $f->type);
      $this->assertEquals(MODIFIER_PUBLIC, $f->modifiers);
    }

    /**
     * Test hasField() on compiled type
     *
     */
    #[@test]
    public function classStaticFieldExists() {
      $t= $this->compile('class Logger { public static self $instance; }');
      $this->assertTrue($t->hasField('instance'));
    }

    /**
     * Test getField() on compiled type
     *
     */
    #[@test]
    public function classStaticField() {
      $f= $this->compile('class Logger { public static self $instance; }')->getField('instance');
      $this->assertEquals('instance', $f->name);
      $this->assertEquals(new TypeName('Logger'), $f->type);
      $this->assertEquals(MODIFIER_STATIC | MODIFIER_PUBLIC, $f->modifiers);
    }
    
    /**
     * Test hasField() on compiled type
     *
     */
    #[@test]
    public function enumFieldExists() {
      $t= $this->compile('enum Days { MON, TUE, WED, THU, FRI, SAT, SUN }');
      $this->assertTrue($t->hasField('MON'));
    }

    /**
     * Test getField() on compiled type
     *
     */
    #[@test]
    public function enumField() {
      $f= $this->compile('enum Days { MON, TUE, WED, THU, FRI, SAT, SUN }')->getField('MON');
      $this->assertEquals('MON', $f->name);
      $this->assertEquals(new TypeName('Days'), $f->type);
      $this->assertEquals(MODIFIER_STATIC | MODIFIER_PUBLIC, $f->modifiers);
    }

    /**
     * Test hasConstant() on compiled type
     *
     */
    #[@test]
    public function classConstantExists() {
      $t= $this->compile('class StringConstants { const string LF= "\n"; }');
      $this->assertTrue($t->hasConstant('LF'));
    }

    /**
     * Test getConstant() on compiled type
     *
     */
    #[@test]
    public function classConstant() {
      $c= $this->compile('class StringConstants { const string LF= "\n"; }')->getConstant('LF');
      $this->assertEquals('LF', $c->name);
      $this->assertEquals(new TypeName('string'), $c->type);
      $this->assertEquals("\n", $c->value);
    }

    /**
     * Test hasConstant() on compiled type
     *
     */
    #[@test]
    public function interfaceConstantExists() {
      $t= $this->compile('interface StringConstants { const string LF= "\n"; }');
      $this->assertTrue($t->hasConstant('LF'));
    }

    /**
     * Test getConstant() on compiled type
     *
     */
    #[@test]
    public function interfaceConstant() {
      $c= $this->compile('interface StringConstants { const string LF= "\n"; }')->getConstant('LF');
      $this->assertEquals('LF', $c->name);
      $this->assertEquals(new TypeName('string'), $c->type);
      $this->assertEquals("\n", $c->value);
    }

    /**
     * Test hasMethod() on compiled type
     *
     */
    #[@test]
    public function classMethodExists() {
      $t= $this->compile('class String { public self substring(int $start, int $len) { }}');
      $this->assertTrue($t->hasMethod('substring'));
    }

    /**
     * Test getMethod() on compiled type
     *
     */
    #[@test]
    public function classMethod() {
      $m= $this->compile('class String { public self substring(int $start, int $len) { }}')->getMethod('substring');
      $this->assertEquals('substring', $m->name);
      $this->assertEquals(new TypeName('String'), $m->returns);
      $this->assertEquals(MODIFIER_PUBLIC, $m->modifiers);
      $this->assertEquals(array(new TypeName('int'), new TypeName('int')), $m->parameters);
    }

    /**
     * Test hasOperator() on compiled type
     *
     */
    #[@test]
    public function classOperatorExists() {
      $t= $this->compile('class Complex { public static self operator + (self $a, self $b) { }}');
      $this->assertTrue($t->hasOperator('+'));
    }

    /**
     * Test getOperator() on compiled type
     *
     */
    #[@test]
    public function classOperator() {
      $m= $this->compile('class Complex { public static self operator + (self $a, self $b) { }}')->getOperator('+');
      $this->assertEquals('+', $m->symbol);
      $this->assertEquals(new TypeName('Complex'), $m->returns);
      $this->assertEquals(MODIFIER_PUBLIC | MODIFIER_STATIC, $m->modifiers);
      $this->assertEquals(array(new TypeName('Complex'), new TypeName('Complex')), $m->parameters);
    }

    /**
     * Test hasMethod() on compiled type
     *
     */
    #[@test]
    public function enumMethodExists() {
      $t= $this->compile('enum Coin { penny(1), nickel(2), dime(10), quarter(25); public string color() { }}');
      $this->assertTrue($t->hasMethod('color'));
    }

    /**
     * Test getMethod() on compiled type
     *
     */
    #[@test]
    public function enumMethod() {
      $m= $this->compile('enum Coin { penny(1), nickel(2), dime(10), quarter(25); public string color() { }}')->getMethod('color');
      $this->assertEquals('color', $m->name);
      $this->assertEquals(new TypeName('string'), $m->returns);
      $this->assertEquals(MODIFIER_PUBLIC, $m->modifiers);
      $this->assertEquals(array(), $m->parameters);
    }

    /**
     * Test hasIndexer() on compiled type
     *
     */
    #[@test]
    public function classIndexerExists() {
      $t= $this->compile('class ArrayList<T> { public T this[int $offset] { get { } set { } isset { } unset { } }}');
      $this->assertTrue($t->hasIndexer('color'));
    }

    /**
     * Test getIndexer() on compiled type
     *
     */
    #[@test]
    public function classIndexer() {
      $i= $this->compile('class ArrayList<T> { public T this[int $offset] { get { } set { } isset { } unset { } }}')->getIndexer();
      $this->assertEquals(new TypeName('T'), $i->type);
      $this->assertEquals(new TypeName('int'), $i->parameter);
    }
  }
?>
