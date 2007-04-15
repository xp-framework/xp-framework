<?php
/* This file is part of the XP framework
 *
 * $Id$
 */
  uses('text.parser.generic.AbstractParser');

#line 2 "grammar/compact-xml.jay"
  uses('xml.Tree', 'xml.Node', 'xml.Comment');
#line 11 "-"
  define('TOKEN_T_WORD',  259);
  define('TOKEN_T_STRING',  260);
  define('TOKEN_T_NUMBER',  261);
  define('TOKEN_T_TEXT',  262);
  define('TOKEN_T_COMMENT',  263);
  define('TOKEN_YY_ERRORCODE', 256);

  /**
   * Generated parser class
   *
   * @purpose  Parser implementation
   */
  class CompactXmlParser extends AbstractParser {
    protected static $yyLhs= array(-1,
          0,     1,     1,     1,     2,     2,     3,     3,     6,     4, 
          4,     5,     5,     7,     7,     7, 
    );
    protected static $yyLen= array(2,
          1,     6,     3,     1,     1,     4,     3,     1,     3,     1, 
          0,     3,     1,     2,     1,     0, 
    );
    protected static $yyDefRed= array(0,
          0,     4,     0,     1,     0,     0,    10,     0,     0,     0, 
          0,     0,     8,     0,    13,     3,     6,     0,     0,     0, 
         15,     0,     9,     0,     7,    12,    14,     2, 
    );
    protected static $yyDgoto= array(3,
          4,     5,    12,     9,    16,    13,    22, 
    );
    protected static $yySindex = array(         -253,
        -84,     0,     0,     0,   -39,  -250,     0,  -247,   -56,   -79, 
        -46,   -33,     0,  -253,     0,     0,     0,  -244,  -245,  -247, 
          0,  -123,     0,   -56,     0,     0,     0,     0, 
    );
    protected static $yyRindex= array(            0,
        -40,     0,     0,     0,   -55,     0,     0,     0,     0,     0, 
          0,     0,     0,  -107,     0,     0,     0,     0,   -55,     0, 
          0,     0,     0,     0,     0,     0,     0,     0, 
    );
    protected static $yyGindex= array(0,
         -9,     0,     0,     1,    -3,     2,     0, 
    );
    protected static $yyTable = array(5,
          8,    26,    15,    11,    21,     1,     6,    19,    10,     2, 
         20,    11,    27,    17,    18,    23,     7,    16,     5,    24, 
         28,    25,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,    14,    11,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     5,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     1,     0,     0,     0,     2, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     0,     0,     0,     0,     0,     0,     0,     0,     0, 
          0,     5,     7, 
    );
    protected static $yyCheck = array(40,
         40,   125,    59,    59,    14,   259,    91,    41,   259,   263, 
         44,   259,    22,    93,    61,   260,   262,   125,    59,    19, 
         24,    20,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,   123,   123,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,   123,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,   259,    -1,    -1,    -1,   263, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1, 
         -1,   262,   262, 
    );
    protected static $yyFinal= 3;
    protected static $yyName= array(    
      'end-of-file', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      "'('", "')'", NULL, NULL, "','", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, "';'", NULL, "'='", NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, "'['", NULL, 
      "']'", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, "'{'", NULL, "'}'", NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      'T_WORD', 'T_STRING', 'T_NUMBER', 'T_TEXT', 'T_COMMENT', 
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

    case 1:  #line 14 "grammar/compact-xml.jay"
    {
          $yyVal= new Tree(); 
          $yyVal->root= $yyVals[0+$yyTop];
        } break;

    case 2:  #line 21 "grammar/compact-xml.jay"
    {
          $yyVals[-5+$yyTop]->attribute= array_merge($yyVals[-5+$yyTop]->attribute, $yyVals[-3+$yyTop]);
          $yyVals[-5+$yyTop]->content= $yyVals[-1+$yyTop];
          $yyVals[-5+$yyTop]->children= $yyVals[0+$yyTop];
        } break;

    case 3:  #line 26 "grammar/compact-xml.jay"
    { 
          $yyVals[-2+$yyTop]->content= $yyVals[-1+$yyTop];
          $yyVals[-2+$yyTop]->children= $yyVals[0+$yyTop];
        } break;

    case 4:  #line 30 "grammar/compact-xml.jay"
    {
          $yyVal= new Comment($yyVals[0+$yyTop]);
        } break;

    case 5:  #line 36 "grammar/compact-xml.jay"
    { 
          $yyVal= new Node($yyVals[0+$yyTop]);
        } break;

    case 6:  #line 39 "grammar/compact-xml.jay"
    {
		  $yyVal= new Node($yyVals[-3+$yyTop], NULL, array('id' => $yyVals[-1+$yyTop]));
        } break;

    case 7:  #line 45 "grammar/compact-xml.jay"
    { 
          $yyVal= array_merge($yyVals[-2+$yyTop], $yyVals[0+$yyTop]); 
        } break;

    case 8:  #line 48 "grammar/compact-xml.jay"
    { 
          /* $$= $1; */
        } break;

    case 9:  #line 54 "grammar/compact-xml.jay"
    { 
          $yyVal= array($yyVals[-2+$yyTop] => $yyVals[0+$yyTop]); 
        } break;

    case 10:  #line 60 "grammar/compact-xml.jay"
    { 
          /* $$= $1; */
        } break;

    case 11:  #line 63 "grammar/compact-xml.jay"
    { 
          $yyVal= NULL;
        } break;

    case 12:  #line 69 "grammar/compact-xml.jay"
    { 
          $yyVal= $yyVals[-1+$yyTop];
        } break;

    case 13:  #line 72 "grammar/compact-xml.jay"
    { 
          $yyVal= array(); 
        } break;

    case 14:  #line 78 "grammar/compact-xml.jay"
    { 
          $yyVal[]= $yyVals[0+$yyTop];
        } break;

    case 15:  #line 81 "grammar/compact-xml.jay"
    { 
          $yyVal= array($yyVals[0+$yyTop]);
        } break;

    case 16:  #line 84 "grammar/compact-xml.jay"
    { 
          $yyVal= array(); 
        } break;
#line 363 "-"
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
