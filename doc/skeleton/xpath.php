<?php
  /* 
   * Dokumentiert XPath-Klasse
   * 
   * $Id$
   */
  
  require_once('../../skeleton/lang.base.php');
  define('HR',  str_repeat('-', 72));
  import('xml.XPath');

  $xpath= new XPath();
  $xpath->setContextFile('xpath.xml');
  
  $expressions= array(
    "//include[@href='product']/@name",
    "/include_parts/part/product/language/*",
    "//b",
    "//b/text()",
    "/*"
  );
  
  foreach ($expressions as $e) {
    printf(
      "%s\n%s\n%s\n%s\n", 
      $e, 
      HR,
      $xpath->evaluate($e),
      HR
    );
  }
?>
