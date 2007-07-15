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
     * 
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
  }
?>
