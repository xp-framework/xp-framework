--TEST--
Check for xp presence
--SKIPIF--
<?php if (!extension_loaded("xp")) print "skip"; ?>
--POST--
--GET--
--INI--
--FILE--
<?php 
echo "xp extension is available";
/*
	you can add regression tests for your extension here

  the output of your test code has to be equal to the
  text in the --EXPECT-- section below for the tests
  to pass, differences between the output and the
  expected text are interpreted as failure

	see php4/README.TESTING for further information on
  writing regression tests
*/
?>
--EXPECT--
xp extension is available
