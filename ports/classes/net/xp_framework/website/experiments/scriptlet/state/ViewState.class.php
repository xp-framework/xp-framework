<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'scriptlet.xml.workflow.AbstractState', 
    'util.PropertyManager',
    'io.collections.FileCollection',
    'io.FileUtil',
    'io.File',
    'text.doclet.markup.MarkupBuilder',
    'util.MimeType',
    'io.streams.FileInputStream',
    'text.StreamTokenizer'
  );

  /**
   * Handles /xml/view
   *
   * @purpose  State
   */
  class ViewState extends AbstractState {
  
    /**
     * Constructor
     *
     */
    public function __construct() {
      XSLCallback::getInstance()->registerInstance('view', $this);
    }
    
    /**
     * Return this element's contents
     *
     * @return  string
     */
    #[@xslmethod]
    public function contents() {
      return FileUtil::getContents($this->element);
    }

    /**
     * Return this element's markup
     *
     * @return  string
     */
    #[@xslmethod]
    public function markup() {
      $builder= new MarkupBuilder();
      $d= new DomDocument();
      $markup= '<markup><p>'.$builder->markupFor(FileUtil::getContents($this->element)).'</p></markup>';
      if (FALSE === $d->loadXML(utf8_encode($markup))) {
        throw new FormatException(libxml_get_last_error()->message.' @ '.$this->element->getFileName().': '.htmlspecialchars($markup));
      }
      return $d;
    }
    
    /**
     * Process one-line comments
     *
     * @param   text.Tokenizer st
     * @return  string
     */
    public function oneLineComment($st) {
      return $st->nextToken("\r\n");
    }

    /**
     * Process multi-line comments
     *
     * @param   text.Tokenizer st
     * @param   string end
     * @return  string
     */
    public function multiLineComment($st, $end) {
      $str= '';
      $stop= $end{strlen($end)- 1};
      $ends= substr($end, 0, -1);
      while ($st->hasMoreTokens()) {
        $t= $st->nextToken($stop);
        if ($stop == $t && $ends === substr($str, -1 * strlen($ends))) break;
        $str.= $t;
      }
      return $str.$stop;
    }

    /**
     * Process variables
     *
     * @param   text.Tokenizer st
     * @param   string delim
     * @return  string
     */
    public function variable($st, $delim) {
      return $st->nextToken($delim);
    }

    /**
     * Process quoted strings
     *
     * @param   text.Tokenizer st
     * @param   string delim
     * @return  string
     */
    public function quotedString($st, $delim) {
      $str= '';
      while ($st->hasMoreTokens()) {
        $t= $st->nextToken($delim);
        if ($delim == $t && '\\' != $str{strlen($str)- 1}) break;
        $str.= $t;
      }
      return $str.$delim;
    }

    /**
     * Return this element's contents
     *
     * @param   string language
     * @return  php.DomDocument
     */
    #[@xslmethod]
    public function highlight($language) {
      $delim= "#.,:=;+-*/(){}[]\$%\"'\r\n \t";
      $keywords= array(
        'perl' => array(
          'keywords' => array(
            'for'       => 'keyword',
            'foreach'   => 'keyword', 
            'sub'       => 'keyword',
            'shift'     => 'keyword',
            'return'    => 'keyword',
            'package'   => 'keyword',
            'use'       => 'keyword',
            'if'        => 'keyword',
            'else'      => 'keyword',
            '{'         => 'bracket',
            '}'         => 'bracket',
          ),
          'tokens'    => array(
          ),
          'states'    => array(
            '#' => array('comment', 'oneLineComment', array()),
            '$' => array('variable', 'variable', array($delim)),
            '"' => array('string', 'quotedString', array('"')),
            "'" => array('string', 'quotedString', array("'")),
          )
        ),
        'php' => array(
          'keywords' => array(
            'for'       => 'keyword',
            'while'     => 'keyword',
            'do'        => 'keyword',
            'foreach'   => 'keyword', 
            'new'       => 'keyword',
            'class'     => 'keyword',
            'interface' => 'keyword',
            'return'    => 'keyword',
            'extends'   => 'keyword',
            'implements'=> 'keyword',
            'uses'      => 'keyword',
            'function'  => 'keyword',
            'if'        => 'keyword',
            'else'      => 'keyword',
            'switch'    => 'keyword',
            'case'      => 'keyword',
            'default'   => 'keyword',
            'break'     => 'keyword',
            'continue'  => 'keyword',
            'try'       => 'keyword',
            'catch'     => 'keyword',
            'throw'     => 'keyword',
            'static'    => 'modifier',
            'abstract'  => 'modifier',
            'public'    => 'modifier',
            'private'   => 'modifier',
            'protected' => 'modifier',
            'final'     => 'modifier',
            '{'         => 'bracket',
            '}'         => 'bracket',
          ),
          'tokens'    => array(
            '/'  => array('/* ', array('//' => TRUE, '/*' => TRUE)),
          ),
          'states'    => array(
            '//' => array('comment', 'oneLineComment', array()),
            '/*' => array('comment', 'multiLineComment', array('*/')),
            '$'  => array('variable', 'variable', array($delim)),
            '"'  => array('string', 'quotedString', array('"')),
            "'"  => array('string', 'quotedString', array("'")),
          ),
        ),
        'java' => array(
          'keywords' => array(
            'import'    => 'keyword',
            'package'   => 'keyword',
            'for'       => 'keyword',
            'while'     => 'keyword',
            'do'        => 'keyword',
            'foreach'   => 'keyword', 
            'new'       => 'keyword',
            'class'     => 'keyword',
            'interface' => 'keyword',
            'return'    => 'keyword',
            'extends'   => 'keyword',
            'implements'=> 'keyword',
            'if'        => 'keyword',
            'else'      => 'keyword',
            'switch'    => 'keyword',
            'case'      => 'keyword',
            'default'   => 'keyword',
            'break'     => 'keyword',
            'continue'  => 'keyword',
            'try'       => 'keyword',
            'catch'     => 'keyword',
            'throw'     => 'keyword',
            'finally'   => 'keyword',
            'void'      => 'keyword',
            'volatile'  => 'modifier',
            'native'    => 'modifier',
            'static'    => 'modifier',
            'abstract'  => 'modifier',
            'public'    => 'modifier',
            'private'   => 'modifier',
            'protected' => 'modifier',
            'final'     => 'modifier',
            '{'         => 'bracket',
            '}'         => 'bracket',
          ),
          'tokens'    => array(
            '/'  => array('/* ', array('//' => TRUE, '/*' => TRUE)),
          ),
          'states'    => array(
            '//' => array('comment', 'oneLineComment', array()),
            '/*' => array('comment', 'multiLineComment', array('*/')),
            '"'  => array('string', 'quotedString', array('"')),
            "'"  => array('string', 'quotedString', array("'")),    // char
          ),
        ),
        'javascript' => array(
          'keywords' => array(
            'for'       => 'keyword',
            'while'     => 'keyword',
            'do'        => 'keyword',
            'function'  => 'keyword',
            'if'        => 'keyword',
            'else'      => 'keyword',
            'switch'    => 'keyword',
            'case'      => 'keyword',
            'default'   => 'keyword',
            'break'     => 'keyword',
            'continue'  => 'keyword',
            'return'    => 'keyword',
            'var'       => 'keyword',
            'typeof'    => 'keyword',
            'in'        => 'keyword',
            'try'       => 'keyword',
            'catch'     => 'keyword',
            'throw'     => 'keyword',
            'finally'   => 'keyword',
            '{'         => 'bracket',
            '}'         => 'bracket',
          ),
          'tokens'    => array(
            '/'  => array('/* ', array('//' => TRUE, '/*' => TRUE)),
          ),
          'states'    => array(
            '//' => array('comment', 'oneLineComment', array()),
            '/*' => array('comment', 'multiLineComment', array('*/')),
            '"'  => array('string', 'quotedString', array('"')),
            "'"  => array('string', 'quotedString', array("'")),    // char
          ),
        ),
        'c' => array(
          'keywords' => array(
            'for'       => 'keyword',
            'while'     => 'keyword',
            'do'        => 'keyword',
            'if'        => 'keyword',
            'else'      => 'keyword',
            'switch'    => 'keyword',
            'case'      => 'keyword',
            'default'   => 'keyword',
            'break'     => 'keyword',
            'continue'  => 'keyword',
            'void'      => 'keyword',
            '{'         => 'bracket',
            '}'         => 'bracket',
          ),
          'tokens'    => array(
            '/'  => array('/* ', array('//' => TRUE, '/*' => TRUE)),
          ),
          'states'    => array(
            '#'  => array('variable', 'oneLineComment', array()),   // preprocessor
            '//' => array('comment', 'oneLineComment', array()),
            '/*' => array('comment', 'multiLineComment', array('*/')),
            '"'  => array('string', 'quotedString', array('"')),
            "'"  => array('string', 'quotedString', array("'")),    // char
          ),
        ),
        'csharp' => array(
          'keywords' => array(
            'using'     => 'keyword',
            'namespace' => 'keyword',
            'for'       => 'keyword',
            'while'     => 'keyword',
            'do'        => 'keyword',
            'foreach'   => 'keyword', 
            'new'       => 'keyword',
            'class'     => 'keyword',
            'interface' => 'keyword',
            'return'    => 'keyword',
            'extends'   => 'keyword',
            'implements'=> 'keyword',
            'if'        => 'keyword',
            'else'      => 'keyword',
            'switch'    => 'keyword',
            'case'      => 'keyword',
            'default'   => 'keyword',
            'break'     => 'keyword',
            'continue'  => 'keyword',
            'try'       => 'keyword',
            'catch'     => 'keyword',
            'throw'     => 'keyword',
            'void'      => 'keyword',
            'static'    => 'modifier',
            'abstract'  => 'modifier',
            'public'    => 'modifier',
            'private'   => 'modifier',
            'protected' => 'modifier',
            'final'     => 'modifier',
            '{'         => 'bracket',
            '}'         => 'bracket',
          ),
          'tokens'    => array(
            '/'  => array('/* ', array('//' => TRUE, '/*' => TRUE)),
          ),
          'states'    => array(
            '//' => array('comment', 'oneLineComment', array()),
            '/*' => array('comment', 'multiLineComment', array('*/')),
            '"'  => array('string', 'quotedString', array('"')),
            "'"  => array('string', 'quotedString', array("'")),    // char
          ),
        ),

      );
      
      $out= '';
      $current= NULL;
      $st= new StreamTokenizer(new FileInputStream($this->element), $delim, TRUE);
      while ($st->hasMoreTokens()) {
        $token= $st->nextToken();

        if (isset($keywords[$language]['tokens'][$token])) {
          $combined= $keywords[$language]['tokens'][$token];
          $next= $st->nextToken($combined[0]);
          if (isset($combined[1][$token.$next])) {
            $token.= $next;
          } else {
            $st->pushBack($next);
          }
        }

        if (isset($keywords[$language]['keywords'][$token])) {
          $class= $keywords[$language]['keywords'][$token];
        } else if (isset($keywords[$language]['states'][$token])) {
          $state= $keywords[$language]['states'][$token];
          $class= $state[0];
          $token.= call_user_func_array(array($this, $state[1]), array_merge(array($st), $state[2]));
        } else if (is_numeric($token)) {
          $class= 'number';
        } else {
          $class= 'default';
        }

        if ($current != $class) {
          $out.= '</span><span class="'.$class.'">';
          $current= $class;
        }
        $out.= htmlspecialchars(utf8_encode($token));
      }
      
      $d= new DomDocument();
      $d->loadXML('<code>'.substr($out, 7).($current ? '</span>' : '').'</code>');
      return $d;
    }

    /**
     * Process this state.
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   scriptlet.xml.XMLScriptletResponse response
     */
    public function process($request, $response) {
      $path= $request->getQueryString();

      $prop= PropertyManager::getInstance()->getProperties('storage');
      $this->element= new File($prop->readString('storage', 'base'), strtr($path, array(
        ','   => DIRECTORY_SEPARATOR, 
        '..'  => ''
      )));
      $n= $response->addFormResult(new Node('element', NULL, array(
        'path' => substr($path, 0, strrpos($path, ',')),
        'mime' => MimeType::getByFilename($this->element->getUri())
      )));
      $n->addChild(new Node('name', $this->element->getFileName()));
    }
  }
?>
