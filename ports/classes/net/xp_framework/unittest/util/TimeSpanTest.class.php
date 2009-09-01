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
   * @see      xp://util.TimeSpan
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
     * Create new TimeSpan
     *
     */
    #[@test]
    public function newNegativeTimeSpan() {
      $this->assertEquals('0d, 0h, 0m, 1s', create(new TimeSpan(-1))->toString());
    }

    /**
     * Test wrong arguments for constructor
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function wrongArguments() {
      new TimeSpan('2 days');
    }

    /**
     * Test TimeSpan::add()
     *
     */
    #[@test]
    public function add() {
      $this->assertEquals('0d, 2h, 1m, 5s', create(new TimeSpan(3600))
        ->add(new TimeSpan(3600), new TimeSpan(60))
        ->add(new TimeSpan(5))->toString()
      );
    }
      
    /**
     * Test TimeSpan::subtract()
     *
     */
    #[@test]
    public function subtract() {
      $this->assertEquals('0d, 22h, 58m, 55s', create(new TimeSpan(86400))
        ->substract(new TimeSpan(3600), new TimeSpan(60))
        ->substract(new TimeSpan(5))->toString()
      );
    }

    /**
     * Test TimeSpan::subtract()
     *
     */
    #[@test]
    public function subtractToZero() {
      $this->assertEquals(
        '0d, 0h, 0m, 0s', 
        create(new TimeSpan(6100))->substract(new TimeSpan(6100))->toString()
      );
    }

    /**
     * Test TimeSpan::subtract()
     *
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function subtractToNegative() {
      create(new TimeSpan(0))->substract(new TimeSpan(1));
    }

    /**
     * Test TimeSpan::add() and TimeSpan::subtract()
     *
     */
    #[@test]
    public function addAndSubstract() {
      $this->assertEquals('1d, 1h, 0m, 55s', create(new TimeSpan(86400))
        ->add(new TimeSpan(3600), new TimeSpan(60))
        ->substract(new TimeSpan(5))->toString()
      );
    }

    /**
     * Test wrong arguments for TimeSpan::add()
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function addWrongArguments() {
      create(new TimeSpan(0))->add('2 days');
    }

    /**
     * Test static creation
     *
     */
    #[@test]
    public function fromSeconds() {
      $this->assertEquals('0d, 1h, 0m, 0s', TimeSpan::seconds(3600)->toString());
    }

    /**
     * Test static creation
     *
     */
    #[@test]
    public function fromMinutes() {
      $this->assertEquals('0d, 2h, 7m, 0s', TimeSpan::minutes(127)->toString());
    }
    
    /**
     * Test static creation
     *
     */
    #[@test]
    public function fromHours() {
      $this->assertEquals('1d, 3h, 0m, 0s', TimeSpan::hours(27)->toString());
    }

    /**
     * Test static creation
     *
     */
    #[@test]
    public function fromDays() {
      $this->assertEquals('40d, 0h, 0m, 0s', TimeSpan::days(40)->toString());
    }
    
    /**
     * Test static creation
     *
     */
    #[@test]
    public function fromWeeks() {
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

    /**
     * Test format() method
     *
     */
    #[@test]
    public function formatSeconds() {
      $this->assertEquals('91865', create(new TimeSpan(91865))->format('%s'));
    }

    /**
     * Test format() method
     *
     */
    #[@test]
    public function formatWholeSeconds() {
      $this->assertEquals('5', create(new TimeSpan(91865))->format('%w'));
    }

    /**
     * Test format() method
     *
     */
    #[@test]
    public function formatMinutes() {
      $this->assertEquals('1531', create(new TimeSpan(91865))->format('%m'));
    }

    /**
     * Test format() method
     *
     */
    #[@test]
    public function formatFloatMinutes() {
      $this->assertEquals('1531.08', create(new TimeSpan(91865))->format('%M'));
    }

    /**
     * Test format() method
     *
     */
    #[@test]
    public function formatWholeMinutes() {
      $this->assertEquals('31', create(new TimeSpan(91865))->format('%j'));
    }

    /**
     * Test format() method
     *
     */
    #[@test]
    public function formatHours() {
      $this->assertEquals('25', create(new TimeSpan(91865))->format('%h'));
    }

    /**
     * Test format() method
     *
     */
    #[@test]
    public function formatFloatHours() {
      $this->assertEquals('25.52', create(new TimeSpan(91865))->format('%H'));
    }

    /**
     * Test format() method
     *
     */
    #[@test]
    public function formatWholeHours() {
      $this->assertEquals('1', create(new TimeSpan(91865))->format('%y'));
    }

    /**
     * Test format() method
     *
     */
    #[@test]
    public function formatDays() {
      $this->assertEquals('1', create(new TimeSpan(91865))->format('%d'));
    }

    /**
     * Test format() method
     *
     */
    #[@test]
    public function formatFloatDays() {
      $this->assertEquals('1.06', create(new TimeSpan(91865))->format('%D'));
    }

    /**
     * Test format() method
     *
     */
    #[@test]
    public function formatWholeDays() {
      $this->assertEquals('1', create(new TimeSpan(91865))->format('%e'));
    }

    /**
     * Test format() method
     *
     */
    #[@test]
    public function format() {
      $this->assertEquals('1d1h', create(new TimeSpan(91865))->format('%ed%yh'));
    }

    /**
     * Test format() method
     *
     */
    #[@test]
    public function formatPercent() {
      $this->assertEquals('%1d%1h%', create(new TimeSpan(91865))->format('%%%ed%%%yh%%'));
    }
  }
?>
