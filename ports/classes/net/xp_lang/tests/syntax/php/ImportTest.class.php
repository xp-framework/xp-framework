<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_lang.tests.syntax.php';

  uses(
    'unittest.TestCase',
    'xp.compiler.syntax.php.Lexer',
    'xp.compiler.syntax.php.Parser'
  );

  /**
   * TestCase
   *
   */
  class net·xp_lang·tests·syntax·php·ImportTest extends TestCase {
  
    /**
     * Parse method source and return statements inside this method.
     *
     * @param   string src
     * @return  xp.compiler.Node
     */
    protected function parse($src) {
      return create(new xp·compiler·syntax·xp·Parser())->parse(new xp·compiler·syntax·xp·Lexer($src, '<string:'.$this->name.'>'))->imports;
    }

    /**
     * Test single-type import
     *
     */
    #[@test]
    public function singleTypeImport() {
      $this->assertEquals(array(new ImportNode(array(
          'name'     => 'util.collections.HashTable'
        ))), 
        $this->parse('import util.collections.HashTable; public class net·xp_lang·tests·syntax·php·Test { }')
      );
    }

    /**
     * Test type-import-on-demand
     *
     */
    #[@test]
    public function typeImportOnDemand() {
      $this->assertEquals(array(new ImportNode(array(
          'name'     => 'util.collections.*'
        ))), 
        $this->parse('import util.collections.*; public class net·xp_lang·tests·syntax·php·Test { }')
      );
    }

    /**
     * Test static import
     *
     */
    #[@test]
    public function staticImport() {
      $this->assertEquals(array(new StaticImportNode(array(
          'name'     => 'rdbms.criterion.Restrictions.*'
        ))), 
        $this->parse('import static rdbms.criterion.Restrictions.*; public class net·xp_lang·tests·syntax·php·Test { }')
      );
    }

    /**
     * Test native import
     *
     */
    #[@test]
    public function nativeImport() {
      $this->assertEquals(array(new NativeImportNode(array(
          'name'     => 'standard.*'
        ))), 
        $this->parse('import native standard.*; public class net·xp_lang·tests·syntax·php·Test { }')
      );
    }

    /**
     * Test multiple imports
     *
     */
    #[@test]
    public function multipleImports() {
      $this->assertEquals(array(new ImportNode(array(
          'name'     => 'util.collections.*'
        )), new ImportNode(array(
          'name'     => 'util.Date'
        )), new ImportNode(array(
          'name'     => 'unittest.*'
        ))), 
        $this->parse('
          import util.collections.*; 
          import util.Date; 
          import unittest.*; 

          public class net·xp_lang·tests·syntax·php·Test { }
        ')
      );
    }

    /**
     * Test "import *" is not valid
     *
     */
    #[@test, @expect('text.parser.generic.ParseException')]
    public function noImportAll() {
      $this->parse('import *; public class net·xp_lang·tests·syntax·php·Test { }');
    }

    /**
     * Test "import test" is not valid
     *
     */
    #[@test, @expect('text.parser.generic.ParseException')]
    public function noImportNothing() {
      $this->parse('import test; public class net·xp_lang·tests·syntax·php·Test { }');
    }
  }
?>
