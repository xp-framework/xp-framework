<?php
  require('lang.base.php');
  uses('util.Calendar');
  
  setlocale(LC_TIME, 'de_DE.ISO_8859-1');
  $s= time();
  $e= time()+ 20* 86400;
    
  // Ferien berechnen (Quelle: http://galway.informatik.uni-kl.de/rec/feiertage.html)
  // --------------------------------------------------------------------------------
  // Neujahr = 01.01.
  // Maifeiertag = 05.01.
  // Karfreitag = Ostern - 2 Tage
  // Ostermontag = Ostern + 1 Tag
  // Himmelfahrt = Ostern + 39 Tage
  // Pfingstsonntag = Ostern + 49 Tage
  // Pfingstmontag = Ostern + 50 Tage
  // Fronleichnam = Ostern + 60 Tage
  // Tag der Deutschen Einheit = 10.03.
  // Rosenmontag = Ostern - 48 Tage
  // Aschermittwoch = Ostern - 46 Tage
  // Maria Himmelfahrt = 15.08.
  // Allerheiligen = 01.11.
  // Heiligabend = 24.12.
  // Erster Weihnachtsfeiertag = 25.12.
  // Zweiter Weihnachtsfeiertag = 26.12.
  // Sylvester = 31.12.
  // Reformationstag = 31.10.
  // Buss-und Bettag = 1. Advent - 32 Tage
  // Heilige drei Könige = 06.01.
  // Totensonntag = 1. Advent - 28 Tage
  // Volkstrauertag = 1. Advent - 35 Tage
  $holiday= array();
  for ($i= date('Y', $s); $i <= date('Y', $e); $i++) {
  
    // Feste Feiertage
    $holiday[gmmktime(0, 0, 0, 1, 1, $i)]=     'Neujahr';
    $holiday[gmmktime(0, 0, 0, 1, 6, $i)]=     'Heilige 3 Könige';
    $holiday[gmmktime(0, 0, 0, 5, 1, $i)]=     '1. Mai';
    $holiday[gmmktime(0, 0, 0, 8, 15, $i)]=     'Maria Himmelfahrt';
    $holiday[gmmktime(0, 0, 0, 10, 3, $i)]=     'Tag der deutschen Einheit';
    $holiday[gmmktime(0, 0, 0, 10, 31, $i)]=     'Reformationstag';
    $holiday[gmmktime(0, 0, 0, 11, 1, $i)]=     'Allerheiligen';
    $holiday[gmmktime(0, 0, 0, 12, 24, $i)]=     'Heiligabend';
    $holiday[gmmktime(0, 0, 0, 12, 25, $i)]=     '1. Weihnachtsfeiertag';
    $holiday[gmmktime(0, 0, 0, 12, 26, $i)]=     '2. Weihnachtsfeiertag';
    $holiday[gmmktime(0, 0, 0, 12, 31, $i)]=     'Sylvester';
    
    // Bewegliche Feiertage, von Ostern abhängig
    $easter= Calendar::easter($i);
    $easter= $easter->_utime;
    $holiday[$easter - CAL_SEC_DAY * 48]=     'Rosenmontag';
    $holiday[$easter - CAL_SEC_DAY * 46]=     'Aschermittwoch';
    $holiday[$easter - CAL_SEC_DAY * 2]=     'Karfreitag';
    $holiday[$easter]=                     'Ostersonntag';
    $holiday[$easter + CAL_SEC_DAY * 1]=     'Ostermontag';
    $holiday[$easter + CAL_SEC_DAY * 39]=     'Himmelfahrt';
    $holiday[$easter + CAL_SEC_DAY * 49]=     'Pfingstsonntag';
    $holiday[$easter + CAL_SEC_DAY * 50]=     'Pfingstmontag';
    $holiday[$easter + CAL_SEC_DAY * 60]=     'Fronleichnam';
    
    // Bewegliche Feiertage, vom ersten Advent abhängig
    $advent= Calendar::advent($i);
    $advent= $advent->_utime;
    $holiday[$advent]=                     '1. Advent';
    $holiday[$advent + CAL_SEC_DAY * 7]=     '2. Advent';
    $holiday[$advent + CAL_SEC_DAY * 14]=     '3. Advent';
    $holiday[$advent + CAL_SEC_DAY * 21]=     '4. Advent';
    $holiday[$advent - CAL_SEC_DAY * 35]=     'Volkstrauertag';
    $holiday[$advent - CAL_SEC_DAY * 32]=     'Buß- und Bettag';
    $holiday[$advent - CAL_SEC_DAY * 28]=     'Totensonntag';
  }
  
  // Werktage zwischen zwei Dati
  printf(
    "Werktage vom %s bis zum %s [ohne Feiertage]: %d\n", 
    strftime('%A, %d. %B', $s),
    strftime('%A, %d. %B', $e),
    Calendar::workdays($s, $e)
  );
  
  // Werktage zwischen zwei Dati, mit (deutschen) Feiertagen
  printf(
    "Werktage vom %s bis zum %s [mit Feiertagen]: %d\n", 
    strftime('%A, %d. %B', $s),
    strftime('%A, %d. %B', $e),
    Calendar::workdays($s, $e, $holiday)
  );
  
  printf("In obige Berechnung einbezogene Feiertage:\n");
  ksort($holiday);
  foreach ($holiday as $t=> $name) {
    printf("%-30s: %s\n", strftime('%A, %d. %B %Y', $t), $name);
  }
  
  // Kalenderwoche
  printf("Heutige Kalenderwoche: %d\n", Calendar::week());

  // Ostern und 1. Advent
  for ($i= 1999; $i<= 2010; $i++) printf(
    "%d: Ostersonntag %-20s 1. Advent %-20s\n", 
    $i, 
    strftime('%A, %d. %B', Calendar::easter($i)),
    strftime('%A, %d. %B', Calendar::advent($i))
  );
?>
