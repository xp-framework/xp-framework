<?php
/* This file is part of the XP framework's examples
 *
 * $Id$
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('xml.XPath');

  // {{{ main
  $p= new ParamString();

  $xml= <<<__
<dialog id="file.open">
  <caption>Open a file</caption>
  <buttons>
    <button name="ok">OK</button>
    <button name="cancel"/>
  </buttons>
</dialog>
__;

  $query= $p->value(1, NULL, '/dialog/buttons/button');
  
  Console::writeLine('>>> XML: ', "\n", $xml);
  Console::writeLine('>>> Query: ', $query);
  
  $xpath= new XPath($xml);
  try {
    $result= $xpath->query($query);
  } catch (XPathException $e) {
    $e->printStackTrace();
    exit(-1);
  }
  
  Console::writeLine('<<< Result: ', xp::stringOf($result));
  // }}}
?>
