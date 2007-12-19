<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'net.xp_framework.unittest.text.AbstractTokenizerTest',
    'text.StringTokenizer'
  );

  /**
   * Test string tokenizing.
   *
   * @see      xp://text.StringTokenizer
   * @purpose  TestCase
   */
  class StringTokenizerTest extends AbstractTokenizerTest {
  
    /**
     * Retrieve a tokenizer instance
     *
     * @param   string source
     * @param   string delimiters default ' '
     * @param   bool returnDelims default FALSE
     * @return  text.Tokenizer
     */
    protected function tokenizerInstance($source, $delimiters= ' ', $returnDelims= FALSE) {
      return new StringTokenizer($source, $delimiters, $returnDelims);
    }
  }
?>
