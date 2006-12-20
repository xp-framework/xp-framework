<?php
/* This file is part of the XP framework's examples
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('util.Calendar', 'text.parser.DateParser');
  
  define('DATEFMT', '%a, %b %d %Y');
  
  // {{{ util.Date dateParam(util.cmd.ParamString p, string param, int default
  //     Retrieves a date from the command line arguments
  function dateParam($p, $param, $default) {
    if (!$p->exists($param)) return new Date($default);
    
    try {
      $date= DateParser::parse($p->value($param));
    } catch (FormatException $e) {
      Console::writeLine('*** ', $e->getMessage());
      exit(-1);
    }
    return $date;
  }
  // }}}
  
  // {{{ main
  with ($p= new ParamString()); {
    $s= dateParam($p, 'start', time());
    $e= dateParam($p, 'end', time()+ 2 * CAL_SEC_DAY);
  }

  // Sanity check
  if ($e->isBefore($s)) {
    Console::writeLinef(
      '*** End date %s must be after start date %s', 
      $e->format(DATEFMT), 
      $s->format(DATEFMT)
    );
    exit(0);
  }
  
  Console::writeLinef(
    "Date range:\n  Begin : %s (week #%d)\n  End   : %s (week #%d)", 
    $s->format(DATEFMT),
    Calendar::week($s),
    $e->format(DATEFMT),
    Calendar::week($e)
  );
    
  // Calculate holidays (see: http://galway.informatik.uni-kl.de/rec/feiertage.html)
  // -------------------------------------------------------------------------------
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
  for ($i= $s->getYear(); $i <= $e->getYear(); $i++) {
  
    // Fixed holidays
    $holiday[gmmktime(0, 0, 0, 1, 1, $i)]=   'Neujahr';
    $holiday[gmmktime(0, 0, 0, 1, 6, $i)]=   'Heilige 3 Könige';
    $holiday[gmmktime(0, 0, 0, 5, 1, $i)]=   '1. Mai';
    $holiday[gmmktime(0, 0, 0, 8, 15, $i)]=  'Maria Himmelfahrt';
    $holiday[gmmktime(0, 0, 0, 10, 3, $i)]=  'Tag der deutschen Einheit';
    $holiday[gmmktime(0, 0, 0, 10, 31, $i)]= 'Reformationstag';
    $holiday[gmmktime(0, 0, 0, 11, 1, $i)]=  'Allerheiligen';
    $holiday[gmmktime(0, 0, 0, 12, 24, $i)]= 'Heiligabend';
    $holiday[gmmktime(0, 0, 0, 12, 25, $i)]= '1. Weihnachtsfeiertag';
    $holiday[gmmktime(0, 0, 0, 12, 26, $i)]= '2. Weihnachtsfeiertag';
    $holiday[gmmktime(0, 0, 0, 12, 31, $i)]= 'Sylvester';
    
    // Holidays dependant on easter
    $easter= &Calendar::easter($i);
    $holiday[$easter->getTime() - CAL_SEC_DAY * 48]= 'Rosenmontag';
    $holiday[$easter->getTime() - CAL_SEC_DAY * 46]= 'Aschermittwoch';
    $holiday[$easter->getTime() - CAL_SEC_DAY * 2]=  'Karfreitag';
    $holiday[$easter->getTime()]=                    'Ostersonntag';
    $holiday[$easter->getTime() + CAL_SEC_DAY * 1]=  'Ostermontag';
    $holiday[$easter->getTime() + CAL_SEC_DAY * 39]= 'Himmelfahrt';
    $holiday[$easter->getTime() + CAL_SEC_DAY * 49]= 'Pfingstsonntag';
    $holiday[$easter->getTime() + CAL_SEC_DAY * 50]= 'Pfingstmontag';
    $holiday[$easter->getTime() + CAL_SEC_DAY * 60]= 'Fronleichnam';
    
    // Holidays dependant on 1st of advent
    $advent= &Calendar::advent($i);
    $holiday[$advent->getTime()]=                    '1. Advent';       
    $holiday[$advent->getTime() + CAL_SEC_DAY * 7]=  '2. Advent';       
    $holiday[$advent->getTime() + CAL_SEC_DAY * 14]= '3. Advent';       
    $holiday[$advent->getTime() + CAL_SEC_DAY * 21]= '4. Advent';       
    $holiday[$advent->getTime() - CAL_SEC_DAY * 35]= 'Volkstrauertag';  
    $holiday[$advent->getTime() - CAL_SEC_DAY * 32]= 'Buß- und Bettag'; 
    $holiday[$advent->getTime() - CAL_SEC_DAY * 28]= 'Totensonntag';    
  }
  
  Console::writeLinef(
    '  Workdays between dates [without holidays]: %d', 
    Calendar::workdays($s, $e)
  );
  
  Console::writeLinef(
    '  Workdays between dates [with holidays]   : %d', 
    Calendar::workdays($s, $e, $holiday)
  );
  
  Console::writeLine();
  Console::writeLine('Holidays used in above calculation:');
  ksort($holiday);
  foreach ($holiday as $stamp => $name) {
    Console::writeLinef('  %s: %s', strftime(DATEFMT, $stamp), $name);
  }
  // }}}
?>
