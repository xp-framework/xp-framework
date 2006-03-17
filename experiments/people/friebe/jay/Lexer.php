<?php
/**
 * Project:     phpaspect: aspect-oriented programming for PHP
 * File:        tokenizer.class.php
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General var
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General var License for more details.
 *
 * You should have received a copy of the GNU Lesser General var
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * For questions, help, comments, discussion, etc., please join the
 * phpaspect googlegroups. Send an e-mail to
 * phpaspect@googlegroups.com
 *
 * @link http://phpaspect.org/
 * @copyright 2005.
 * @author William Candillon <wcandillon@elv.enic.fr>
 * @package phpaspect
 * @version 0.2
 */
 
/* PHPaspect tokens */
define('T_ASPECT', 374);
define('T_POINTCUT', 375);
define('T_BEFORE', 376);
define('T_AFTER', 377);
define('T_AROUND', 378);
define('T_CALL', 379);
define('T_EXECUTION', 380);
define('T_GET', 381);
define('T_SET', 382);

class AspectTokenizer extends Object {

        var $tokens;
	var $N = 0;
	var $line;
	var $pos = -1;
	var $token;
	var $value;
	var $fileName;

	//Is $this->pos is inside an HereDoc syntax ?
	var $hereDoc = false;
    var $string = false;
    //Is $this->pos is inside an OpenTagWithEcho syntax ?
    var $openTagWithEcho = false;

	var $aspectTokens = array(
        T_ASPECT => 'aspect', T_POINTCUT => 'pointcut', T_BEFORE => 'before',
		T_AFTER => 'after', T_AROUND => 'around', T_CALL => 'call',
		T_EXECUTION => 'exec', T_GET => 'get', T_SET => 'set',

        TOKEN_T_PUBLIC      => 'public',
        TOKEN_T_PRIVATE     => 'private',
        TOKEN_T_PROTECTED   => 'protected',
        TOKEN_T_ABSTRACT    => 'abstract',
        TOKEN_T_FINAL       => 'final',
        TOKEN_T_IMPLEMENTS  => 'implements',
        TOKEN_T_INTERFACE   => 'interface',
        TOKEN_T_TRY         => 'try',
        TOKEN_T_THROW       => 'throw',
        TOKEN_T_THROWS      => 'throws',
        TOKEN_T_CATCH       => 'catch',
        TOKEN_T_IMPORT      => 'import',
        TOKEN_T_PACKAGE     => 'package',
        TOKEN_T_ENUM        => 'enum',  
        TOKEN_T_OPERATOR    => 'operator',   
        TOKEN_T_VOID        => 'void',   
        TOKEN_T_CONSTRUCT   => '__construct',   
    );

	function __construct($tokens, $fileName){
		$this->tokens = $this->tokenGetAll($tokens);
		$this->N = count($this->tokens);
		$this->pos = -1;
		$this->line = 1;
		$this->fileName = $fileName;
        $GLOBALS['phpaspect_filename'] = $fileName;
        $GLOBALS['phpaspect_line'] = 1;
	}
    
    function setLine($line){
        $this->line = $line;
        $GLOBALS['phpaspect_line'] = $line;
    }
    
	function advance(){
		$this->pos++;
		//We ignore some tokens
		while($this->pos < $this->N){
			//Handle HereDoc syntax
			if($this->hereDoc === true && $this->tokens[$this->pos][0] != T_END_HEREDOC){
				return $this->update();
			}
            if($this->string === true && $this->tokens[$this->pos][0] != 34){
                return $this->update();   
            }
            //Handle OpenTagWithEcho syntax
            if($this->openTagWithEcho === true && $this->tokens[$this->pos][0] == T_CLOSE_TAG){
                return $this->update();
            }
			switch($this->tokens[$this->pos][0]){
                case 34:
                    $this->string?$this->string = false:$this->string = true;
                    return $this->update();
				case TOKEN_T_START_HEREDOC:
					$this->hereDoc = true;
					return $this->update();
				case TOKEN_T_END_HEREDOC:
					$this->hereDoc = false;
					return $this->update();
                case TOKEN_T_OPEN_TAG_WITH_ECHO:
                    $this->openTagWithEcho = true;
                    return $this->update();
				case TOKEN_T_OPEN_TAG:
				case TOKEN_T_CLOSE_TAG:
				case TOKEN_T_WHITESPACE:
				case TOKEN_T_ENCAPSED_AND_WHITESPACE:
                case TOKEN_T_COMMENT:
                    $this->setLine($this->line + substr_count($this->tokens[$this->pos][1], "\n"));
                    $this->pos++;
					continue;
				default: 
					return $this->update();
			}
		}
		return false;
	}

	function update(){
		$this->setLine($this->line + substr_count($this->tokens[$this->pos][1], "\n"));
		$this->token = $this->tokens[$this->pos][0];
		$this->value = $this->tokens[$this->pos][1];
		return true;
	}

	function parseError(){
		return sprintf("Error at line %d in file %s", $this->line, $this->fileName);
	}

	function tokenGetAll($source){
		$tokens= token_get_all($source);
        $return= array();
        $offset= 0;
        for ($id= 0, $s= sizeof($tokens); $id < $s; $id++) {
            $token= $tokens[$id];

            // Map
            if(!is_array($token)) {
				$return[$offset] = array(ord($token), $token);
                $token = $return[$offset];
			} else {
                $return[$offset]= array(
                  array_search(token_name($token[0]), $GLOBALS['_TOKEN_DEBUG']),
                  $token[1]
                );
                $token = $return[$offset];
            }
            
            
            if($token[0] == TOKEN_T_START_HEREDOC && !$this->hereDoc){
                $this->hereDoc = true;
            }elseif($token[0] == TOKEN_T_END_HEREDOC && $this->hereDoc){
                $this->hereDoc = false;
            }elseif($token[0] == 34){
                $this->string?$this->string = false:$this->string = true;
            }elseif($token[0] == TOKEN_T_STRING && !$this->string && !$this->hereDoc &&
                        isset($tokens[$id-1]) && $tokens[$id-1][0] != TOKEN_T_OBJECT_OPERATOR && $tokens[$id-1][0] != TOKEN_T_DOUBLE_COLON) {

                if ('~' == $tokens[$i= $id+ 1][0]) {
                  $classname= '';
                  while ('~' == $tokens[$i][0]) {
                    $classname.= $tokens[$i- 1][1].'~';
                    $i+= 2;
                  }
                  $return[$offset]= array(TOKEN_T_CLASSNAME, $classname.$tokens[$i- 1][1]);
                  $id= $i- 1;  // Skip tokens
                } else if ($key = array_search(strtolower($token[1]), $this->aspectTokens)) {
				  $return[$offset]= array($key, $token[1]);
                }
			}
            
            // *** echo $offset, '] ', $return[$offset][0], ' ', $GLOBALS['_TOKEN_DEBUG'][$return[$offset][0]], ' (', addcslashes($return[$offset][1], "\0..\17"), ')', "\n";
            
            $offset++;
		}
        $this->hereDoc = false;
        $this->string = false;
		return $return;
	}

    function tokenName($token){
            if($token<374){
                return $GLOBALS['_TOKEN_DEBUG'][$token];
            }else{
                return $this->aspectTokens[$token];
            }
    }
    
    function displayTokens(){
        $tokens = array();
        foreach($this->tokens as $id=>$token){
            if($token[0] >= 257){
                $tokens[] = array($this->tokenName($token[0]), $token[1]);
            }else{
                $tokens[] = array($token[0], $token[1]);
            }
        }
        return $tokens;
    }
}
?> 
