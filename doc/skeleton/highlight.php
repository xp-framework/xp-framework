<?php
/* Demonstrates the usage of the PHPSyntaxHighlighter class
 *
 * Pipe the output of this script into an HTML file if you're running
 * on command line and then use your favorite HTML viewer to look at
 * the results
 *
 * $Id$
 */
  require('lang.base.php');
  uses('util.text.PHPSyntaxHighlighter');
  
  $p= &new PHPSyntaxHighlighter('<?php
  // Test class
  class Test extends Object {
    function init() {
      $this->test= array(
        "hello"     => "world"
        "encoding"  => "application/x-www-urlencoded"
      );
    }
  }
  
  $test= &new Test();
  $test->init();
?>');
  echo $p->getHighlight();
?>
