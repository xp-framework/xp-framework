<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase');

  /**
   * TestCase
   *
   * @see      reference
   * @purpose  purpose
   */
  class MockedRpcRouterTest extends TestCase {
  
    /**
     * Check for existance of specific header
     *
     * @param   string[] headers
     * @return  string needle
     * @throws  unittest.AssertionFailedError
     */
    protected function assertHasHeader($headers, $needle) {
      foreach ($headers as $h) {
        if (FALSE !== (strpos($h, $needle))) return;
      }
      
      $this->fail('Expected header not found', $headers, $needle);
    }
    
    /**
     * Check if one string contains another
     *
     * @param   
     * @return  
     */
    protected function assertStringContained(String $haystack, String $needle) {
      if (FALSE === strpos((string)$needle, (string)$haystack)) {
        $this->fail('Expected sub-string not found', $needle, $haystack);
      }
    }
  }
?>
