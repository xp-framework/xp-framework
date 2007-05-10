<?php
/* This file is part of the XP framework
 *
 * $Id$
 */
  uses('text.parser.generic.AbstractParser');

#line 2 "FQL.jay"
  uses(
    'FilterFactory',
    'io.collections.FileCollection',
    'io.collections.iterate.FilteredIOCollectionIterator',
    'io.collections.iterate.NameEqualsFilter',
    'io.collections.iterate.AllOfFilter',
    'io.collections.iterate.AnyOfFilter',
    'io.collections.iterate.SizeBiggerThanFilter',
    'io.collections.iterate.NameMatchesFilter'
  );
#line 20 "-"
  define('TOKEN_T_SELECT',  259);
  define('TOKEN_T_FROM',  260);
  define('TOKEN_T_WHERE',  261);
  define('TOKEN_T_STRING',  262);
  define('TOKEN_T_AND',  263);
  define('TOKEN_T_OR',  264);
  define('TOKEN_T_WORD',  266);
  define('TOKEN_T_LIKE',  267);
  define('TOKEN_T_NUMBER',  268);
  define('TOKEN_T_ILIKE',  269);
  define('TOKEN_T_MATCHES',  270);
  define('TOKEN_T_REGEX',  271);
  define('TOKEN_YY_ERRORCODE', 256);

  /**
   * Generated parser class
   *
   * @purpose  Parser implementation
   */
  class FQLParser extends AbstractParser {
    protected static $yyLhs= array(-1,
          0,     1,     2,     2,     3,     3,     3,     4,     4,     7, 
          7,     7,     8,     8,     5,     5,     5,     6,     6, 
    );
    protected static $yyLen= array(2,
          4,     4,     0,     2,     1,     3,     3,     3,     2,     2, 
          2,     3,     0,     1,     1,     1,     1,     1,     1, 
    );
    protected static $yyDefRed= array(0,
          0,     0,     0,     0,     0,     0,     0,     1,     0,     0, 
          4,     0,     2,     0,     0,     0,    15,    16,    17,     0, 
          9,     0,     0,    10,    11,     0,    18,    19,     8,     6, 
          7,    14,    12, 
    );
    protected static $yyDgoto= array(2,
          5,     8,    11,    12,    20,    29,    21,    33, 
    );
    protected static $yySindex = array(         -254,
       -249,     0,  -253,   -28,  -247,  -246,  -251,     0,   -24,   -58, 
          0,  -256,     0,  -244,  -243,  -250,     0,     0,     0,  -262, 
          0,  -251,  -251,     0,     0,  -242,     0,     0,     0,     0, 
          0,     0,     0, 
    );
    protected static $yyRindex= array(            0,
          0,     0,     0,     0,    20,     0,     0,     0,     0,     0, 
          0,    22,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     1,     0,     0,     0,     0, 
          0,     0,     0, 
    );
    protected static $yyGindex= array(0,
          0,     0,   -13,     0,     0,     0,     0,     0, 
    );
    protected static $yyTable = array(27,
         13,    19,    17,    18,     1,    28,    22,    23,    30,    31, 
          3,     6,     4,     7,    10,     9,    13,    24,    25,     3, 
         26,     5,     0,    32,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,    14,     0, 
         15,    16,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,    13,    13, 
    );
    protected static $yyCheck = array(262,
          0,    60,    61,    62,   259,   268,   263,   264,    22,    23, 
        260,    40,   266,   261,   266,   262,    41,   262,   262,     0, 
        271,     0,    -1,   266,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,   267,    -1, 
        269,   270,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,   263,   264, 
    );
    protected static $yyFinal= 2;
    protected static $yyName= array(    
      'end-of-file', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      "'('", "')'", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, "'<'", "'='", "'>'", NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'T_SELECT', 
      'T_FROM', 'T_WHERE', 'T_STRING', 'T_AND', 'T_OR', NULL, 'T_WORD', 'T_LIKE', 
      'T_NUMBER', 'T_ILIKE', 'T_MATCHES', 'T_REGEX', 
    );

    protected static $yyTableCount= 0, $yyNameCount= 0;

    static function __static() {
      self::$yyTableCount= sizeof(self::$yyTable);
      self::$yyNameCount= sizeof(self::$yyName);
    }

    /**
     * Retrieves name of a given token
     *
     * @param   int token
     * @return  string name
     */
    protected function yyname($token) {
      return isset(self::$yyName[$token]) ? self::$yyName[$token] : '<unknown>';
    }

    /**
     * Helper method for yyexpecting
     *
     * @param   int n
     * @return  string[] list of token names.
     */
    protected function yysearchtab($n) {
      if (0 == $n) return array();

      for (
        $result= array(), $token= $n < 0 ? -$n : 0; 
        $token < self::$yyNameCount && $n+ $token < self::$yyTableCount; 
        $token++
      ) {
        if (@self::$yyCheck[$n+ $token] == $token && !isset($result[$token])) {
          $result[$token]= self::$yyName[$token];
        }
      }
      return array_filter(array_values($result));
    }

    /**
     * Computes list of expected tokens on error by tracing the tables.
     *
     * @param   int state for which to compute the list.
     * @return  string[] list of token names.
     */
    protected function yyexpecting($state) {
      return array_merge($this->yysearchtab(self::$yySindex[$state], self::$yyRindex[$state]));
    }

    /**
     * Parser main method. Maintains a state and a value stack, 
     * currently with fixed maximum size.
     *
     * @param   text.parser.generic.AbstractLexer lexer
.    * @return  mixed result of the last reduction, if any.
     */
    public function yyparse($yyLex) {
      $yyVal= NULL;
      $yyStates= $yyVals= array();
      $yyToken= -1;
      $yyState= $yyErrorFlag= 0;

      while (1) {
        for ($yyTop= 0; ; $yyTop++) {
          $yyStates[$yyTop]= $yyState;
          $yyVals[$yyTop]= $yyVal;

          for (;;) {
            if (($yyN= self::$yyDefRed[$yyState]) == 0) {

              // Check whether it's necessary to fetch the next token
              $yyToken < 0 && $yyToken= $yyLex->advance() ? $yyLex->token : 0;

              if (
                ($yyN= self::$yySindex[$yyState]) != 0 && 
                ($yyN+= $yyToken) >= 0 && 
                $yyN < self::$yyTableCount && 
                self::$yyCheck[$yyN] == $yyToken
              ) {
                $yyState= self::$yyTable[$yyN];       // shift to yyN
                $yyVal= $yyLex->value;
                $yyToken= -1;
                $yyErrorFlag > 0 && $yyErrorFlag--;
                continue 2;
              }
        
              if (
                ($yyN= self::$yyRindex[$yyState]) != 0 && 
                ($yyN+= $yyToken) >= 0 && 
                $yyN < self::$yyTableCount && 
                self::$yyCheck[$yyN] == $yyToken
              ) {
                $yyN= self::$yyTable[$yyN];           // reduce (yyN)
              } else {
                switch ($yyErrorFlag) {
                  case 0: return $this->error(
                    E_PARSE, 
                    sprintf(
                      'Syntax error at %s, line %d (offset %d): Unexpected %s',
                      $yyLex->fileName,
                      $yyLex->position[0],
                      $yyLex->position[1],
                      $this->yyName($yyToken)
                    ), 
                    $this->yyExpecting($yyState)
                  );
                  
                  case 1: case 2: {
                    $yyErrorFlag= 3;
                    do { 
                      if (
                        ($yyN= @self::$yySindex[$yyStates[$yyTop]]) != 0 && 
                        ($yyN+= TOKEN_YY_ERRORCODE) >= 0 && 
                        $yyN < self::$yyTableCount && 
                        self::$yyCheck[$yyN] == TOKEN_YY_ERRORCODE
                      ) {
                        $yyState= self::$yyTable[$yyN];
                        $yyVal= $yyLex->value;
                        break 3;
                      }
                    } while ($yyTop-- >= 0);

                    throw new ParseError(E_ERROR, sprintf(
                      'Irrecoverable syntax error at %s, line %d (offset %d)',
                      $yyLex->fileName,
                      $yyLex->position[0],
                      $yyLex->position[1]
                    ));
                  }

                  case 3: {
                    if (0 == $yyToken) {
                      throw new ParseError(E_ERROR, sprintf(
                        'Irrecoverable syntax error at end-of-file at %s, line %d (offset %d)',
                        $yyLex->fileName,
                        $yyLex->position[0],
                        $yyLex->position[1]
                      ));
                    }

                    $yyToken = -1;
                    break 1;
                  }
                }
              }
            }

            $yyV= $yyTop+ 1 - self::$yyLen[$yyN];
            $yyVal= $yyV > $yyTop ? NULL : $yyVals[$yyV];

            // Actions
            switch ($yyN) {

    case 1:  #line 30 "FQL.jay"
    { 
          if ($yyVals[0+$yyTop]) {
            $yyVal= new FilteredIOCollectionIterator($yyVals[-1+$yyTop], $yyVals[0+$yyTop]);
          } else {
            $yyVal= new IOCollectionIterator($yyVals[-1+$yyTop]);
          }
        } break;

    case 2:  #line 40 "FQL.jay"
    {
            $yyVal= new FileCollection($yyVals[-1+$yyTop]);
        } break;

    case 3:  #line 46 "FQL.jay"
    {
          $yyVal= NULL;
        } break;

    case 4:  #line 49 "FQL.jay"
    {
          $yyVal= $yyVals[0+$yyTop];
        } break;

    case 5:  #line 55 "FQL.jay"
    {
          $yyVal= $yyVals[0+$yyTop];
        } break;

    case 6:  #line 58 "FQL.jay"
    {
          $yyVal= new AllOfFilter(array($yyVals[-2+$yyTop], $yyVals[0+$yyTop]));
        } break;

    case 7:  #line 61 "FQL.jay"
    {
          $yyVal=  new AnyOfFilter(array($yyVals[-2+$yyTop], $yyVals[0+$yyTop]));
        } break;

    case 8:  #line 67 "FQL.jay"
    {
          try {
            $yyVal= FilterFactory::filterFor($yyVals[-2+$yyTop], $yyVals[-1+$yyTop], $yyVals[0+$yyTop]);
          } catch (XPException $e) {
            $this->error(E_COMPILE_ERROR, 'In expression "'.$yyVals[-2+$yyTop].' '.$yyVals[-1+$yyTop].'": '.$e->getMessage());
            $yyVal= NULL;
          }
        } break;

    case 9:  #line 75 "FQL.jay"
    {
          try {
            $yyVal= FilterFactory::filterFor($yyVals[-1+$yyTop], '~', $yyVals[0+$yyTop]);
          } catch (XPException $e) {
            $this->error(E_COMPILE_ERROR, 'In expression "'.$yyVals[-1+$yyTop].' '.$yyVals[0+$yyTop].'": '.$e->getMessage());
            $yyVal= NULL;
          }
        } break;

    case 10:  #line 86 "FQL.jay"
    { 
          $yyVal= '/^'.str_replace('%', '.*', preg_quote($yyVals[0+$yyTop])).'$/'; 
        } break;

    case 11:  #line 89 "FQL.jay"
    { 
          $yyVal= '/^'.str_replace('%', '.*', preg_quote($yyVals[0+$yyTop])).'$/i'; 
        } break;

    case 12:  #line 92 "FQL.jay"
    { 
          $yyVal= '/'.$yyVals[-1+$yyTop].'/'.$yyVals[0+$yyTop];
        } break;

    case 13:  #line 98 "FQL.jay"
    {
          $yyVal= '';
        } break;
#line 386 "-"
            }
                   
            $yyTop-= self::$yyLen[$yyN];
            $yyState= $yyStates[$yyTop];
            $yyM= self::$yyLhs[$yyN];

            if (0 == $yyState && 0 == $yyM) {
              $yyState= self::$yyFinal;

              // Check whether it's necessary to fetch the next token
              $yyToken < 0 && $yyToken= $yyLex->advance() ? $yyLex->token : 0;

              // We've reached the final token!
              if (0 == $yyToken) return $yyVal;
              continue 2;
            }

            $yyState= (
              ($yyN= self::$yyGindex[$yyM]) != 0 && 
              ($yyN+= $yyState) >= 0 && 
              $yyN < self::$yyTableCount && 
              self::$yyCheck[$yyN] == $yyState
            ) ? self::$yyTable[$yyN] : self::$yyDgoto[$yyM];
            continue 2;
          }
        }
      }
    }

  }
?>
