<?php
  function run0() {
    echo "### THREAD-1: Hello###\n";
    for ($i= 0; $i < 5; $i++) {        
      printf("    1: %d\n", $i);        
      usleep(rand(1, 4) * 100000);      
    }
    printf("### THREAD-1: DONE ###\n");    
  }

  function run1() {
    echo "### THREAD-2: Hello###\n";
    for ($i= 0; $i < 5; $i++) {        
      printf("    2: %d\n", $i);        
      usleep(rand(1, 4) * 100000);      
    }
    printf("### THREAD-2: DONE ###\n");    
  }

  function run2() {
    echo "### THREAD-3: Hello###\n";
    for ($i= 0; $i < 5; $i++) {        
      printf("    3: %d\n", $i);        
      usleep(rand(1, 4) * 100000);      
    }
    printf("### THREAD-3: DONE ###\n");    
  }
  
  $t= thread_new('#1');
  var_dump($t);
  $s= thread_start($t, 'run0');
  var_dump($s);

  $r= thread_new('#2');
  var_dump($r);
  $q= thread_start($r, 'run1');
  var_dump($q);

  $x= thread_new('#3');
  var_dump($x);
  $y= thread_start($x, 'run2');
  var_dump($y);
  
  var_dump(thread_join($t));
  var_dump(thread_join($r));
  var_dump(thread_join($x));
?>
