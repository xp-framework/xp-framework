<?php
/* This file is part of the XP framework
 *
 * $Id$
 */

  

  uses('text.parser.generic.AbstractParser');

  /**
   * Generated parser class
   *
   * @purpose  Parser implementation
   */
  class JsonParser extends AbstractParser {
    const T_TRUE= 261;
    const T_FALSE= 262;
    const T_NULL= 263;
    const T_INT= 264;
    const T_FLOAT= 265;
    const T_STRING= 270;
    const T_ESCAPE_QUOTATION= 271;
    const T_ESCAPE_REVERSESOLIDUS= 272;
    const T_ESCAPE_SOLIDUS= 273;
    const T_ESCAPE_BACKSPACE= 274;
    const T_ESCAPE_FORMFEED= 275;
    const T_ESCAPE_NEWLINE= 276;
    const T_ESCAPE_CARRIAGERETURN= 277;
    const T_ESCAPE_HORIZONTALTAB= 278;
    const T_ESCAPE_UNICODE= 279;
    const YY_ERRORCODE= 256;

    protected static $yyLhs= array(-1,
          0,     0,     0,     0,     1,     1,     5,     5,     6,     7, 
          7,     2,     2,     9,     9,     3,     3,     8,     8,    10, 
         10,    10,    10,    10,    10,    10,    10,    10,    10,     4, 
          4,     4,     4,     4, 
    );
    protected static $yyLen= array(2,
          1,     1,     1,     1,     3,     2,     1,     3,     3,     3, 
          2,     3,     2,     1,     3,     3,     2,     1,     2,     1, 
          1,     1,     1,     1,     1,     1,     1,     1,     1,     1, 
          1,     1,     1,     1, 
    );
    protected static $yyDefRed= array(0,
         30,    31,    32,    34,    33,     0,     0,     0,     0,     1, 
          2,     3,     4,     6,     0,     0,     7,     0,    20,    21, 
         22,    23,    24,    25,    26,    27,    28,    29,    17,     0, 
         18,    13,    14,     0,    11,     0,     5,     0,     0,    16, 
         19,     0,    12,    10,     8,     9,    15, 
    );
    protected static $yyDgoto= array(9,
         10,    11,    12,    13,    16,    17,    18,    30,    34,    31, 
    );
    protected static $yySindex = array(           15,
          0,     0,     0,     0,     0,   -32,   -34,   -33,     0,     0, 
          0,     0,     0,     0,   -24,   -41,     0,   -52,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,   -14, 
          0,     0,     0,   -40,     0,    -4,     0,   -26,    15,     0, 
          0,    15,     0,     0,     0,     0,     0, 
    );
    protected static $yyRindex= array(            0,
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0, 
    );
    protected static $yyGindex= array(-3,
          0,     0,     0,     0,     0,   -29,     0,    -1,     0,   -23, 
    );
    protected static $yyTable = array(29,
          7,    15,    38,    42,    33,    39,    41,    15,    45,    35, 
          0,     0,    41,    36,     0,     0,     0,     0,     0,    40, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,    44, 
          0,     0,     0,     0,     0,    46,     0,     0,    47,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     7,     0, 
          0,     0,    43,     0,     0,     0,     0,     8,     0,    32, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,    37,     0,     0,     0,     0,     0,     6, 
          0,     0,    14,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     8,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     6,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     1,     2,     3, 
          4,     5,     0,     0,     0,    19,    20,    21,    22,    23, 
         24,    25,    26,    27,    28,    19,    20,    21,    22,    23, 
         24,    25,    26,    27,    28,    19,    20,    21,    22,    23, 
         24,    25,    26,    27,    28,    19,    20,    21,    22,    23, 
         24,    25,    26,    27,    28,     1,     2,     3,     4,     5, 
    );
    protected static $yyCheck = array(34,
         34,    34,    44,    44,     8,    58,    30,    34,    38,    34, 
         -1,    -1,    36,    15,    -1,    -1,    -1,    -1,    -1,    34, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    34, 
         -1,    -1,    -1,    -1,    -1,    39,    -1,    -1,    42,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    34,    -1, 
         -1,    -1,    93,    -1,    -1,    -1,    -1,    91,    -1,    93, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,   125,    -1,    -1,    -1,    -1,    -1,   123, 
         -1,    -1,   125,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    91,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,   123,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,   261,   262,   263, 
        264,   265,    -1,    -1,    -1,   270,   271,   272,   273,   274, 
        275,   276,   277,   278,   279,   270,   271,   272,   273,   274, 
        275,   276,   277,   278,   279,   270,   271,   272,   273,   274, 
        275,   276,   277,   278,   279,   270,   271,   272,   273,   274, 
        275,   276,   277,   278,   279,   261,   262,   263,   264,   265, 
    );
    protected static $yyFinal= 9;
    protected static $yyName= array(    
      'end-of-file', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, "'\"'", NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, "','", NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, "':'", NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, "'['", 
      NULL, "']'", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, "'{'", NULL, "'}'", NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, 'T_TRUE', 'T_FALSE', 'T_NULL', 'T_INT', 'T_FLOAT', NULL, 
      NULL, NULL, NULL, 'T_STRING', 'T_ESCAPE_QUOTATION', 
      'T_ESCAPE_REVERSESOLIDUS', 'T_ESCAPE_SOLIDUS', 'T_ESCAPE_BACKSPACE', 
      'T_ESCAPE_FORMFEED', 'T_ESCAPE_NEWLINE', 'T_ESCAPE_CARRIAGERETURN', 
      'T_ESCAPE_HORIZONTALTAB', 'T_ESCAPE_UNICODE', 
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

    case 5:  #line 28 "grammar/json.jay"
    { $yyVal= $yyVals[-1+$yyTop]; } break;

    case 6:  #line 29 "grammar/json.jay"
    { $yyVal= array(); } break;

    case 8:  #line 34 "grammar/json.jay"
    { $yyVal= $yyVals[-2+$yyTop] + $yyVals[0+$yyTop]; } break;

    case 9:  #line 38 "grammar/json.jay"
    { $yyVal= array($yyVals[-2+$yyTop] => $yyVals[0+$yyTop]); } break;

    case 10:  #line 42 "grammar/json.jay"
    { $yyVal= utf8_decode($yyVals[-1+$yyTop]); /* xp::CHARSET */ } break;

    case 11:  #line 43 "grammar/json.jay"
    { $yyVal= ''; } break;

    case 12:  #line 47 "grammar/json.jay"
    { $yyVal= $yyVals[-1+$yyTop]; } break;

    case 13:  #line 48 "grammar/json.jay"
    { $yyVal= array(); } break;

    case 14:  #line 52 "grammar/json.jay"
    { $yyVal= array($yyVals[0+$yyTop]); } break;

    case 15:  #line 53 "grammar/json.jay"
    { $yyVal= array_merge($yyVals[-2+$yyTop], array($yyVals[0+$yyTop])); } break;

    case 16:  #line 57 "grammar/json.jay"
    { $yyVal= new String($yyVals[-1+$yyTop], 'utf-8'); } break;

    case 17:  #line 58 "grammar/json.jay"
    { $yyVal= String::$EMPTY; } break;

    case 19:  #line 63 "grammar/json.jay"
    { $yyVal= $yyVals[-1+$yyTop].$yyVals[0+$yyTop]; } break;

    case 20:  #line 67 "grammar/json.jay"
    { $yyVal= $yyVals[0+$yyTop]; } break;

    case 21:  #line 68 "grammar/json.jay"
    { $yyVal= '"'; } break;

    case 22:  #line 69 "grammar/json.jay"
    { $yyVal= "\\"; } break;

    case 23:  #line 70 "grammar/json.jay"
    { $yyVal= "/"; } break;

    case 24:  #line 71 "grammar/json.jay"
    { $yyVal= "\b"; } break;

    case 25:  #line 72 "grammar/json.jay"
    { $yyVal= "\f"; } break;

    case 26:  #line 73 "grammar/json.jay"
    { $yyVal= "\n"; } break;

    case 27:  #line 74 "grammar/json.jay"
    { $yyVal= "\r"; } break;

    case 28:  #line 75 "grammar/json.jay"
    { $yyVal= "\t"; } break;

    case 29:  #line 76 "grammar/json.jay"
    {
                                $yyVal= iconv(
                                  'ucs-4be',
                                  'utf-8',
                                  pack('N', hexdec(substr($yyVals[0+$yyTop], 2)))
                                );
                              } break;

    case 30:  #line 86 "grammar/json.jay"
    { $yyVal= TRUE; } break;

    case 31:  #line 87 "grammar/json.jay"
    { $yyVal= FALSE; } break;

    case 32:  #line 88 "grammar/json.jay"
    { $yyVal= NULL; } break;

    case 33:  #line 89 "grammar/json.jay"
    { $yyVal= doubleval($yyVals[0+$yyTop]); } break;

    case 34:  #line 90 "grammar/json.jay"
    { $yyVal= intval($yyVals[0+$yyTop]); } break;
#line 403 "-"
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
