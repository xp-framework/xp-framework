<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.Enum', 'Profileable');

  /**
   * Increment profiling
   *
   * @purpose  Profiling
   */
  abstract class Increment extends Enum implements Profileable {
    public static
      $post,
      $pre,
      $binary;
    
    static function __static() {
      self::$post= newinstance(__CLASS__, array(0, 'post'), '{
        static function __static() { }

        public function run($times) {
          $a= 0;
          for ($i= 0; $i < $times; $i++) {
            $a++;
          }
        }
      }');
      self::$pre= newinstance(__CLASS__, array(1, 'pre'), '{
        static function __static() { }

        public function run($times) {
          $a= 0;
          for ($i= 0; $i < $times; $i++) {
            ++$a;
          }
        }
      }');
      self::$binary= newinstance(__CLASS__, array(2, 'binary'), '{
        static function __static() { }

        public function run($times) {
          $a= 0;
          for ($i= 0; $i < $times; $i++) {
            $a= $a+ 1;
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
