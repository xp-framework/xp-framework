<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_lang.tests.execution.source';

  uses(
    'net.xp_lang.tests.execution.source.ExecutionTest',
    'xp.compiler.checks.UninitializedVariables'
  );

  /**
   * Tests properties
   *
   */
  class net·xp_lang·tests·execution·source·PropertiesOverloadingTest extends ExecutionTest {
    protected static $base= NULL;
    protected static $child= NULL;

    /**
     * Sets up test case
     *
     */
    public function setUp() {
      parent::setUp();
      $this->check(new UninitializedVariables(), TRUE);
      if (NULL !== self::$base) return;

      try {
        self::$base= $this->define('class', 'TimePeriod', NULL, '{
          protected int $seconds = 0;
          
          public __construct(int $initial= 0) {
            $this.seconds= $initial;
          }

          public double hours {
            get { return $this.seconds / 3600; }
            set { $this.seconds= 3600 * $value; }
          }

          public double minutes {
            get { return $this.seconds / 60; }
            set { $this.seconds= $value * 60; }
          }

          public int seconds {
            get { return $this.seconds; }
            set { $this.seconds= $value; }
          }
        }');
        self::$child= $this->define('class', 'WholeTimePeriod', self::$base, '{
          public int hours {
            get { return floor($this.seconds / 3600) as int; }
          }

          public int minutes {
            get { return floor($this.seconds / 60) as int; }
            set { $this.seconds= $value * 60; }
          }
        }', array('import native standard.floor;'));
      } catch (Throwable $e) {
        self::$base= self::$child= NULL;
        throw new PrerequisitesNotMetError($e->getMessage(), $e);
      }
    }
    
    /**
     * Test base class
     *
     */
    #[@test]
    public function readHoursBase() {
      $period= self::$base->newInstance(3700);
      $this->assertEquals(3700 / 3600, $period->hours);
    }

    /**
     * Test base class
     *
     */
    #[@test]
    public function writeHoursBase() {
      $period= self::$base->newInstance();
      $period->hours= 3700 / 3600;
      $this->assertEquals(3700 / 3600, $period->hours);
    }

    /**
     * Test child class
     *
     */
    #[@test]
    public function readHoursChild() {
      $period= self::$child->newInstance(3700);
      $this->assertEquals(1, $period->hours);
    }

    /**
     * Test child class
     *
     */
    #[@test]
    public function writeHoursChild() {
      $period= self::$child->newInstance();
      $period->hours= 3700 / 3600;
      $this->assertEquals(1, $period->hours);
    }

    /**
     * Test base class
     *
     */
    #[@test]
    public function readMinutesBase() {
      $period= self::$base->newInstance(3700);
      $this->assertEquals(3700 / 60, $period->minutes);
    }

    /**
     * Test base class
     *
     */
    #[@test]
    public function writeMinutesBase() {
      $period= self::$base->newInstance();
      $period->minutes= 3700 / 60;
      $this->assertEquals(3700 / 60, $period->minutes);
    }

    /**
     * Test child class
     *
     */
    #[@test]
    public function readMinutesChild() {
      $period= self::$child->newInstance(3700);
      $this->assertEquals(61, $period->minutes);
    }

    /**
     * Test child class
     *
     */
    #[@test]
    public function writeMinutesChild() {
      $period= self::$child->newInstance();
      $period->minutes= 3700 / 60;
      $this->assertEquals(61, $period->minutes);
    }

    /**
     * Test base class
     *
     */
    #[@test]
    public function readSecondsBase() {
      $period= self::$base->newInstance(3700);
      $this->assertEquals(3700, $period->seconds);
    }

    /**
     * Test child class
     *
     */
    #[@test]
    public function readSecondsChild() {
      $period= self::$child->newInstance(3700);
      $this->assertEquals(3700, $period->seconds);
    }
  }
?>
