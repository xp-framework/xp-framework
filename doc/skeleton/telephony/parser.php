<?php
  require ('lang.base.php');
  uses (
    'util.telephony.TelephonyAddress',
    'util.telephony.TelephonyAddressParser'
  );
  
  print ("Parsing examples:\n");
  try(); {
    $parser= &new TelephonyAddressParser ();

    $numbers= array (
      '+49 721 555106',
      '+49 721 555-106',
      '0721 555 106',
      '555106',
      '0049 721 5555-106',
      '0179 6741880',
      '511',
      '++++++++49 -#+#-.721-,,.-91374-----++++511',
      '+49 700 ALEXKIESEL',
      '+49 700 ALEX-KIESEL'
    );

    foreach ($numbers as $number) {
      $p= &$parser->parseNumber ($number);
      
      printf ("%50s => %s\n",
        '['.$number.']',
        $p->getNumber()
      );
    }    
        
  } if (catch ('Exception', $e)) {
    $e->printStackTrace();
    exit;
  }

  // Get the number I have to dial to call $b from $a  
  $a= &$parser->parseNumber ('+49 721 91374-578');
  $b= &$parser->parseNumber ('91374-511');

  echo "\n\n";
  printf ("Source number:      %s\nDestination number: %s\n",
    $a->getNumber(),
    $b->getNumber()
  );
  
  printf ("Number to dial:     %s\n",
    ($b->getNumber ($a->getCallCategory ($b)))
  );

?>
