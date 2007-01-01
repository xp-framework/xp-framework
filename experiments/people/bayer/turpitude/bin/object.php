<?php
  class Test {
    var $stringvar = "stringval",
        $arrayvar = array(),
        $hashvar = array(),
        $longvar = 1337;

    function Test() {
      $this->arrayvar = array(1,2,3,4);
      $this->hashvar = array("fruit" => "apple", "planet" => "mars");
    }
  }

  return new Test();
?>
