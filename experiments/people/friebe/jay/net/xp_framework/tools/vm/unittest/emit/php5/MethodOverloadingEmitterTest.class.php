<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('net.xp_framework.tools.vm.unittest.emit.php5.AbstractEmitterTest');

  /**
   * Tests PHP5 emitter
   *
   * @see      xp://net.xp_framework.tools.vm.unittest.emit.php5.MethodEmitterTest
   * @purpose  Unit Test
   */
  class MethodOverloadingEmitterTest extends AbstractEmitterTest {

    /**
     * Tests overloading the constructor
     *
     */
    #[@test]
    public function overloadedMethod() {
      $this->assertSourcecodeEquals(
        preg_replace('/\n\s*/', '', 'class main·Date extends lang·Object{
          protected $stamp= 0;

          public function isBeforestring($value){
            return $this->stamp<strtotime($value); 
          }

          public function isBeforemain·Date($value){
            return $this->stamp<$value->stamp; 
          }

          public static function main(){
            $d= new main·Date(); 
            var_dump($d->isBeforemain·Date(new main·Date(1))); 
            var_dump($d->isBeforestring(\'1977-12-14\')); 
          }
        };'),
        $this->emit('class Date {
          protected int $stamp= 0;
          
          [@overloaded] public bool isBefore(string $value) {
            return $this->stamp < strtotime($value);
          }

          [@overloaded] public bool isBefore(Date $value) {
            return $this->stamp < $value->stamp;
          }

          public static void main() {
            $d= new Date();
            var_dump($d->isBefore(new Date(1)));
            var_dump($d->isBefore("1977-12-14"));
          }
        }')
      );
    }

    /**
     * Tests overloading the constructor
     *
     */
    #[@test]
    public function overloadedConstructor() {
      $this->assertSourcecodeEquals(
        preg_replace('/\n\s*/', '', 'class main·Date extends lang·Object{
          protected $stamp= 0;

          public function __constructstring($in){
            $this->stamp= strtotime($in); 
          }

          public function __constructint($in){
            $this->stamp= $in; 
          }

          public static function main(){
            xp::spawn(\'main·Date\', \'__constructint\', array(1)); 
            xp::spawn(\'main·Date\', \'__constructstring\', array(\'1977-12-14\')); 
          }
        };'),
        $this->emit('class main·Date {
          protected int $stamp= 0;

          [@overloaded] public __construct(string $in) {
            $this->stamp= strtotime($in);
          }

          [@overloaded] public __construct(int $in) {
            $this->stamp= $in;
          }
          
          public static void main() {
            new Date(1);
            new Date("1977-12-14");
          }
        }')
      );
    }
  }
?>
