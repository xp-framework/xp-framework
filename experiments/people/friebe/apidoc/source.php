<?php
  require('lang.base.php');
  xp::sapi('scriptlet.production');
  uses(
    'scriptlet.HttpScriptlet',
    'text.StringTokenizer'
  );

  define('DELIMITERS', " \$[]=<>*&();:/#'{}\"\r\n");
  
  class SourceCodeScriptlet extends HttpScriptlet {
  
    function doGet(&$request, &$response) {
      static $keywords= array(
        'static'      => 'keyword',
        'class'       => 'keyword',
        'function'    => 'keyword',
        'extends'     => 'keyword',
        'uses'        => 'keyword',
        'implements'  => 'keyword',
        'try'         => 'keyword',
        'throw'       => 'keyword',
        'catch'       => 'keyword',
        'if'          => 'keyword',
        'else'        => 'keyword',
        'continue'    => 'keyword',
        'return'      => 'keyword',
        'for'         => 'keyword',
        'foreach'     => 'keyword',
        'while'       => 'keyword',
        'do'          => 'keyword',
        'break'       => 'keyword',
        'switch'      => 'keyword',
        'case'        => 'keyword',
        'default'     => 'keyword',
        'var'         => 'keyword',
        'array'       => 'keyword',
        'new'         => 'keyword',
        'deref'       => 'keyword',
        'raise'       => 'keyword',
        'deref'       => 'keyword',
        '__construct' => 'special',
        '__static'    => 'special',
        '__destruct'  => 'special',
        'parent'      => 'special',
        'self'        => 'special',   // Forward-compatible:)
      );
      
      if (0 == sscanf($request->getQueryString(), '%[^<[]', $class) && $class) {
        return throw(new HttpScriptletException(htmlspecialchars($class).' does not exist!', HTTP_NOT_FOUND));
      }
      
      $cl= &ClassLoader::getDefault();
      $tokenizer= &new StringTokenizer($cl->loadClassBytes($class), DELIMITERS, TRUE);
      
      $response->write('<style type="text/css">
        code {
          white-space: pre;
        }
        .string {
          color: blue;
        }
        .delimiter {
          color: red;
        }
        .keyword {
          color: #990000;
          font-weight: bold;
        }
        .special {
          color: #444444;
          font-weight: bold;
        }
        .variable {
          color: #000099;
          font-weight: bold;
        }
        .comment {
          color: #666666;
        }
        .annotation {
          color: #ee6600;
        }
      </style>');

      $response->write('<code>');
      $prev= NULL;
      do {
        $token= $tokenizer->nextToken(DELIMITERS);
        if ("'" == $token{0}) {
          $type= 'string';
          $t= $tokenizer->nextToken("'");
          $value= "'".$t;
          if ("'" != $t) {
            do {
              $value.= $tokenizer->nextToken("'");
              if (substr($value, -2, 1) != '\\') break;    // Escaped char
              
              $value.= $tokenizer->nextToken("'");
            } while ($tokenizer->hasMoreTokens());
          }
        } else if ('"' == $token{0}) {
          $type= 'string';
          $t= $tokenizer->nextToken('"');
          $value= '"'.$t;
          if ('"' != $t) {
            do {
              $value.= $tokenizer->nextToken('"');
              if (substr($value, -2, 1) != '\\') break;    // Escaped char
              
              $value.= $tokenizer->nextToken('"');
            } while ($tokenizer->hasMoreTokens());
          }
        } else if ('$' == $token{0}) {
          $type= 'variable';
          $value= '$'.$tokenizer->nextToken(DELIMITERS);
        } else if ('#' == $token{0}) {
          $type= 'annotation';
          $value= '#'.$tokenizer->nextToken("\r\n");
        } else if ('/' == $token{0}) {
          $t= $tokenizer->nextToken(DELIMITERS);
          if ('/' == $t) {
            $type= 'comment';
            $value= '//'.$tokenizer->nextToken("\r\n");
          } else if ('*' == $t) {
            $type= 'comment';
            $value= '/*';
            do {
              $value.= $tokenizer->nextToken('/');
            } while ($tokenizer->hasMoreTokens() && substr($value, -1, 1) != '*');
            $value.= $tokenizer->nextToken('/');
          } else {
            $type= 'word';
            $value= $token.$t;
          }
        } else if (FALSE !== strpos(DELIMITERS, $token) && 1 == strlen($token)) {
          $type= 'delimiter';
          $value= $token;
        } else if (isset($keywords[$token])) {
          $type= $keywords[$token];
          $value= $token;
        } else {
          $type= 'word';
          $value= $token;
        }
        
        if ($prev != $type) {
          $prev && $response->write('</span>');
          $response->write('<span class="'.$type.'">');
          $prev= $type;
        }
        
        $response->write(htmlspecialchars($value));
      } while ($tokenizer->hasMoreTokens());
      
      $response->write('</span>');
      $response->write('</code>');
    }
  }


  scriptlet::run(new SourceCodeScriptlet());  
?>
