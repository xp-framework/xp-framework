<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  $package= 'security.password';
  
  uses('security.password.Algorithm', 'util.log.Traceable');

  /**
   * A password strength algorithm inspired by "The Password Meter".
   *
   * Requirements
   * ============
   * The password must be minimum 8 characters in length and must contain
   * at least 3 of the following:
   * <ol>
   *   <li>Lowercase characters</li>
   *   <li>Uppercase characters</li>
   *   <li>Numbers [0-9]</li>
   *   <li>Symbols [anything except A-Z, a-z, 0-9]</li>
   * </ol>
   *
   * @see      http://passwordmeter.com/
   * @purpose  Algorithm implementation
   */
  class security·password·StandardAlgorithm extends Object implements security·password·Algorithm, Traceable {
    protected
      $cat = NULL;

    /**
     * Add a given number to the strength
     *
     * @param   int num
     * @param   string what
     * @return  int
     */
    protected function add($num, $what) {
      $this->cat && $this->cat->debug('+ ', $num, '[', $what, ']');
      return $num;
    }

    /**
     * Deduct a given number from the strength
     *
     * @param   int num
     * @param   string what
     * @return  int
     */
    protected function deduct($num, $what) {
      $this->cat && $this->cat->debug('- ', $num, '[', $what, ']');
      return $num;
    }
    
    /**
     * Calculate the strength of a password
     *
     * @param   string password
     * @return  int
     */
    public function strengthOf($password) {
      $length= strlen($password);
      $this->cat && $this->cat->info('Password: ', $password);
      
      $count= array(
        'lcalpha'  => 0,
        'ucalpha'  => 0,
        'num'      => 0,
        'sym'      => 0,
        
        // Consecutive (e.g. "agh", "ZGF", ",._" or "1978")
        'lcalphac' => 0,
        'ucalphac' => 0,
        'numc'     => 0,
        'symc'     => 0,
        
        // Sequential (e.g. "abc", "ZYX" or "123")
        'lcalphas' => 0,
        'ucalphas' => 0,
        'nums'     => 0,
        'syms'     => 0,
        
        // Repeat
        'repeat'   => 0,
      );
      
      // Characterize string
      $last= '';
      for ($i= 0; $i < $length; $i++) {
        if ($password{$i} >= 'a' && $password{$i} <= 'z') {
          $inc= 'lcalpha';
        } else if ($password{$i} >= 'A' && $password{$i} <= 'Z') {
          $inc= 'ucalpha';
        } else if ($password{$i} >= '0' && $password{$i} <= '9') {
          $inc= 'num';
        } else {
          $inc= 'sym';
        }

        $count[$inc]++;
        if ($last === $inc) $count[$inc.'c']++;
        if (0 === strcasecmp(substr($password, $i, 3), implode('', range($password{$i}, chr(ord($password{$i})+ 2))))) $count[$inc.'s']++;
        if (substr_count($password, $password{$i}) > 1) $count['repeat']++;
        $last= $inc;
      }
      
      // Check how many requirements are met
      $req=
        ($length >= 8) + 
        ($count['num'] > 0) + 
        ($count['lcalpha'] > 0) + 
        ($count['ucalpha'] > 0) + 
        ($count['sym'] > 0)
      ;
      
      // Calculate strength
      $strength= max(0, min(100, 
        + $this->add($req < 4 ? 0 : $req * 2, 'Requirements')
        
        // Additions
        + $this->add($length * 4, 'Length')
        + $this->add($count['num'] == $length ? 0 : $count['num'] * 4, 'Numbers')
        + $this->add($count['sym'] * 6, 'Symbols')
        + $this->add($count['ucalpha'] == 0 || $count['ucalpha'] == $length ? 0 : ($length - $count['ucalpha']) * 2, 'Lowercase')
        + $this->add($count['lcalpha'] == 0 || $count['lcalpha'] == $length ? 0 : ($length - $count['lcalpha']) * 2, 'Uppercase')
        
        // Deductions
        - $this->deduct($count['ucalpha'] + $count['lcalpha'] == $length ? $length : 0, 'Letters only')
        - $this->deduct($count['num'] == $length ? $length : 0, 'Numbers only')
        - $this->deduct($count['repeat'] * ($count['repeat'] - 1), 'Repeated characters')
        - $this->deduct($count['ucalphac'] * 2, 'Consecutive uppercase')
        - $this->deduct($count['lcalphac'] * 2, 'Consecutive lowercase')
        - $this->deduct($count['numc'] * 2, 'Consecutive numbers')
        - $this->deduct(($count['lcalphas'] + $count['ucalphas']) * 3, 'Sequential letters')
        - $this->deduct($count['nums'] * 3, 'Sequential numbers')
      ));
      
      $this->cat && $this->cat->info('Strength: ', $strength);
      return $strength;
    }

    /**
     * Set a trace for debugging
     *
     * @param   util.log.LogCategory cat
     */
    public function setTrace($cat) {
      $this->cat= $cat;
    }
  }
?>
