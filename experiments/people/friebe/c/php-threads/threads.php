<?php
  function run($name) {
    echo "### THREAD-", $name, ": Hello###\n";
    for ($i= 0; $i < 5; $i++) {        
      printf("    %s: %d\n", $name, $i);        
      usleep(rand(1, 4) * 100000);      
    }
    printf("### THREAD-%s: DONE ###\n", $name);    
    
    return $name;
  }
 
  $t= thread_new('#1');
  var_dump($t);
  $r= thread_new('#2');
  var_dump($r);
  $x= thread_new('#3');
  var_dump($x);

  thread_start($t, 'run', array('NumberOne'));
  thread_start($r, 'run', array('NumberTwo'));
  thread_start($x, 'run', array('NumberThree'));
  
  var_dump(thread_join($t));
  var_dump(thread_join($r));
  var_dump(thread_join($x));
?>
