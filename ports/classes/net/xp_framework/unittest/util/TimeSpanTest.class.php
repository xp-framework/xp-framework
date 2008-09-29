<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'util.TimeSpan'
  );

  /**
   * TestCase
   *
   * @see      reference
   * @purpose  purpose
   */
  class TimeSpanTest extends TestCase {
    
    /**
     * Create new TimeSpan
     *
     */
    #[@test]
    public function newTimeSpan() {
      $this->assertEquals('0d, 2h, 1m, 5s', create(new TimeSpan(7265))->toString());
    }

    /**
     * Test wrong arguments for constructor
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function wrongArguments() {
      $t= new TimeSpan('2 days');
    }

    /**
     * Test TimeSpan::add()
     *
     */
    #[@test]
    public function addAndSubstract() {
      $this->assertEquals('0d, 2h, 1m, 5s', create(new TimeSpan(3600))
        ->add(new TimeSpan(3600), new TimeSpan(60))
        ->add(new TimeSpan(5))->toString(),
        'add'
      );
      
      $this->assertEquals('0d, 22h, 58m, 55s', create(new TimeSpan(86400))
        ->substract(new TimeSpan(3600), new TimeSpan(60))
        ->substract(new TimeSpan(5))->toString(),
        'substract'
      );

      $this->assertEquals('1d, 1h, 0m, 55s', create(new TimeSpan(86400))
        ->add(new TimeSpan(3600), new TimeSpan(60))
        ->substract(new TimeSpan(5))->toString(),
        'mixed'
      );
    }

    /**
     * Test wrong arguments for TimeSpan::add()
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function addWrongArguments() {
      $t= create(new TimeSpan(0))->add('2 days');
    }

    /**
     * Test static creation
     *
     */
    #[@test]
    public function staticCreation() {
      $this->assertEquals('0d, 1h, 0m, 0s', TimeSpan::seconds(3600)->toString());
      $this->assertEquals('0d, 2h, 7m, 0s', TimeSpan::minutes(127)->toString());
      $this->assertEquals('1d, 3h, 0m, 0s', TimeSpan::hours(27)->toString());
      $this->assertEquals('40d, 0h, 0m, 0s', TimeSpan::days(40)->toString());
      $this->assertEquals('7d, 0h, 0m, 0s', TimeSpan::weeks(1)->toString());
    }

    /**
     * Test whole values
     *
     */
    #[@test]
    public function wholeValues() {
      $t= new TimeSpan(91865);
      $this->assertEquals(5, $t->getWholeSeconds(), 'wholeSeconds');
      $this->assertEquals(31, $t->getWholeMinutes(), 'wholeMinutes');
      $this->assertEquals(1, $t->getWholeHours(), 'wholeHours');
      $this->assertEquals(1, $t->getWholeDays(), 'wholeDays');
    }
  }
?>
