// created by jay 0.8 (c) 1998 Axel.Schreiner@informatik.uni-osnabrueck.de

					// line 2 "Arith.jay"
<?

class Arith {		// must first specify class header
				// must not use yy[A-Z].* as identifiers
				// could overwrite methods named yy[a-z].*
				// in (anonymous) subclass
					// line 11 "-"
// %token constants

  var $Number = 99;
  var $UNARY = 257;
  var $yyErrorCode = 256;

  /** thrown for irrecoverable syntax errors and stack overflow.
    */
    
   function yyException ($message) {
           echo $message;
           exit;        
    
   }

  /** must be implemented by a scanner object to supply input to the parser.
    */
  var $yyInput; // must implement advance, token and value!
    

  /** (syntax) error message.
      Can be overwritten to control message format.
      @param message text to be displayed.
      @param expected vector of acceptable tokens, if available.
    */
  function yyerror ($message, $expected = null) {
         
    if ($expected != null ) {
      echo "$message expecting";
      foreach($expected as $e) {
        echo " $e\n";
       }
    } else {
       echo $message;
    }
  }

  /** debugging support, requires the package jay.yydebug.
      Set to null to suppress debugging messages.
    */
  //protected jay.yydebug.yyDebug yydebug;


  /** index-checked interface to yyName[].
      @param token single character or %token value.
      @return token name or [illegal] or [unknown].
    */
  function yyname ($token) {
    if ($token < 0 || $token >  count($this->yyNameyyName)) return "[illegal]";
    if (($name = $this->yyName[$token]) != null) return $name;
    return "[unknown]";
  }

  /** computes list of expected tokens on error by tracing the tables.
      @param state for which to compute the list.
      @return list of token names.
    */
  function yyExpecting ($state) {
    $len = 0;
    $ok = array();;//new boolean[YyNameClass.yyName.length];

    if (($n =  $this->yySindex[$state]) != 0) {
       $start = $n;
       if ($start < 0) { $start = 0; }       
      for ($token = $start;
           $token < count($this->yyName) && $n+$token < count($this->yyTable); $token++) {
        if ($this->yyCheck[$n+$token] == $token && !$ok[$token] && 
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
           $n+$token <  count($this->yyTable); $token++) {
        if ($this->yyCheck[$n+$token] == $token && !$ok[$token] 
           && $this->yyName[$token] != null) {
          $len++;
          $ok[$token] = true;
        }
       } // end for
    }
    $result = array();
    for ($n = $token = 0; $n < $len;  $token++) {
      if ($ok[$token]) { $result[$n++] =$this->yyName[$token];
     }
    return $result;
  }

  /** the generated parser, with debugging messages.
      Maintains a state and a value stack, currently with fixed maximum size.
      @param yyLex scanner.
      @param yydebug debug message writer implementing yyDebug, or null.
      @return result of the last reduction, if any.
      @throws yyException on irrecoverable parse error.
    */
  function  yyparse ($yyLex, $yydebug){
    $this->yydebug = $yydebug;
    return $this->yyparse($yyLex);
  }

  /** initial size and increment of the state/value stack [default 256].
      This is not final so that it can be overwritten outside of invocations
      of yyparse().
    */
  var $yyMax;

  /** executed at the beginning of a reduce action.
      Used as $$ = yyDefault($1), prior to the user-specified action, if any.
      Can be overwritten to provide deep copy, etc.
      @param first value for $1, or null.
      @return first.
    */
  function yyDefault ($first) {
    return $first;
  }

  /** the generated parser.
      Maintains a state and a value stack, currently with fixed maximum size.
      @param yyLex scanner.
      @return result of the last reduction, if any.
      @throws yyException on irrecoverable parse error.
    */
  function yyparse ($yyLex) {
    if ($this->yyMax <= 0) $this->yyMax = 256;			// initial size
    $this->yyState = 0;
    $this->yyStates = array();
    $this->yyVal = null;
    $this->yyVals = array();
    $yyToken = -1;					// current input
    $yyErrorFlag = 0;				// #tks to shift

    //yyLoop: 
    for ($yyTop = 0;; $yyTop++) {
      $this->yyStates[$yyTop] = $this->yyState;
      $this->yyVals[$yyTop] = $this->yyVal;
      //if ($this->yydebug != null) yydebug.push(yyState, yyVal);

      //yyDiscarded: 
      for (;;) {	// discarding a token does not change stack
        if (($yyN = $this->yyDefRed[$this->yyState]) == 0) {	// else [default] reduce (yyN)
          if ($yyToken < 0) {
            if ($this->yyLex->advance()) {
                $yyToken = $this->yyLex->token() ;
            } else {
            $yyToken = 0;
            }
           //if ($this->yydebug != null)
            //  yydebug.lex(yyState, yyToken, yyname(yyToken), yyLex.value());
           // }
          if (($yyN = $this->yySindex[$this->yyState]) != 0 && ($yyN += $yyToken) >= 0
              && $yyN < count($this->yyTable) && $this->yyCheck[$yyN] == $yyToken) {
            //if ($yydebug != null)
              //yydebug.shift(yyState, YyTableClass.yyTable[yyN], yyErrorFlag-1);
            $this->yyState = $this->yyTable[$yyN];		// shift to yyN
            $this->yyVal = $this->yyLex->value();
            $yyToken = -1;
            if ($yyErrorFlag > 0) $yyErrorFlag--;
            break 2; // goto!!yyLoop;
          }
          if (($yyN = $this->yyRindex[$this->yyState]) != 0 && ($yyN += $yyToken) >= 0
              && $yyN < count($this->yyTable) && $this->yyCheck[$yyN] == $yyToken)
            $yyN = $this->yyTable[$yyN];			// reduce (yyN)
          else
            switch (yyErrorFlag) {
  
            case 0:
              $this->yyerror("syntax error", $this->yyExpecting($this->yyState));
              //if (yydebug != null) yydebug.error("syntax error");
  
            case 1: case 2:
              $yyErrorFlag = 3;
              do {
                if (($yyN = $this->yySindex[$this->yyStates[$yyTop]]) != 0
                    && ($yyN += $yyErrorCode) >= 0 && $yyN < count($this->yyTable)
                    && $this->yyCheck[$yyN] == $this->yyErrorCode) {
                  //if (yydebug != null)
                  //  yydebug.shift(yyStates[yyTop], YyTableClass.yyTable[yyN], 3);
                  $this->yyState = $this->yyTable[$yyN];
                  $this->yyVal = $this->yyLex->value();
                  break 3' //continue yyLoop;
                }
                //if (yydebug != null) yydebug.pop(yyStates[yyTop]);
              } while ($yyTop-- >= 0);
              //if (yydebug != null) yydebug.reject();
              $this->yyException("irrecoverable syntax error");
  
            case 3:
              if ($yyToken == 0) {
                //if (yydebug != null) yydebug.reject();
                $this->yyException("irrecoverable syntax error at end-of-file");
              }
              //if ($yydebug != null)
              //  yydebug.discard(yyState, yyToken, yyname(yyToken),
  			 //				yyLex.value());
              $yyToken = -1;
              break 1; //continue yyDiscarded;		// leave stack alone
            }
        }
        $yyV = $yyTop + 1-$this->yyLen[$yyN];
        //if (yydebug != null)
        //  yydebug.reduce(yyState, yyStates[yyV-1], yyN, YyRuleClass.yyRule[yyN], YyLenClass.yyLen[yyN]);
        $yyVal = $this->yyDefault($yyV > $yyTop ? null : $this->yyVals[$yyV]);
        switch ($yyN) {
case 1:
					// line 22 "Arith.jay"
  { $this->yyVal = $this->yyVals[-2+$yyTop] + $this->yyVals[0+$yyTop]; }
  break;
case 2:
					// line 23 "Arith.jay"
  { $this->yyVal = $this->yyVals[-2+$yyTop] - $this->yyVals[0+$yyTop]; }
  break;
case 3:
					// line 24 "Arith.jay"
  { $this->yyVal = $this->yyVals[-2+$yyTop] * $this->yyVals[0+$yyTop]; }
  break;
case 4:
					// line 25 "Arith.jay"
  { $this->yyVal = $this->yyVals[-2+$yyTop] / $this->yyVals[0+$yyTop];  }
  break;
case 5:
					// line 26 "Arith.jay"
  { $this->yyVal = $this->yyVals[0+$yyTop]; }
  break;
case 6:
					// line 27 "Arith.jay"
  { $this->yyVal = -$this->yyVals[0+$yyTop]d; }
  break;
case 7:
					// line 28 "Arith.jay"
  { $this->yyVal = $this->yyVals[-1+$yyTop]; }
  break;
case 10:
					// line 32 "Arith.jay"
  { echo "\t".$this->yyVals[-1+$yyTop]); }
  break;
case 12:
					// line 34 "Arith.jay"
  { $this->yyErrorFlag = 0; }
  break;
					// line 256 "-"
        }
        $yyTop -= $this->yyLen[$yyN];
        $this->yyState = $this->yyStates[$yyTop];
        $yyM = $this->yyLhs[$yyN];
        if ($this->yyState == 0 && $yyM == 0) {
          //if (yydebug != null) yydebug.shift(0, yyFinal);
          $this->yyState = $this->yyFinal;
          if ($yyToken < 0) {
            $yyToken = $this->yyLex->advance() ? $this->yyLex->token() : 0;
            //if (yydebug != null)
            //   yydebug.lex(yyState, yyToken,yyname(yyToken), yyLex.value());
          }
          if ($yyToken == 0) {
            //if (yydebug != null) yydebug.accept(yyVal);
            return $yyVal;
          }
          break 2; continue yyLoop;
        }
        if (($yyN = $this->yyGindex[$yyM]) != 0 && ($yyN += $this->yyState) >= 0
            && $yyN < count($this->yyTable) && $this->yyCheck[$yyN] == $this->yyState)
          $this->yyState = $this->yyTable[$yyN];
        else
          $yyState = $this->yyDgoto[$yyM];
        //if (yydebug != null) yydebug.shift(yyStates[yyTop], yyState);
	 //continue yyLoop;
      }
    }
  }

  var  $yyLhs  = array(              -1,
    1,    1,    1,    1,    1,    1,    1,    1,    0,    0,
    0,    0,
  );
  var $yyLen = array(           2,
    3,    3,    3,    3,    2,    2,    3,    1,    0,    3,
    2,    3,
  );
  var $yyDefRed = array(            9,
    0,    0,    8,    0,    0,    0,   11,    0,   12,    5,
    6,    0,    0,    0,    0,    0,   10,    7,    0,    0,
    3,    4,
  );
  var $yyDgoto  = array(             1,
    8,
  );
  var   $yySindex = array(            0,
  -10,   -7,    0,  -39,  -39,  -39,    0,   -5,    0,    0,
    0,  -19,  -39,  -39,  -39,  -39,    0,    0,  -40,  -40,
    0,    0,
  );
  var $yyRindex = array(            0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,   -2,    3,
    0,    0,
  );
  var  $yyGindex = array(    134563172,
    5,
  );
  var  $yyTable = array(             7,
    6,   15,    9,    4,   17,    5,   16,    1,   10,   11,
   12,    0,    2,    0,    0,    0,    0,   19,   20,   21,
   22,   18,   15,   13,    0,   14,    0,   16,    0,    6,
    0,    0,    4,    0,    5,    0,   15,   13,    1,   14,
    1,   16,    1,    2,    0,    2,    0,    2,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    3,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    3,    0,
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
    0,    0,    0,    0,    0,    2,
  );
 var  $yyCheck = array(            10,
   40,   42,   10,   43,   10,   45,   47,   10,    4,    5,
    6,   -1,   10,   -1,   -1,   -1,   -1,   13,   14,   15,
   16,   41,   42,   43,   -1,   45,   -1,   47,   -1,   40,
   -1,   -1,   43,   -1,   45,   -1,   42,   43,   41,   45,
   43,   47,   45,   41,   -1,   43,   -1,   45,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   99,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   99,   -1,
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
   -1,   -1,   -1,   -1,   -1,  256,
  );

  var  $yyFinal = 134563172;
 var  $yyRule = array(
    "\$accept :  prog ",
    "expr :  expr  '+'  expr ",
    "expr :  expr  '-'  expr ",
    "expr :  expr  '*'  expr ",
    "expr :  expr  '/'  expr ",
    "expr : '+'  expr ",
    "expr : '-'  expr ",
    "expr : '('  expr  ')'",
    "expr :  Number ",
    "prog :",
    "prog :  prog   expr  '\\n'",
    "prog :  prog  '\\n'",
    "prog :  prog   error  '\\n'",
  );
  var $yyName =array(    
    "end-of-file",null,null,null,null,null,null,null,null,null,"'\\n'",
    null,null,null,null,null,null,null,null,null,null,null,null,null,null,
    null,null,null,null,null,null,null,null,null,null,null,null,null,null,
    null,"'('","')'","'*'","'+'",null,"'-'",null,"'/'",null,null,null,
    null,null,null,null,null,null,null,null,null,null,null,null,null,null,
    null,null,null,null,null,null,null,null,null,null,null,null,null,null,
    null,null,null,null,null,null,null,null,null,null,null,null,null,null,
    null,null,null,null,null,null,"Number",null,null,null,null,null,null,
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
    null,null,null,null,null,null,null,null,null,null,null,"UNARY",
  );

					// line 36 "Arith.jay"
				// rest is emitted after yyparse()

  public static void main (String args []) {
    Scanner scanner = new Scanner(new InputStreamReader(System.in));
    try {
      new Arith().yyparse(scanner);
    } catch (IOException ie) { ie.printStackTrace(); }
      catch (yyException ye) { System.err.println(ye); }
  }

}				// must specify trailing } for parser

				// yyInput through separate class
class Scanner extends StreamTokenizer implements Arith.yyInput {
  public Scanner (Reader r) {
    super (new FilterReader(new BufferedReader(r)) {
      protected boolean addSpace;	// kludge to add space after \n
      public int read () throws IOException {
        int ch = addSpace ? ' ' : in.read();
        addSpace = ch == '\n';
	return ch;
      }
    });
    eolIsSignificant(true);	// need '\n'
    ordinaryChar('/');		// gotcha: would start comment
    ordinaryChar('-');		// gotcha: would start Number
    commentChar('#');		// comments from # to end-of-line
  }

  public boolean advance () throws IOException {
    return ttype != TT_EOF && super.nextToken() != TT_EOF;
  }

  public int token () {
    value = null;
    switch (ttype) {
    case TT_EOF:	return 0;	// should not happen
    case TT_EOL:	return '\n';
    case TT_NUMBER:	value = new Double(nval);
			return Arith.Number;
    case TT_WORD:	return Arith.yyErrorCode;
    default:		return ttype;
    }
  }

  protected Object value;

  public Object value () {
    return value;
  }
}
					// line 460 "-"
