<?php
 // created by jay 0.8 (c) 1998 Axel.Schreiner@informatik.uni-osnabrueck.de
 // modified by alan@akbkhome.com to try to generate php!
 // modified by cellog@users.sourceforge.net to fit PEAR CS
 // %token constants

  define ('TOKEN_T_SELECT',  259);
  $GLOBALS['_TOKEN_DEBUG'][259] = 'T_SELECT';
  define ('TOKEN_T_FROM',  260);
  $GLOBALS['_TOKEN_DEBUG'][260] = 'T_FROM';
  define ('TOKEN_T_WHERE',  261);
  $GLOBALS['_TOKEN_DEBUG'][261] = 'T_WHERE';
  define ('TOKEN_T_STRING',  262);
  $GLOBALS['_TOKEN_DEBUG'][262] = 'T_STRING';
  define ('TOKEN_T_AND',  263);
  $GLOBALS['_TOKEN_DEBUG'][263] = 'T_AND';
  define ('TOKEN_T_OR',  264);
  $GLOBALS['_TOKEN_DEBUG'][264] = 'T_OR';
  define ('TOKEN_T_WORD',  266);
  $GLOBALS['_TOKEN_DEBUG'][266] = 'T_WORD';
  define ('TOKEN_T_LIKE',  267);
  $GLOBALS['_TOKEN_DEBUG'][267] = 'T_LIKE';
  define ('TOKEN_T_NUMBER',  268);
  $GLOBALS['_TOKEN_DEBUG'][268] = 'T_NUMBER';
  define ('TOKEN_T_ILIKE',  269);
  $GLOBALS['_TOKEN_DEBUG'][269] = 'T_ILIKE';
  define ('TOKEN_T_MATCHES',  270);
  $GLOBALS['_TOKEN_DEBUG'][270] = 'T_MATCHES';
  define ('TOKEN_T_REGEX',  271);
  $GLOBALS['_TOKEN_DEBUG'][271] = 'T_REGEX';
if (!defined('TOKEN_yyErrorCode')) {   define('TOKEN_yyErrorCode', 256);
}
 // Class now

#line 2 "FQL.jay"
?><?php
  uses(
    'CompileError',
    'FilterFactory',
    'io.collections.FileCollection',
    'io.collections.iterate.FilteredIOCollectionIterator',
    'io.collections.iterate.NameEqualsFilter',
    'io.collections.iterate.AllOfFilter',
    'io.collections.iterate.AnyOfFilter',
    'io.collections.iterate.SizeBiggerThanFilter',
    'io.collections.iterate.NameMatchesFilter'
  );
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */

  class FQLParser extends Object {
    var
      $cat    = NULL,
      $errors = array();

    /**
     * Adds an error
     *
     * @access  public
     * @param   &net.xp_framework.tools.vm.CompileError error
     */
    function addError(&$error) {
      $this->errors[]= &$error;
    }
    
    /**
     * Returns whether errors have occured
     *
     * @access  public
     * @return  bool
     */
    function hasErrors() {
      return !empty($this->errors);
    }

    /**
     * Returns whether errors have occured
     *
     * @access  public
     * @return  net.xp_framework.tools.vm.CompileError[]
     */
    function getErrors() {
      return $this->errors;
    }

    /**
     * Error handler
     *
     * @access  public
     * @param   int level
     * @param   string message
     */
    function error($level, $message) {
      switch ($level) {
        case E_ERROR:
        case E_CORE_ERROR:
        case E_COMPILE_ERROR:
          $this->addError(new CompileError($level, $message));
          // Fall-through intended
      }
      
      $this->cat && $this->cat->error($message);
    }

    /**
     * Set a logger category for debugging
     *
     * @access  public
     * @param   util.log.LogCategory cat
     */
    function setTrace($cat) {
      $this->cat= $cat;
    }
#line 105 "-"

    /**
     * thrown for irrecoverable syntax errors and stack overflow.
     */
    
     var $yyErrorCode = 256;

    /**
     * Debugging
     */
     var $debug = false;


  var $yyLhs  = array(              -1,
    0,    1,    2,    2,    3,    3,    3,    4,    4,    7,
    7,    7,    8,    8,    5,    5,    5,    6,    6,
  );
  var $yyLen = array(           2,
    4,    4,    0,    2,    1,    3,    3,    3,    2,    2,
    2,    3,    0,    1,    1,    1,    1,    1,    1,
  );
  var $yyDefRed = array(            0,
    0,    0,    0,    0,    0,    0,    0,    1,    0,    0,
    4,    0,    2,    0,    0,    0,   15,   16,   17,    0,
    9,    0,    0,   10,   11,    0,   18,   19,    8,    6,
    7,   14,   12,
  );
  var $yyDgoto  = array(             2,
    5,    8,   11,   12,   20,   29,   21,   33,
  );
  var $yySindex = array(         -254,
 -249,    0, -253,  -28, -247, -246, -251,    0,  -24,  -58,
    0, -256,    0, -244, -243, -250,    0,    0,    0, -262,
    0, -251, -251,    0,    0, -242,    0,    0,    0,    0,
    0,    0,    0,
  );
  var $yyRindex= array(            0,
    0,    0,    0,    0,   20,    0,    0,    0,    0,    0,
    0,   22,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    1,    0,    0,    0,    0,
    0,    0,    0,
  );
  var $yyGindex = array(            0,
    0,    0,  -13,    0,    0,    0,    0,    0,
  );
  var $yyTable = array(            27,
   13,   19,   17,   18,    1,   28,   22,   23,   30,   31,
    3,    6,    4,    7,   10,    9,   13,   24,   25,    3,
   26,    5,    0,   32,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,   14,    0,
   15,   16,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,   13,   13,
  );
 var $yyCheck = array(           262,
    0,   60,   61,   62,  259,  268,  263,  264,   22,   23,
  260,   40,  266,  261,  266,  262,   41,  262,  262,    0,
  271,    0,   -1,  266,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,  267,   -1,
  269,  270,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,  263,  264,
  );
  var $yyFinal = 2;
//tvar $yyRule = array(
//t   "\$accept :  start ",
//t    "start :  T_SELECT   T_FROM   collection   where ",
//t    "collection :  T_WORD  '('  T_STRING  ')'",
//t    "where :",
//t    "where :  T_WHERE   restrictions ",
//t    "restrictions :  restriction ",
//t    "restrictions :  restriction   T_AND   restrictions ",
//t    "restrictions :  restriction   T_OR   restrictions ",
//t    "restriction :  T_WORD   operator   criteria ",
//t    "restriction :  T_WORD   matches ",
//t    "matches :  T_LIKE   T_STRING ",
//t    "matches :  T_ILIKE   T_STRING ",
//t    "matches :  T_MATCHES   T_REGEX   optional_modifiers ",
//t    "optional_modifiers :",
//t    "optional_modifiers :  T_WORD ",
//t    "operator : '='",
//t    "operator : '>'",
//t    "operator : '<'",
//t    "criteria :  T_STRING ",
//t    "criteria :  T_NUMBER ",
//t  );
  var $yyName= array(    
    "end-of-file",null,null,null,null,null,null,null,null,null,null,null,
    null,null,null,null,null,null,null,null,null,null,null,null,null,null,
    null,null,null,null,null,null,null,null,null,null,null,null,null,null,
    "'('","')'",null,null,null,null,null,null,null,null,null,null,null,
    null,null,null,null,null,null,null,"'<'","'='","'>'",null,null,null,
    null,null,null,null,null,null,null,null,null,null,null,null,null,null,
    null,null,null,null,null,null,null,null,null,null,null,null,null,null,
    null,null,null,null,null,null,null,null,null,null,null,null,null,null,
    null,null,null,null,null,null,null,null,null,null,null,null,null,null,
    null,null,null,null,null,null,null,null,null,null,null,null,null,null,
    null,null,null,null,null,null,null,null,null,null,null,null,null,null,
    null,null,null,null,null,null,null,null,null,null,null,null,null,null,
    null,null,null,null,null,null,null,null,null,null,null,null,null,null,
    null,null,null,null,null,null,null,null,null,null,null,null,null,null,
    null,null,null,null,null,null,null,null,null,null,null,null,null,null,
    null,null,null,null,null,null,null,null,null,null,null,null,null,null,
    null,null,null,null,null,null,null,null,null,null,null,null,null,null,
    null,null,null,null,null,null,null,null,null,null,null,null,null,null,
    null,null,null,null,null,null,null,null,null,null,null,"T_SELECT",
    "T_FROM","T_WHERE","T_STRING","T_AND","T_OR",null,"T_WORD","T_LIKE",
    "T_NUMBER","T_ILIKE","T_MATCHES","T_REGEX",
  );
    /**
     * (syntax) error message.
     * Can be overwritten to control message format.
     * @param message text to be displayed.
     * @param expected vector of acceptable tokens, if available.
     */
    function raiseError ($message, $expected = null)
    {     
        if ($expected !== null ) {
            $m = "$message expecting";
            foreach($expected as $e) {
                $m .= " $e";
            }
        } else {
            $m = $message;
        }
        return $this->error(E_COMPILE_ERROR, $message);  
    }



    /**
     * index-checked interface to yyName[].
     * @param token single character or %token value.
     * @return token name or [illegal] or [unknown].
     */
    function yyname ($token) {
        if ($token < 0 || $token >  count($this->yyName)) return "[illegal]";
        if (($name = $this->yyName[$token]) != null) return $name;
        return "[unknown]";
    }

    /**
     * computes list of expected tokens on error by tracing the tables.
     * @param state for which to compute the list.
     * @return list of token names.
     */
    function yyExpecting ($state) {
        $len = 0;
        $ok = array();//new boolean[YyNameClass.yyName.length];

        if (($n =  $this->yySindex[$state]) != 0) {
            $start = $n;
            if ($start < 0) { $start = 0; }       
            for ($token = $start;
                $token < count($this->yyName) && 
                        $n+$token < count($this->yyTable); $token++) {
                if (@$this->yyCheck[$n+$token] == $token && !@$ok[$token] && 
                        $this->yyName[$token] != null) {
                    $len++;
                    $ok[$token] = true;
                }
            } // end for
        }
        if (($n = $this->yyRindex[$state]) != 0) {
            $start = $n;
            if ($start < 0) { $start = 0; }       
            for ($token = $start;
                     $token < count($this->yyName)  && 
                     $n+$token <  count($this->yyTable); $token++) 
            {
               if (@$this->yyCheck[$n+$token] == $token && !@$ok[$token] 
                          && @$this->yyName[$token] != null) {
                    $len++;
                    $ok[$token] = true;
               }
            } // end for
        }
        $result = array();
        for ($n = $token = 0; $n < $len;  $token++) {
            if (@$ok[$token]) { $result[$n++] =$this->yyName[$token]; }
        }
        return $result;
    }


    /**
     * initial size and increment of the state/value stack [default 256].
     * This is not final so that it can be overwritten outside of invocations
     * of yyparse().
     */
    var $yyMax;

    /**
     * executed at the beginning of a reduce action.
     * Used as $$ = yyDefault($1), prior to the user-specified action, if any.
     * Can be overwritten to provide deep copy, etc.
     * @param first value for $1, or null.
     * @return first.
     */
    function yyDefault ($first) {
        return $first;
    }

    /**
     * the generated parser.
     * Maintains a state and a value stack, currently with fixed maximum size.
     * @param yyLex scanner.
     * @return result of the last reduction, if any.
     * @throws yyException on irrecoverable parse error.
     */
    function yyparse (&$yyLex) {
//t        $this->debug = true;
        $this->yyLex = &$yyLex;

        if ($this->debug)
           echo "\tStarting jay:yyparse";
        //error_reporting(E_ALL);
        if ($this->yyMax <= 0) $this->yyMax = 256;			// initial size
        $yyState = 0;
        $yyStates = array();
        $yyVal = null;
        $yyVals = array();
        $this->yyTableCount = count($this->yyTable);
        $yyToken = -1;                 // current input
        $yyErrorFlag = 0;              // #tks to shift
        $tloop = 0;
    
        while (1) {//yyLoop: 
            //echo "yyLoop\n";
            //if ($this->debug) echo "\tyyLoop:\n";
            for ($yyTop = 0;; $yyTop++) {
                //if ($this->debug) echo ($tloop++) .">>>>>>yyLoop:yTop = {$yyTop}\n";
                $yyStates[$yyTop] = $yyState;
                $yyVals[$yyTop] = $yyVal;

                //yyDiscarded: 
                for (;;) {	// discarding a token does not change stack
                    //echo "yyDiscarded\n";
                    if ($this->debug) echo "\tIn main loop : State = {$yyState}\n";
                    if ($this->debug) echo "\tyydefred = {$this->yyDefRed[$yyState]}\n";
                    if (($yyN = $this->yyDefRed[$yyState]) == 0) {	
                        // else [default] reduce (yyN)
                        //if ($this->debug) echo "\tA:token is $yyToken\n";
                        if ($yyToken < 0) {
                            //if ($this->debug) echo "\tA:advance\n";
                            if ($yyLex->advance()) {
                               
                                $yyToken = $yyLex->token ;
                            } else {
                                $yyToken = 0;
                            }
                        }
                        if ($this->debug) {
                            echo "\tA:token is now " .
                            "{$this->yyName[$yyToken]} " .token_name($yyToken).  "\n";
                            var_dump($yyToken);
                        }
                        //if ($this->debug) echo "GOT TOKEN $yyToken";
                        //if ($this->debug) echo "Sindex:  {$this->yySindex[$yyState]}\n";

                        if (($yyN = $this->yySindex[$yyState]) != 0
                                  && ($yyN += $yyToken) >= 0
                                  && $yyN < $this->yyTableCount && $this->yyCheck[$yyN] == $yyToken) {
                            $yyState = $this->yyTable[$yyN];		// shift to yyN
                            $yyVal = $yyLex->value;
                            $yyToken = -1;
                            if ($yyErrorFlag > 0) $yyErrorFlag--;
                            continue 2; // goto!!yyLoop;
                        }
 
                       
              
                        if (($yyN = $this->yyRindex[$yyState]) != 0
                                && ($yyN += $yyToken) >= 0
                                && $yyN < $this->yyTableCount && $this->yyCheck[$yyN] == $yyToken) {
                            $yyN = $this->yyTable[$yyN];			// reduce (yyN)
                        } else {
                            switch ($yyErrorFlag) {
    
                                case 0:
                                    $info = $yyLex->parseError();
                                    $info .= ', Unexpected '.$this->yyName($yyToken).',';
                                    return $this->raiseError("$info syntax error",
                                                $this->yyExpecting($yyState));
                                
                                case 1: case 2:
                                    $yyErrorFlag = 3;
                                    do { 
                                        if (($yyN = @$this->yySindex
                                                [$yyStates[$yyTop]]) != 0
                                                && ($yyN += $this->yyErrorCode) >= 0 && $yyN < $this->yyTableCount
                                                && $this->yyCheck[$yyN] == $this->yyErrorCode) {
                                            $yyState = $this->yyTable[$yyN];
                                            $yyVal = $yyLex->value;
                                            //vi /echo "goto yyLoop?\n";
                                            break 3; //continue yyLoop;
                                        }
                                    } while ($yyTop-- >= 0);
                                    $info = $yyLex->parseError();
                                    return $this->raiseError("$info irrecoverable syntax error");
    
                                case 3:
                                    if ($yyToken == 0) {
                                        $info =$yyLex->parseError();
                                        return $this->raiseError("$info irrecoverable syntax error at end-of-file");
                                    }
                                    $yyToken = -1;
                                    //echo "goto yyDiscarded?";  
                                    break 1; //continue yyDiscarded;		// leave stack alone
                            }
                        }
                    }    
                    $yyV = $yyTop + 1-$this->yyLen[$yyN];
                    //if ($this->debug) echo "\tyyV is $yyV\n";
                    $yyVal = $yyV > $yyTop ? null : $yyVals[$yyV];
                    // echo "\tyyVal is ". serialize($yyVal) ."\n";
                    if ($this->debug) echo "\tswitch($yyN)\n";
                   
 switch ($yyN) {

    case 1:  #line 100 "FQL.jay"
    { 
          if ($yyVals[0+$yyTop]) {
            $yyVal= &new FilteredIOCollectionIterator($yyVals[-1+$yyTop], $yyVals[0+$yyTop]);
          } else {
            $yyVal= &new IOCollectionIterator($yyVals[-1+$yyTop]);
          }
        } break;

    case 2:  #line 110 "FQL.jay"
    {
            $yyVal= &new FileCollection($yyVals[-1+$yyTop]);
        } break;

    case 3:  #line 116 "FQL.jay"
    {
          $yyVal= NULL;
        } break;

    case 4:  #line 119 "FQL.jay"
    {
          $yyVal= $yyVals[0+$yyTop];
        } break;

    case 5:  #line 125 "FQL.jay"
    {
          $yyVal= $yyVals[0+$yyTop];
        } break;

    case 6:  #line 128 "FQL.jay"
    {
          $yyVal= &new AllOfFilter(array($yyVals[-2+$yyTop], $yyVals[0+$yyTop]));
        } break;

    case 7:  #line 131 "FQL.jay"
    {
          $yyVal= &new AnyOfFilter(array($yyVals[-2+$yyTop], $yyVals[0+$yyTop]));
        } break;

    case 8:  #line 137 "FQL.jay"
    {
          try(); {
            $yyVal= &FilterFactory::filterFor($yyVals[-2+$yyTop], $yyVals[-1+$yyTop], $yyVals[0+$yyTop]);
          } if (catch('Exception', $e)) {
            $this->error(E_COMPILE_ERROR, 'In expression "'.$yyVals[-2+$yyTop].' '.$yyVals[-1+$yyTop].'": '.$e->getMessage());
            $yyVal= NULL;
          }
        } break;

    case 9:  #line 145 "FQL.jay"
    {
          try(); {
            $yyVal= &FilterFactory::filterFor($yyVals[-1+$yyTop], '~', $yyVals[0+$yyTop]);
          } if (catch('Exception', $e)) {
            $this->error(E_COMPILE_ERROR, 'In expression "'.$yyVals[-1+$yyTop].' '.$yyVals[0+$yyTop].'": '.$e->getMessage());
            $yyVal= NULL;
          }
        } break;

    case 10:  #line 156 "FQL.jay"
    { 
          $yyVal= '/^'.str_replace('%', '.*', preg_quote($yyVals[0+$yyTop])).'$/'; 
        } break;

    case 11:  #line 159 "FQL.jay"
    { 
          $yyVal= '/^'.str_replace('%', '.*', preg_quote($yyVals[0+$yyTop])).'$/i'; 
        } break;

    case 12:  #line 162 "FQL.jay"
    { 
          $yyVal= '/'.$yyVals[-1+$yyTop].'/'.$yyVals[0+$yyTop];
        } break;

    case 13:  #line 168 "FQL.jay"
    {
          $yyVal= '';
        } break;
#line 544 "-"
  }
                   
                    //if ($this->debug) echo "\tDONE switch\n";if ($this->debug) echo "\t--------------\n";
                    $yyTop -= $this->yyLen[$yyN];
                    //if ($this->debug) echo "\tyyTop is $yyTop\n";
                    $yyState = $yyStates[$yyTop];
                    //if ($this->debug) echo "\tyyState is {$yyState}\n";
                    $yyM = $this->yyLhs[$yyN];
                    //if ($this->debug) echo "\tyyM is now $yyM\n";



                    if ($yyState == 0 && $yyM == 0) {
                        $yyState = $yyFinal;
                        if ($yyToken < 0) {
                            $yyToken =0;
                            if ($yyLex->advance()) {
                                $yyToken = $yyLex->token;
                            }
                        }
                        if ($this->debug) echo "\tTOKEN IS NOW $yyToken\n";
                        if ($yyToken == 0) {
                            return $yyVal;
                        }
                        //if ($this->debug) echo "\t>>>>> yyLoop(A)?\n";
                        continue 2; //continue yyLoop;
                    }
                    if (($yyN = $this->yyGindex[$yyM]) != 0 && ($yyN += $yyState) >= 0
                            && $yyN < $this->yyTableCount && $this->yyCheck[$yyN] == $yyState) {
                        //if ($this->debug) echo "\tyyState: using yyTable\n";
                        $yyState = $this->yyTable[$yyN];
                    } else {
                        //if ($this->debug) echo "\tyyState: using yyDgoto\n";
                        $yyState = $this->yyDgoto[$yyM];
                    }  
                    //if ($this->debug) echo "\t>>>>> yyLoop(B)?\n";
                    continue 2;//continue yyLoop;
                }
            }
        }
    }


#line 185 "FQL.jay"
} implements(__FILE__, 'util.log.Traceable');
 
#line 591 "-"


 ?>
