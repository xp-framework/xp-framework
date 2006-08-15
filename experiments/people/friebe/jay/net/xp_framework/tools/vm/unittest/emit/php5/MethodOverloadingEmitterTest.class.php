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
     * @access  public
     */
    #[@test]
    function overloadedMethod() {
      $this->assertSourcecodeEquals(
        preg_replace('/\n\s*/', '', 'class Date extends xp·lang·Object{
          protected $stamp;

          public function isBeforestring($value){
            return $this->stamp<strtotime($value); 
          }

          public function isBeforeDate($value){
            return $this->stamp<$value->stamp; 
          }

          public static function main(){
            $d= new Date(); 
            var_dump($d->isBeforeDate(new Date(1))); 
            var_dump($d->isBeforestring(\'1977-12-14\')); 
          }
        };'),
        $this->emit('class Date {
          protected integer $stamp= 0;
          
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
     * @access  public
     */
    #[@test]
    function overloadedConstructor() {
      $this->assertSourcecodeEquals(
        preg_replace('/\n\s*/', '', 'class Date extends xp·lang·Object{
          protected $stamp;

          public function __constructstring($in){
            $this->stamp= strtotime($in); 
          }

          public function __constructinteger($in){
            $this->stamp= $in; 
          }

          public static function main(){
            xp::spawn(\'Date\', \'__constructinteger\', array(1)); 
            xp::spawn(\'Date\', \'__constructstring\', array(\'1977-12-14\')); 
          }
        };'),
        $this->emit('class Date {
          protected integer $stamp= 0;

          [@overloaded] public __construct(string $in) {
            $this->stamp= strtotime($in);
          }

          [@overloaded] public __construct(integer $in) {
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
