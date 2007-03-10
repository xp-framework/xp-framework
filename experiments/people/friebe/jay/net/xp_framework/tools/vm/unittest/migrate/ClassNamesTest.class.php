<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'unittest.TestCase',
    'net.xp_framework.tools.vm.util.MigrationNameMapping'
  );

  /**
   * Tests class names
   *
   * @purpose  Unit Test
   */
  class ClassNamesTest extends TestCase {
    protected
      $names= NULL;
      
    /**
     * Setup method
     *
     */
    public function setUp() {
      $this->names= new MigrationNameMapping();
      $this->names->addMapping('Date', 'util.Date');
      $this->names->setNamespaceSeparator('.');
    }

    /**
     * Tests special class names ("xp", "parent", "self") stay unqualified
     *
     */
    #[@test]
    public function specialClassesStayUnqualified() {
      foreach (array('xp', 'parent', 'self') as $short) {
        $this->assertEquals(
          $short, 
          $this->names->packagedNameOf($this->names->qualifiedNameOf($short))
        );
      }
    }

    /**
     * Tests fully qualified names don't get qualified twice
     *
     */
    #[@test]
    public function fullyQualifiedNames() {
      $this->assertEquals(
        'net.xp_framework.unittest.DemoTest', 
        $this->names->packagedNameOf('net.xp_framework.unittest.DemoTest')
      );
    }

    /**
     * Tests PHP5's builtin classes get prefixed with PHP
     *
     */
    #[@test]
    public function php5BuiltinClasses() {
      foreach (array('stdClass', 'Directory', 'Exception', 'ReflectionClass') as $builtin) {
        $this->assertEquals(
          'php.'.$builtin, 
          $this->names->packagedNameOf($this->names->qualifiedNameOf($builtin))
        );
      }
    }

    /**
     * Tests qualified & packaged name of date class
     *
     */
    #[@test]
    public function nameOfDateClass() {
      $this->assertEquals(
        'util.Date', 
        $this->names->packagedNameOf($this->names->qualifiedNameOf('Date'))
      );
    }

    /**
     * Tests qualified & packaged name of date class
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function unknownClass() {
      $this->names->qualifiedNameOf('@@NON_EXISTANT_CLASS@@');
    }
  }
?>
