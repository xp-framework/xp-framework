<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_lang.tests.execution.source';

  uses('net.xp_lang.tests.execution.source.ExecutionTest', 'lang.Enum');

  /**
   * Tests class declarations
   *
   */
  class net·xp_lang·tests·execution·source·EnumDeclarationTest extends ExecutionTest {

    /**
     * Test declaring an enum
     *
     */
    #[@test]
    public function weekdayEnum() {
      $class= $this->define('enum', 'WeekDay', NULL, '{
        MON, TUE, WED, THU, FRI, SAT, SUN;
        
        public bool isWeekend() {
          return $this.ordinal > self::$FRI.ordinal;
        }
      }');
      $this->assertEquals('SourceWeekDay', $class->getName());
      $this->assertTrue($class->isEnum());
      
      with ($method= $class->getMethod('isWeekend')); {
        $this->assertEquals('isWeekend', $method->getName());
        $this->assertEquals(MODIFIER_PUBLIC, $method->getModifiers());
        $this->assertEquals(Primitive::$BOOLEAN, $method->getReturnType());
        $this->assertEquals(0, $method->numParameters());
      }

      $this->assertEquals('WED', Enum::valueOf($class, 'WED')->name());
      $this->assertEquals('SAT', Enum::valueOf($class, 'SAT')->name());
      $this->assertTrue(Enum::valueOf($class, 'SUN')->isWeekend());
      $this->assertFalse(Enum::valueOf($class, 'MON')->isWeekend());
    }

    /**
     * Test declaring an enum
     *
     */
    #[@test]
    public function coinEnum() {
      $class= $this->define('enum', 'Coin', NULL, '{
        penny(1), nickel(2), dime(10), quarter(25);

        public int value() {
          return $this.ordinal;
        }

        public string color() {
          switch ($this) {
            case self::$penny: return "copper";
            case self::$nickel: return "nickel";
            case self::$dime: case self::$quarter: return "silver";
          }
        }
      }');
      $this->assertEquals('SourceCoin', $class->getName());
      $this->assertTrue($class->isEnum());
      
      // Test values
      foreach (array(
        array('penny', 1, 'copper'),
        array('nickel', 2, 'nickel'),
        array('dime', 10, 'silver'),
        array('quarter', 25, 'silver')
      ) as $values) {
        $coin= Enum::valueOf($class, $values[0]);
        $this->assertEquals($values[0], $coin->name(), $values[0]);
        $this->assertEquals($values[1], $coin->value(), $values[0]);
        $this->assertEquals($values[2], $coin->color(), $values[0]);
      }
    }

    /**
     * Test declaring an enum
     *
     */
    #[@test]
    public function operationEnum() {
      $class= $this->define('abstract enum', 'Operation', NULL, '{
        plus {
          public int evaluate(int $x, int $y) { return $x + $y; }
        },
        minus {
          public int evaluate(int $x, int $y) { return $x - $y; }
        };
        
        public abstract int evaluate(int $x, int $y);
      }');
      $this->assertEquals('SourceOperation', $class->getName());
      $this->assertTrue($class->isEnum());

      $plus= Enum::valueOf($class, 'plus');
      $this->assertEquals(2, $plus->evaluate(1, 1));

      $minus= Enum::valueOf($class, 'minus');
      $this->assertEquals(0, $minus->evaluate(1, 1));
    }

    /**
     * Test declaring an enum
     *
     */
    #[@test]
    public function partialOperationEnum() {
      $class= $this->define('abstract enum', 'PartialOperation', NULL, '{
        plus {
          public int evaluate(int $x, int $y) { return $x + $y; }
        };
        
        public abstract int evaluate(int $x, int $y);
      }');
      $this->assertEquals('SourcePartialOperation', $class->getName());
      $this->assertTrue($class->isEnum());

      $plus= Enum::valueOf($class, 'plus');
      $this->assertEquals(2, $plus->evaluate(1, 1));
    }

    /**
     * Test declaring an enum
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function brokenOperationEnum() {
      $this->define('enum', 'BrokenOperation', NULL, '{
        plus {
          public int evaluate(int $x, int $y) { return $x + $y; }
        };

        public abstract int evaluate(int $x, int $y);
      }');
    }
  }
?>
