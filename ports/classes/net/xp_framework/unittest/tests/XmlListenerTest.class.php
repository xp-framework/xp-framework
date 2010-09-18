<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'unittest.TestSuite',
    'unittest.XmlTestListener',
    'io.streams.MemoryOutputStream',
    'io.streams.StringWriter',
    'xml.Tree',
    'net.xp_framework.unittest.tests.SimpleTestCase'
  );

  /**
   * TestCase for the XML test listener implementation
   *
   * @see      xp://unittest.XmlTestListener
   */
  class XmlListenerTest extends TestCase {
    protected $out= NULL;
    protected $suite= NULL;
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->out= new MemoryOutputStream();
      $this->suite= new TestSuite();
      $this->suite->addListener(new XmlTestListener(new StringWriter($this->out)));
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function outputInitiallyEmpty() {
      $this->assertEquals('', $this->out->getBytes());
    }
    
    /**
     * Run tests and return XML generated
     *
     * @param   unittest.TestCase... tests
     * @return  xml.Tree the output
     * @throws  unittest.AssertionFailedError in case the generated XML is not well-formed
     */
    protected function runTests() {
      foreach (func_get_args() as $test) {
        $this->suite->addTest($test);
      }
      $this->suite->run();
      
      try {
        return Tree::fromString($this->out->getBytes());
      } catch (XMLFormatException $e) {
        $this->fail('XML generated is not well-formed: '.$e->getMessage(), $this->out->getBytes(), NULL);
      }
    }
    
    /**
     * Assertion helper testing for a testsuite node
     *
     * @param   string name
     * @param   [:string] attr
     * @param   xml.Node n
     * @throws  unittest.AssertionFailedError
     */
    protected function assertSuiteNode($name, $attr, $suite) {
      $this->assertClass($suite, 'xml.Node');
      $this->assertEquals('testsuite', $suite->getName());
      $this->assertEquals($name, $suite->getAttribute('name'));
      
      foreach ($attr as $key => $value) {
        $this->assertEquals($value, $suite->getAttribute($key), 'Attribute "'.$key.'"');
      }
    }

    /**
     * Assertion helper testing for a testcase node
     *
     * @param   [:string] attr
     * @param   xml.Node n
     * @throws  unittest.AssertionFailedError
     */
    protected function assertCaseNode($attr, $suite) {
      $this->assertClass($suite, 'xml.Node');
      $this->assertEquals('testcase', $suite->getName());
      
      foreach ($attr as $key => $value) {
        $this->assertEquals($value, $suite->getAttribute($key), 'Attribute "'.$key.'"');
      }
    }

    /**
     * Test running a succeeding test
     *
     */
    #[@test]
    public function successfulTest() {
      $t= $this->runTests(new SimpleTestCase('succeeds'));

      $this->assertEquals('testsuites', $t->root->getName());
      with ($suite= @$t->root->children[0]); {
        $this->assertSuiteNode(
          'net.xp_framework.unittest.tests.SimpleTestCase',
          array('tests' => '1', 'errors' => '0', 'failures' => '0', 'skipped' => '0'),
          $suite
        );
        
        with ($case= @$suite->children[0]); {
          $this->assertCaseNode(array('name' => 'succeeds'), $case);
          $this->assertNotEquals(NULL, $case->getAttribute('time'));
        }
      }
    }

    /**
     * Test running a skipped test
     *
     */
    #[@test]
    public function skippedTest() {
      $t= $this->runTests(new SimpleTestCase('skipped'));

      $this->assertEquals('testsuites', $t->root->getName());
      with ($suite= @$t->root->children[0]); {
        $this->assertSuiteNode(
          'net.xp_framework.unittest.tests.SimpleTestCase',
          array('tests' => '1', 'errors' => '0', 'failures' => '0', 'skipped' => '1'),
          $suite
        );
        
        with ($case= @$suite->children[0]); {
          $this->assertCaseNode(array('name' => 'skipped'), $case);
          $this->assertNotEquals(NULL, $case->getAttribute('time'));
        }
      }
    }

    /**
     * Test running a failing test
     *
     */
    #[@test]
    public function failingTest() {
      $t= $this->runTests(new SimpleTestCase('fails'));

      $this->assertEquals('testsuites', $t->root->getName());
      with ($suite= @$t->root->children[0]); {
        $this->assertSuiteNode(
          'net.xp_framework.unittest.tests.SimpleTestCase',
          array('tests' => '1', 'errors' => '0', 'failures' => '1', 'skipped' => '0'),
          $suite
        );

        with ($case= @$suite->children[0]); {
          $this->assertCaseNode(array('name' => 'fails'), $case);
          $this->assertNotEquals(NULL, $case->getAttribute('time'));

          with ($failure= @$case->children[0]); {
            $this->assertClass($failure, 'xml.Node');
            $this->assertEquals('failure', $failure->getName());
            $this->assertNotEquals(NULL, $failure->getAttribute('message'));
            $this->assertNotEquals(NULL, $failure->getContent());
          }
        }
      }
    }

    /**
     * Test running a failing test
     *
     */
    #[@test]
    public function errorTest() {
      $t= $this->runTests(new SimpleTestCase('throws'));

      $this->assertEquals('testsuites', $t->root->getName());
      with ($suite= @$t->root->children[0]); {
        $this->assertSuiteNode(
          'net.xp_framework.unittest.tests.SimpleTestCase',
          array('tests' => '1', 'errors' => '1', 'failures' => '0', 'skipped' => '0'),
          $suite
        );

        with ($case= @$suite->children[0]); {
          $this->assertCaseNode(array('name' => 'throws'), $case);
          $this->assertNotEquals(NULL, $case->getAttribute('time'));

          with ($failure= @$case->children[0]); {
            $this->assertClass($failure, 'xml.Node');
            $this->assertEquals('error', $failure->getName());
            $this->assertNotEquals(NULL, $failure->getAttribute('message'));
            $this->assertNotEquals(NULL, $failure->getContent());
          }
        }
      }
    }

    /**
     * Test running a failing test
     *
     */
    #[@test]
    public function warningTest() {
      $t= $this->runTests(new SimpleTestCase('raisesAnError'));

      $this->assertEquals('testsuites', $t->root->getName());
      with ($suite= @$t->root->children[0]); {
        $this->assertSuiteNode(
          'net.xp_framework.unittest.tests.SimpleTestCase',
          array('tests' => '1', 'errors' => '1', 'failures' => '0', 'skipped' => '0'),
          $suite
        );

        with ($case= @$suite->children[0]); {
          $this->assertCaseNode(array('name' => 'raisesAnError'), $case);
          $this->assertNotEquals(NULL, $case->getAttribute('time'));

          with ($failure= @$case->children[0]); {
            $this->assertClass($failure, 'xml.Node');
            $this->assertEquals('error', $failure->getName());
            $this->assertNotEquals(NULL, $failure->getAttribute('message'));
            $this->assertNotEquals(NULL, $failure->getContent());
          }
        }
      }
    }

    /**
     * Test running multiple tests
     *
     */
    #[@test]
    public function multipleTests() {
      $t= $this->runTests(new SimpleTestCase('succeeds'), new SimpleTestCase('fails'));

      $this->assertEquals('testsuites', $t->root->getName());
      with ($suite= @$t->root->children[0]); {
        $this->assertSuiteNode(
          'net.xp_framework.unittest.tests.SimpleTestCase',
          array('tests' => '2', 'errors' => '0', 'failures' => '1', 'skipped' => '0'),
          $suite
        );
        $this->assertEquals(2, sizeof($suite->children));
        $this->assertCaseNode(array('name' => 'succeeds'), $suite->children[0]);
        $this->assertCaseNode(array('name' => 'fails'), $suite->children[1]);
      }
    }
  }
?>
