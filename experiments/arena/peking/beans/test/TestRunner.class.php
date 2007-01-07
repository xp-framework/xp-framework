<?php
  uses('remote.beans.BeanInterface');

  interface TestRunner extends BeanInterface {
  
    /**
     * (Insert method's description here)
     *
     * @param   string classname
     * @return  mixed results
     */
    public function runTestClass($classname);
  }
?>
