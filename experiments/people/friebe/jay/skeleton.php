#	jay skeleton

#	character in column 1 determines outcome...
#		# is a comment
#		. is copied
#		t is copied as //t if -t is set
#	other lines are interpreted to call jay procedures

.<?php
. // created by jay 0.8 (c) 1998 Axel.Schreiner@informatik.uni-osnabrueck.de
. // modified by alan@akbkhome.com to try to generate php!
. // modified by cellog@users.sourceforge.net to fit PEAR CS
. // %token constants
.
. require_once 'PEAR.php';
.
 tokens var
.
. // Class now
.
 prolog		## %{ ... %} prior to the first %%
.
.    /**
.     * thrown for irrecoverable syntax errors and stack overflow.
.     */
.    
.     var $yyErrorCode = 256;
.
.    /**
.     * Debugging
.     */
.     var $debug = false;
.
.    /**
.     * (syntax) error message.
.     * Can be overwritten to control message format.
.     * @param message text to be displayed.
.     * @param expected vector of acceptable tokens, if available.
.     */
.    function raiseError ($message, $expected = null)
.    {     
.        if ($expected !== null ) {
.            $m = "$message expecting";
.            foreach($expected as $e) {
.                $m .= " $e";
.            }
.        } else {
.            $m = $message;
.        }
.        return PEAR::raiseError($message);  
.    }
.
.
.
.    /**
.     * index-checked interface to yyName[].
.     * @param token single character or %token value.
.     * @return token name or [illegal] or [unknown].
.     */
.    function yyname ($token) {
.        if ($token < 0 || $token >  count($GLOBALS[$this->yyGlobalName]['yyName'])) return "[illegal]";
.        if (($name = $GLOBALS[$this->yyGlobalName]['yyName'][$token]) != null) return $name;
.        return "[unknown]";
.    }
.
.    /**
.     * computes list of expected tokens on error by tracing the tables.
.     * @param state for which to compute the list.
.     * @return list of token names.
.     */
.    function yyExpecting ($state) {
.        $len = 0;
.        $ok = array();//new boolean[YyNameClass.yyName.length];
.
.        if (($n =  $GLOBALS[$this->yyGlobalName]['yySindex'][$state]) != 0) {
.            $start = $n;
.            if ($start < 0) { $start = 0; }       
.            for ($token = $start;
.                $token < count($GLOBALS[$this->yyGlobalName]['yyName']) && 
.                        $n+$token < count($GLOBALS[$this->yyGlobalName]['yyTable']); $token++) {
.                if (@$GLOBALS[$this->yyGlobalName]['yyCheck'][$n+$token] == $token && !@$ok[$token] && 
.                        $GLOBALS[$this->yyGlobalName]['yyName'][$token] != null) {
.                    $len++;
.                    $ok[$token] = true;
.                }
.            } // end for
.        }
.        if (($n = $GLOBALS[$this->yyGlobalName]['yyRindex'][$state]) != 0) {
.            $start = $n;
.            if ($start < 0) { $start = 0; }       
.            for ($token = $start;
.                     $token < count($GLOBALS[$this->yyGlobalName]['yyName'])  && 
.                     $n+$token <  count($GLOBALS[$this->yyGlobalName]['yyTable']); $token++) 
.            {
.               if (@$GLOBALS[$this->yyGlobalName]['yyCheck'][$n+$token] == $token && !@$ok[$token] 
.                          && @$GLOBALS[$this->yyGlobalName]['yyName'][$token] != null) {
.                    $len++;
.                    $ok[$token] = true;
.               }
.            } // end for
.        }
.        $result = array();
.        for ($n = $token = 0; $n < $len;  $token++) {
.            if (@$ok[$token]) { $result[$n++] =$GLOBALS[$this->yyGlobalName]['yyName'][$token]; }
.        }
.        return $result;
.    }
.
.
.    /**
.     * initial size and increment of the state/value stack [default 256].
.     * This is not final so that it can be overwritten outside of invocations
.     * of yyparse().
.     */
.    var $yyMax;
.
.    /**
.     * executed at the beginning of a reduce action.
.     * Used as $$ = yyDefault($1), prior to the user-specified action, if any.
.     * Can be overwritten to provide deep copy, etc.
.     * @param first value for $1, or null.
.     * @return first.
.     */
.    function yyDefault ($first) {
.        return $first;
.    }
.
.    /**
.     * the generated parser.
.     * Maintains a state and a value stack, currently with fixed maximum size.
.     * @param yyLex scanner.
.     * @return result of the last reduction, if any.
.     * @throws yyException on irrecoverable parse error.
.     */
.    function yyparse (&$yyLex) {
t        $this->debug = true;
.        $this->yyLex = &$yyLex;
.        if (!$this->yyGlobalName) {
.            echo "\n\nYou must define \$this->yyGlobalName to match the build option -g _XXXXX \n\n";
.            exit;
.        }
.        if ($this->debug)
.           echo "\tStarting jay:yyparse";
.        //error_reporting(E_ALL);
.        if ($this->yyMax <= 0) $this->yyMax = 256;			// initial size
.        $this->yyState = 0;
.        $this->yyStates = array();
.        $this->yyVal = null;
.        $this->yyVals = array();
.        $yyTableCount = count($GLOBALS[$this->yyGlobalName]['yyTable']);
.        $yyToken = -1;                 // current input
.        $yyErrorFlag = 0;              // #tks to shift
.        $tloop = 0;
 local		## %{ ... %} after the first %%

.    
.        while (1) {//yyLoop: 
.            //echo "yyLoop\n";
.            //if ($this->debug) echo "\tyyLoop:\n";
.            for ($yyTop = 0;; $yyTop++) {
.                //if ($this->debug) echo ($tloop++) .">>>>>>yyLoop:yTop = {$yyTop}\n";
.                $this->yyStates[$yyTop] = $this->yyState;
.                $this->yyVals[$yyTop] = $this->yyVal;
.
.                //yyDiscarded: 
.                for (;;) {	// discarding a token does not change stack
.                    //echo "yyDiscarded\n";
.                    if ($this->debug) echo "\tIn main loop : State = {$this->yyState}\n";
.                    if ($this->debug) echo "\tyydefred = {$GLOBALS[$this->yyGlobalName]['yyDefRed'][$this->yyState]}\n";
.                    if (($yyN = $GLOBALS[$this->yyGlobalName]['yyDefRed'][$this->yyState]) == 0) {	
.                        // else [default] reduce (yyN)
.                        //if ($this->debug) echo "\tA:token is $yyToken\n";
.                        if ($yyToken < 0) {
.                            //if ($this->debug) echo "\tA:advance\n";
.                            if ($yyLex->advance()) {
.                               
.                                $yyToken = $yyLex->token ;
.                            } else {
.                                $yyToken = 0;
.                            }
.                        }
.                        if ($this->debug) {
.                            echo "\tA:token is now " .
.                            "{$GLOBALS[$this->yyGlobalName]['yyName'][$yyToken]} " .token_name($yyToken).  "\n";
.                            var_dump($yyToken);
.                        }
.                        //if ($this->debug) echo "GOT TOKEN $yyToken";
.                        //if ($this->debug) echo "Sindex:  {$GLOBALS[$this->yyGlobalName]['yySindex'][$this->yyState]}\n";
.
.                        if (($yyN = $GLOBALS[$this->yyGlobalName]['yySindex'][$this->yyState]) != 0
.                                  && ($yyN += $yyToken) >= 0
.                                  && $yyN < $yyTableCount && $GLOBALS[$this->yyGlobalName]['yyCheck'][$yyN] == $yyToken) {
.                            $this->yyState = $GLOBALS[$this->yyGlobalName]['yyTable'][$yyN];		// shift to yyN
.                            $this->yyVal = $yyLex->value;
.                            $yyToken = -1;
.                            if ($yyErrorFlag > 0) $yyErrorFlag--;
.                            continue 2; // goto!!yyLoop;
.                        }
. 
.                       
.              
.                        if (($yyN = $GLOBALS[$this->yyGlobalName]['yyRindex'][$this->yyState]) != 0
.                                && ($yyN += $yyToken) >= 0
.                                && $yyN < $yyTableCount && $GLOBALS[$this->yyGlobalName]['yyCheck'][$yyN] == $yyToken) {
.                            $yyN = $GLOBALS[$this->yyGlobalName]['yyTable'][$yyN];			// reduce (yyN)
.                        } else {
.                            switch ($yyErrorFlag) {
.    
.                                case 0:
.                                    $info = $yyLex->parseError();
.                                    $info .= ', Unexpected '.$this->yyName($yyToken).',';
.                                    return $this->raiseError("$info syntax error",
.                                                $this->yyExpecting($this->yyState));
.                                
.                                case 1: case 2:
.                                    $yyErrorFlag = 3;
.                                    do { 
.                                        if (($yyN = @$GLOBALS[$this->yyGlobalName]['yySindex']
.                                                [$this->yyStates[$yyTop]]) != 0
.                                                && ($yyN += $this->yyErrorCode) >= 0 && $yyN < $yyTableCount
.                                                && $GLOBALS[$this->yyGlobalName]['yyCheck'][$yyN] == $this->yyErrorCode) {
.                                            $this->yyState = $GLOBALS[$this->yyGlobalName]['yyTable'][$yyN];
.                                            $this->yyVal = $yyLex->value;
.                                            //vi /echo "goto yyLoop?\n";
.                                            break 3; //continue yyLoop;
.                                        }
.                                    } while ($yyTop-- >= 0);
.                                    $info = $yyLex->parseError();
.                                    return $this->raiseError("$info irrecoverable syntax error");
.    
.                                case 3:
.                                    if ($yyToken == 0) {
.                                        $info =$yyLex->parseError();
.                                        return $this->raiseError("$info irrecoverable syntax error at end-of-file");
.                                    }
.                                    $yyToken = -1;
.                                    //echo "goto yyDiscarded?";  
.                                    break 1; //continue yyDiscarded;		// leave stack alone
.                            }
.                        }
.                    }    
.                    $yyV = $yyTop + 1-$GLOBALS[$this->yyGlobalName]['yyLen'][$yyN];
.                    //if ($this->debug) echo "\tyyV is $yyV\n";
.                    $yyVal = $yyV > $yyTop ? null : $this->yyVals[$yyV];
.                    //if ($this->debug) echo "\tyyVal is ". serialize($yyVal) ."\n";
.                    if ($this->debug) echo "\tswitch($yyN)\n";
.                   
.                    $method = '_' .$yyN;
.                    if (method_exists($this,$method)) {
.                         $this->$method($yyTop);
.
.                    }
.                   
.                    //if ($this->debug) echo "\tDONE switch\n";if ($this->debug) echo "\t--------------\n";
.                    $yyTop -= $GLOBALS[$this->yyGlobalName]['yyLen'][$yyN];
.                    //if ($this->debug) echo "\tyyTop is $yyTop\n";
.                    $this->yyState = $this->yyStates[$yyTop];
.                    //if ($this->debug) echo "\tyyState is {$this->yyState}\n";
.                    $yyM = $GLOBALS[$this->yyGlobalName]['yyLhs'][$yyN];
.                    //if ($this->debug) echo "\tyyM is now $yyM\n";
.
.
.
.                    if ($this->yyState == 0 && $yyM == 0) {
.                        $this->yyState = $GLOBALS[$this->yyGlobalName]['yyFinal'];
.                        if ($yyToken < 0) {
.                            $yyToken =0;
.                            if ($yyLex->advance()) {
.                                $yyToken = $yyLex->token;
.                            }
.                        }
.                        if ($this->debug) echo "\tTOKEN IS NOW $yyToken\n";
.                        if ($yyToken == 0) {
.                            return $yyVal;
.                        }
.                        //if ($this->debug) echo "\t>>>>> yyLoop(A)?\n";
.                        continue 2; //continue yyLoop;
.                    }
.                    if (($yyN = $GLOBALS[$this->yyGlobalName]['yyGindex'][$yyM]) != 0 && ($yyN += $this->yyState) >= 0
.                            && $yyN < $yyTableCount && $GLOBALS[$this->yyGlobalName]['yyCheck'][$yyN] == $this->yyState) {
.                        //if ($this->debug) echo "\tyyState: using yyTable\n";
.                        $this->yyState = $GLOBALS[$this->yyGlobalName]['yyTable'][$yyN];
.                    } else {
.                        //if ($this->debug) echo "\tyyState: using yyDgoto\n";
.                        $this->yyState = $GLOBALS[$this->yyGlobalName]['yyDgoto'][$yyM];
.                    }  
.                    //if ($this->debug) echo "\t>>>>> yyLoop(B)?\n";
.                    continue 2;//continue yyLoop;
.                }
.            }
.        }
.    }
.
 actions		## code from the actions within the grammar
.
 epilog			## text following second %%
.
 tables			## tables for rules, default reduction, and action calls
.
 debug			## tables for debugging support
. ?>
