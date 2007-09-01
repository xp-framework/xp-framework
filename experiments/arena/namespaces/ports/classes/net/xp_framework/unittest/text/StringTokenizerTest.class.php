<?php
/* This class is part of the XP framework
 *
 * $Id: StringTokenizerTest.class.php 10308 2007-05-08 21:15:47Z friebe $ 
 */

  namespace net::xp_framework::unittest::text;

  ::uses(
    'net.xp_framework.unittest.text.TokenizerTest',
    'text.StringTokenizer'
  );

  /**
   * Test string tokenizing.
   *
   * @see      xp://text.StringTokenizer
   * @purpose  TestCase
   */
  class StringTokenizerTest extends TokenizerTest {
  
    /**
     * Retrieve a tokenizer instance
     *
     * @param   string source
     * @param   string delimiters default ' '
     * @param   bool returnDelims default FALSE
     * @return  text.Tokenizer
     */
    protected function tokenizerInstance($source, $delimiters= ' ', $returnDelims= FALSE) {
      return new text::StringTokenizer($source, $delimiters, $returnDelims);
    }
  }
?>
