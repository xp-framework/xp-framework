<?php namespace net\xp_framework\unittest\tests;
 
use lang\types\ArrayList;

/**
 * Test values annotation
 *
 * @see  xp://unittest.TestSuite
 * @see  https://github.com/xp-framework/xp-framework/issues/313
 * @see  https://github.com/xp-framework/xp-framework/issues/298
 */
class ValuesTest extends \unittest\TestCase {
  protected $suite= null;
    
  /**
   * Setup method. Creates a new test suite.
   */
  public function setUp() {
    $this->suite= new \unittest\TestSuite();
  }

  /**
   * Values for external_value_source tests
   *
   * @param  int lo
   * @param  int hi
   * @return var[]
   */
  public static function range($lo= 1, $hi= 3) {
    return range($lo, $hi);
  }

  #[@test]
  public function inline_value_source() {
    $test= newinstance('unittest.TestCase', array('fixture'), '{
      public $values= array();

      #[@test, @values(array(1, 2, 3))]
      public function fixture($value) {
        $this->values[]= $value;
      }
    }');
    $this->suite->runTest($test);
    $this->assertEquals(array(1, 2, 3), $test->values);
  }

  #[@test]
  public function local_value_source() {
    $test= newinstance('unittest.TestCase', array('fixture'), '{
      public $values= array();

      public function values() {
        return array(1, 2, 3);
      }

      #[@test, @values("values")]
      public function fixture($value) {
        $this->values[]= $value;
      }
    }');
    $this->suite->runTest($test);
    $this->assertEquals(array(1, 2, 3), $test->values);
  }

  #[@test]
  public function local_value_source_with_args() {
    $test= newinstance('unittest.TestCase', array('fixture'), '{
      public $values= array();

      public function range($lo= 1, $hi= 3) {
        return range($lo, $hi);
      }

      #[@test, @values(source= "range", args= array(1, 4))]
      public function fixture($value) {
        $this->values[]= $value;
      }
    }');
    $this->suite->runTest($test);
    $this->assertEquals(array(1, 2, 3, 4), $test->values);
  }

  #[@test]
  public function local_value_source_without_args() {
    $test= newinstance('unittest.TestCase', array('fixture'), '{
      public $values= array();

      public function range($lo= 1, $hi= 3) {
        return range($lo, $hi);
      }

      #[@test, @values(source= "range")]
      public function fixture($value) {
        $this->values[]= $value;
      }
    }');
    $this->suite->runTest($test);
    $this->assertEquals(array(1, 2, 3), $test->values);
  }

  #[@test]
  public function external_value_source_fully_qualified_class() {
    $test= newinstance('unittest.TestCase', array('fixture'), '{
      public $values= array();

      #[@test, @values("net.xp_framework.unittest.tests.ValuesTest::range")]
      public function fixture($value) {
        $this->values[]= $value;
      }
    }');
    $this->suite->runTest($test);
    $this->assertEquals(array(1, 2, 3), $test->values);
  }

  #[@test]
  public function external_value_source_unqualified_class() {
    $test= newinstance('unittest.TestCase', array('fixture'), '{
      public $values= array();

      #[@test, @values("net\\\\xp_framework\\\\unittest\\\\tests\\\\ValuesTest::range")]
      public function fixture($value) {
        $this->values[]= $value;
      }
    }');
    $this->suite->runTest($test);
    $this->assertEquals(array(1, 2, 3), $test->values);
  }

  #[@test]
  public function external_value_source_provider_and_args() {
    $test= newinstance('unittest.TestCase', array('fixture'), '{
      public $values= array();

      #[@test, @values(source= "net.xp_framework.unittest.tests.ValuesTest::range", args= array(1, 10))]
      public function fixture($value) {
        $this->values[]= $value;
      }
    }');
    $this->suite->runTest($test);
    $this->assertEquals(array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10), $test->values);
  }

  #[@test]
  public function local_value_source_with_self() {
    $test= newinstance('unittest.TestCase', array('fixture'), '{
      public $values= array();

      public static function range() {
        return array(1, 2, 3);
      }

      #[@test, @values("self::range")]
      public function fixture($value) {
        $this->values[]= $value;
      }
    }');
    $this->suite->runTest($test);
    $this->assertEquals(array(1, 2, 3), $test->values);
  }

  #[@test]
  public function all_variants_succeed() {
    $test= newinstance('unittest.TestCase', array('fixture'), '{
      #[@test, @values(array(1, 2, 3))]
      public function fixture($value) {
        $this->assertTrue(TRUE);
      }
    }');
    $r= $this->suite->runTest($test);
    $this->assertEquals(3, $r->successCount());
  }

  #[@test]
  public function all_variants_fail() {
    $test= newinstance('unittest.TestCase', array('fixture'), '{
      #[@test, @values(array(1, 2, 3))]
      public function fixture($value) {
        $this->assertTrue(FALSE);
      }
    }');
    $r= $this->suite->runTest($test);
    $this->assertEquals(3, $r->failureCount());
  }

  #[@test]
  public function all_variants_skipped() {
    $test= newinstance('unittest.TestCase', array('fixture'), '{
      public function setUp() {
        throw new PrerequisitesNotMetError("Not ready yet");
      }

      #[@test, @values(array(1, 2, 3))]
      public function fixture($value) {
        throw new Error("Will never be reached");
      }
    }');
    $r= $this->suite->runTest($test);
    $this->assertEquals(3, $r->skipCount());
  }

  #[@test]
  public function some_variants_succeed_some_fail() {
    $test= newinstance('unittest.TestCase', array('fixture'), '{
      #[@test, @values(array(1, 2, 3))]
      public function fixture($value) {
        $this->assertEquals(0, $value % 2);
      }
    }');
    $r= $this->suite->runTest($test);
    $this->assertEquals(1, $r->successCount());
    $this->assertEquals(2, $r->failureCount());
  }

  #[@test]
  public function supplying_values_for_multiple_parameters() {
    $test= newinstance('unittest.TestCase', array('fixture'), '{
      public $values= array();

      #[@test, @values(array(array(1, 2), array(3, 4), array(5, 6)))]
      public function fixture($a, $b) {
        $this->values[]= $a;
        $this->values[]= $b;
      }
    }');
    $this->suite->runTest($test);
    $this->assertEquals(array(1, 2, 3, 4, 5, 6), $test->values);
  }

  #[@test]
  public function using_traversable_in_values() {
    $test= newinstance('unittest.TestCase', array('fixture'), '{
      public $values= array();

      public function values() {
        return new ArrayList(1, 2, 3);
      }

      #[@test, @values("values")]
      public function fixture($value) {
        $this->values[]= $value;
      }
    }');
    $this->suite->runTest($test);
    $this->assertEquals(array(1, 2, 3), $test->values);
  }

  #[@test]
  public function using_this_in_value_provider() {
    $test= newinstance('unittest.TestCase', array('fixture'), '{
      public $values= array();

      public function values() {
        return array($this);
      }

      #[@test, @values("values")]
      public function fixture($value) {
        $this->values[]= $value;
      }
    }');
    $this->suite->runTest($test);
    $this->assertEquals(array($test), $test->values);
  }

  #[@test]
  public function protected_local_values_method() {
    $test= newinstance('unittest.TestCase', array('fixture'), '{
      public $values= array();

      protected function values() {
        return array(1, 2, 3);
      }

      #[@test, @values("values")]
      public function fixture($value) {
        $this->values[]= $value;
      }
    }');
    $this->suite->runTest($test);
    $this->assertEquals(array(1, 2, 3), $test->values);
  }

  #[@test]
  public function private_local_values_method() {
    $test= newinstance('unittest.TestCase', array('fixture'), '{
      public $values= array();

      private function values() {
        return array(1, 2, 3);
      }

      #[@test, @values("values")]
      public function fixture($value) {
        $this->values[]= $value;
      }
    }');
    $this->suite->runTest($test);
    $this->assertEquals(array(1, 2, 3), $test->values);
  }

  #[@test]
  public function values_with_expect() {
    $test= newinstance('unittest.TestCase', array('not_at_number'), '{
      #[@test, @values(array("a")), @expect("lang.FormatException")]
      public function not_at_number($value) {
        throw new FormatException("Not a number: ".$value);
      }
    }');
    $r= $this->suite->runTest($test);
    $this->assertEquals(1, $r->successCount());
  }
}
