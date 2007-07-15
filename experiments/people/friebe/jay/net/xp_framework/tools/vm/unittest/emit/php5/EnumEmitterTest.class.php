<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('net.xp_framework.tools.vm.unittest.emit.php5.AbstractEmitterTest');

  /**
   * Tests PHP5 emitter
   *
   * @purpose  Unit Test
   */
  class EnumEmitterTest extends AbstractEmitterTest {

    /**
     * Tests basic enum 
     *
     */
    #[@test]
    public function dayEnum() {
      $this->assertSourcecodeEquals('
        uses(\'lang.Enum\'); 

        class main·Day extends lang·Enum {
          public static
            $monday,  
            $tuesday,
            $wednesday,
            $thursday,
            $friday,
            $saturday,
            $sunday;

          static function __static() {
            main·Day::$monday= new main·Day(0, \'monday\');
            main·Day::$tuesday= new main·Day(1, \'tuesday\');
            main·Day::$wednesday= new main·Day(2, \'wednesday\');
            main·Day::$thursday= new main·Day(3, \'thursday\');
            main·Day::$friday= new main·Day(4, \'friday\');
            main·Day::$saturday= new main·Day(5, \'saturday\');
            main·Day::$sunday= new main·Day(6, \'sunday\');
          }
          
          public static function values() { 
            return array(
              main·Day::$monday,
              main·Day::$tuesday,
              main·Day::$wednesday,
              main·Day::$thursday,
              main·Day::$friday,
              main·Day::$saturday,
              main·Day::$sunday,
            ); 
          }
        };',
        $this->emit('enum Day { 
          monday,  
          tuesday,
          wednesday,
          thursday,
          friday,
          saturday,
          sunday
        }')
      );
    }

    /**
     * Tests basic enum with assigned ordinal values
     *
     */
    #[@test]
    public function coinEnum() {
      $this->assertSourcecodeEquals('
        uses(\'lang.Enum\'); 

        class main·Coin extends lang·Enum {
          public static $penny, $nickel, $dime, $quarter;
          
          static function __static() {
            main·Coin::$penny= new main·Coin(1, \'penny\');
            main·Coin::$nickel= new main·Coin(2, \'nickel\');
            main·Coin::$dime= new main·Coin(10, \'dime\');
            main·Coin::$quarter= new main·Coin(25, \'quarter\');
          }
          
          public static function values() { 
            return array(
              main·Coin::$penny, 
              main·Coin::$nickel, 
              main·Coin::$dime, 
              main·Coin::$quarter,
            ); 
          }
        };',
        $this->emit('enum Coin { penny(1), nickel(2), dime(10), quarter(25) }')
      );
    }

    /**
     * Tests basic enum with assigned ordinal values
     *
     */
    #[@test]
    public function enumWithMethod() {
      $this->assertSourcecodeEquals('
        uses(\'lang.Enum\'); 

        class main·Coin extends lang·Enum {
          public static $penny, $nickel, $dime, $quarter;
          
          static function __static() {
            main·Coin::$penny= new main·Coin(1, \'penny\');
            main·Coin::$nickel= new main·Coin(2, \'nickel\');
            main·Coin::$dime= new main·Coin(10, \'dime\');
            main·Coin::$quarter= new main·Coin(25, \'quarter\');
          }
          
          public static function values() { 
            return array(
              main·Coin::$penny, 
              main·Coin::$nickel, 
              main·Coin::$dime, 
              main·Coin::$quarter,
            ); 
          }
          
          /**
           * @return  int
           */
          public function value() {
            return $this->ordinal;
          }
        };',
        $this->emit('enum Coin { 
          penny(1), nickel(2), dime(10), quarter(25);
          
          public int value() {
            return $this->ordinal;
          }
        }')
      );
    }
  }
?>
