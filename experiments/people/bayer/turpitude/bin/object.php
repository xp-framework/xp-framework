<?php
  class Test {
    public    $stringvar = "stringval";
    public    $arrayvar = array();
    public    $hashvar = array();
    public    $objvar = NULL;
    public    $nullval = NULL;
    public    $longvar = 1337;
    protected $protected = "protectedvar";
    private   $private = "privatevar";

    function __construct() {
      $this->arrayvar = array(1,2,3,4);
      $this->hashvar = array("fruit" => "apple", "planet" => "mars");
    }

    function setObj($o) {
      $this->objvar = $o;
    }
  }

  $a = new Test();
  $b = new Test();
  $a->setObj($b);

  return $a;
?>
