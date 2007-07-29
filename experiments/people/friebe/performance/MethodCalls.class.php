<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.Enum', 'Profileable');

  /**
   * Method calls profiling
   *
   * @purpose  Profiling
   */
  abstract class MethodCalls extends Enum implements Profileable {
    public static
      $public,
      $private,
      $protected;
    
    static function __static() {
      self::$public= newinstance(__CLASS__, array(0, 'public'), '{
        static function __static() { }
        public function publicMethod($i) {
          $i++;
        }

        public function run($times) {
          for ($i= 0; $i < $times; $i++) {
            $this->publicMethod($i);
          }
        }
      }');
      self::$private= newinstance(__CLASS__, array(1, 'private'), '{
        static function __static() { }
        private function privateMethod($i) {
          $i++;
        }

        public function run($times) {
          for ($i= 0; $i < $times; $i++) {
            $this->privateMethod($i);
          }
        }
      }');
      self::$protected= newinstance(__CLASS__, array(2, 'protected'), '{
        static function __static() { }
        protected function protectedMethod($i) {
          $i++;
        }

        public function run($times) {
          for ($i= 0; $i < $times; $i++) {
            $this->protectedMethod($i);
          }
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
  }
?>
