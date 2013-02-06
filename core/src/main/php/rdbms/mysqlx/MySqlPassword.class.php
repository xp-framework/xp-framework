<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.Enum', 'math.BigInt');

  /**
   * MySQL password functions
   *
   * @see   php://sha1
   * @test  xp://net.xp_framework.unittest.rdbms.mysql.MySqlPasswordTest
   * @see   http://forge.mysql.com/wiki/MySQL_Internals_ClientServer_Protocol
   */
  abstract class MySqlPassword extends Enum {
    public static 
      $PROTOCOL_40= NULL,
      $PROTOCOL_41= NULL;
    
    static function __static() {
      self::$PROTOCOL_40= newinstance(__CLASS__, array(0, 'PROTOCOL_40'), '{
        static function __static() { }
        
        public static function hash($in) {
          $nr= new BigInt(1345345333);
          $nr2= new BigInt(0x12345671);
          $add= new BigInt(7);

          for ($i= 0, $s= strlen($in); $i < $s; $i++) {
            $ord= ord($in{$i});
            if (0x20 === $ord || 0x09 === $ord) continue;
            $value= $nr->bitwiseAnd(63)->add0($add)->multiply0($ord)->add0($nr->multiply0(0x100));
            $nr= $nr->bitwiseXor($value);
            $nr2= $nr2->multiply0(0x100)->bitwiseXor($nr)->add0($nr2);
            $add= $add->add0($ord);
          }
          return array($nr->bitwiseAnd(0x7FFFFFFF), $nr2->bitwiseAnd(0x7FFFFFFF));
        }
        
        public function scramble($password, $message) {
          if ("" === $password || NULL === $password) return "";

          $hp= self::hash($password);
          $hm= self::hash($message);
          $SEED_MAX= 0x3FFFFFFF;

          $seed1= $hp[0]->bitwiseXor($hm[0])->modulo($SEED_MAX);
          $seed2= $hp[1]->bitwiseXor($hm[1])->modulo($SEED_MAX);
          $to= "";
          for ($i= 0, $s= strlen($message); $i < $s; $i++) {
            $seed1= $seed1->multiply0(3)->add0($seed2)->modulo($SEED_MAX);
            $seed2= $seed1->add0($seed2)->add0(33)->modulo($SEED_MAX);
            $to.= chr($seed1->divide(new BigFloat($SEED_MAX))->multiply(31)->intValue() + 64);
          }
          $seed1= $seed1->multiply0(3)->add0($seed2)->modulo($SEED_MAX);
          $seed2= $seed1->add0($seed2)->add0(33)->modulo($SEED_MAX);

          return $to ^ str_repeat(chr($seed1->divide(new BigFloat($SEED_MAX))->multiply(31)->intValue()), strlen($message));
        }
      }');
      self::$PROTOCOL_41= newinstance(__CLASS__, array(1, 'PROTOCOL_41'), '{
        static function __static() { }
        public function scramble($password, $message) {
          if ("" === $password || NULL === $password) return "";

          $stage1= sha1($password, TRUE);
          return sha1($message.sha1($stage1, TRUE), TRUE) ^ $stage1;
        }
      }');
    }
    
    /**
     * Scrambles a given password
     *
     * @param   string password
     * @param   string message
     * @return  string
     */
    public abstract function scramble($password, $message);
  }
?>
