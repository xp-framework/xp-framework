<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.Enum', 'math.BigInt');

  /**
   * (Insert class' description here)
   *
   * @see   php://sha1
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
            $value= $nr->bitwiseAnd(new BigInt(63))->add($add)->multiply(new BigInt($ord))->add($nr->multiply(new BigInt(0x100)));
            $nr= $nr->bitwiseXor($value);
            $nr2= $nr2->multiply(new BigInt(0x100))->bitwiseXor($nr)->add($nr2);
            $add= $add->add(new BigInt($ord));
          }
          return array($nr->bitwiseAnd(new BigInt(0x7FFFFFFF)), $nr2->bitwiseAnd(new BigInt(0x7FFFFFFF)));
        }
        
        public function scramble($password, $message) {
          if ("" === $password || NULL === $password) return "";

          $hp= self::hash($password);
          $hm= self::hash($message);
          $SEED_MAX= new BigInt(0x3FFFFFFF);

          $seed1= $hp[0]->bitwiseXor($hm[0])->modulo($SEED_MAX);
          $seed2= $hp[1]->bitwiseXor($hm[1])->modulo($SEED_MAX);
          $to= "";
          for ($i= 0, $s= strlen($message); $i < $s; $i++) {
            $seed1= $seed1->multiply(new BigInt(3))->add($seed2)->modulo($SEED_MAX);
            $seed2= $seed1->add($seed2)->add(new BigInt(33))->modulo($SEED_MAX);
            $rnd= $seed1->divide($SEED_MAX)->multiply(new BigInt(31));
            $to.= chr($rnd->intValue() + 64);
          }
          $seed1= $seed1->multiply(new BigInt(3))->add($seed2)->modulo($SEED_MAX);
          $seed2= $seed1->add($seed2)->add(new BigInt(33))->modulo($SEED_MAX);
          $rnd= $seed1->divide($SEED_MAX)->multiply(new BigInt(31));

          return $to ^ str_repeat(chr($rnd->intValue()), strlen($message));
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
     * Returns all enum members
     *
     * @return  lang.Enum[]
     */
    public static function values() {
      return parent::membersOf(__CLASS__);
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
