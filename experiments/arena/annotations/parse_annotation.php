<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */
  require('lang.base.php');

  // Parser states
  define('ST_INITIAL',  0);
  define('ST_TOKEN',    1);
  define('ST_VALUE',    2);
  define('ST_KEY',      3);
  define('ST_ARRAY',    4);
  
  $states= array(
    ST_INITIAL  => 'ST_INITIAL',
    ST_TOKEN    => 'ST_TOKEN',
    ST_VALUE    => 'ST_VALUE',
    ST_KEY      => 'ST_KEY',
    ST_ARRAY    => 'ST_ARRAY',
  );

  // {{{ main
  $DEBUG= TRUE;
  $annotation= (isset($argv[1]) 
    ? $argv[1] 
    : '[@test, @webmethod(name = \'Hello\'), @restricted(roles= array(\'admin\', \'root\')), @deprecated(\'Use new method X instead\')]'
  );
  var_dump($annotation);
  
  $tokens= token_get_all('<?php '.trim($annotation, '[]').' ?>');
  $state= ST_INITIAL;
  for ($i= 1, $s= sizeof($tokens)- 1; $i < $s; $i++) {
  
    // {{{ DEBUG
    $DEBUG && printf(
      '[%-10s (%d)] %s',
      $states[$state],
      $state,
      (is_array($tokens[$i]) 
        ? token_name($tokens[$i][0]).' :: '.$tokens[$i][1]
        : 'T_NONE :: '.$tokens[$i]
      )
    );
    // }}}
    
    switch ($state.$tokens[$i][0]) {
      case ST_INITIAL.'@':
        $state= ST_TOKEN;
        break;
      
      case ST_TOKEN.T_STRING:
        $name= $tokens[$i][1];
        $annotations[$name]= NULL;
        break;
              
      case ST_TOKEN.'(':
        $state= ST_VALUE;
        break;
      
      case ST_TOKEN.',':
        $state= ST_INITIAL;
        break;
      
      case ST_VALUE.T_STRING:
        $key= $tokens[$i][1];
        $annotations[$name]= array($key => NULL);
        $state= ST_KEY;
        break;
        
      case ST_VALUE.T_CONSTANT_ENCAPSED_STRING:
        $annotations[$name]= trim($tokens[$i][1], '"\'');
        break;
      
      case ST_KEY.T_ARRAY:
        $annotations[$name][$key]= array();
        $state= ST_ARRAY;
        break;
      
      case ST_KEY.T_CONSTANT_ENCAPSED_STRING:
        $annotations[$name][$key]= trim($tokens[$i][1], '"\'');
        break;        

      case ST_KEY.T_LNUMBER:
        $annotations[$name][$key]= (int)$tokens[$i][1];
        break;

      case ST_KEY.T_DNUMBER:
        $annotations[$name][$key]= (float)$tokens[$i][1];
        break;
      
      case ST_KEY.T_STRING:
        $map= array('TRUE' => TRUE, 'FALSE' => FALSE, 'NULL' => NULL);
        $key= strtoupper($tokens[$i][1]);
        if (!array_key_exists($key, $map)) {
          print(" *** Parse error ***\n");
          $annotations= NULL;
          break 2;
        }
        $annotations[$name]= $map[$key];
        break;
      
      case ST_KEY.')':
        $state= ST_TOKEN;
        break;
      
      case ST_ARRAY.T_CONSTANT_ENCAPSED_STRING:
        $annotations[$name][$key][]= trim($tokens[$i][1], '"\'');
        break;

      case ST_ARRAY.T_LNUMBER:
        $annotations[$name][$key][]= (int)$tokens[$i][1];
        break;

      case ST_ARRAY.T_DNUMBER:
        $annotations[$name][$key][]= (float)$tokens[$i][1];
        break;
      
      case ST_ARRAY.T_STRING:
        $map= array('TRUE' => TRUE, 'FALSE' => FALSE, 'NULL' => NULL);
        $key= strtoupper($tokens[$i][1]);
        if (!array_key_exists($key, $map)) {
          print(" *** Parse error ***\n");
          $annotations= NULL;
          break 2;
        }
        $annotations[$name][$key][]= $map[$key];
        break;
      
      case ST_ARRAY.')':
        $state= ST_VALUE;
        break;
      
      case ST_VALUE.')':
        $state= ST_TOKEN;
        break;

      case ST_ARRAY.',':
      case ST_ARRAY.'(':
      case ST_KEY.'=':
      case ST_ARRAY.T_WHITESPACE:
      case ST_INITIAL.T_WHITESPACE:
      case ST_KEY.T_WHITESPACE:
      case ST_VALUE.T_WHITESPACE:
      case ST_TOKEN.T_WHITESPACE:
        break;

      default:
        print(" *** Parse error ***\n");
        $annotations= NULL;
        break 2;
    }
    $DEBUG && print("\n");
  }
  var_dump($annotations);
  // }}}
?>
