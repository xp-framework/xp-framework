<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('Unmarshaller');
  
  // {{{ main
  $xml= <<<__
<Order reference="12343-AHSHE-314159">
  <Client>
    <Name>Jean Smith</Name>
    <Address>2000, Alameda de las Pulgas, San Mateo, CA 94403</Address>
  </Client>

  <Item reference="RF-0001">
    <Description>Stuffed Penguin</Description>
    <Quantity>10</Quantity>
    <UnitPrice>8.95</UnitPrice>
  </Item>

  <Item reference="RF-0034">
    <Description>Chocolate</Description>
    <Quantity>5</Quantity>
    <UnitPrice>28.50</UnitPrice>
  </Item>

  <Item reference="RF-3341">
    <Description>Cookie</Description>
    <Quantity>30</Quantity>
    <UnitPrice>0.85</UnitPrice>
  </Item>
</Order>
__;

  try(); {
    $order= &Unmarshaller::unmarshal($xml, 'Order');
  } if (catch('Throwable', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
  Console::writeLine(xp::stringOf($order));
  // }}}
?>
