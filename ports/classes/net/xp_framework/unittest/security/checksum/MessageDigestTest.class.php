<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'security.checksum.MessageDigest'
  );

  /**
   * TestCase
   *
   * @see      xp://security.checksum.MessageDigest
   */
  class MessageDigestTest extends TestCase {
  
    /**
     * Test
     *
     */
    #[@test, @expect('security.NoSuchAlgorithmException')]
    public function unsupportedAlgorithm() {
      newinstance('security.checksum.MessageDigest', array(), '{
        protected function algorithm() { return "@@illegal@@"; }
        protected function instance($final) { /* Unreachable */ }
      }');
    }
  }
?>
