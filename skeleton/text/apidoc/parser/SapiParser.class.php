<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'text.apidoc.parser.GenericParser',
    'text.apidoc.CommentFactory'
  );
  
  /**
   * Implementation of GenericParser for sapis from within
   * the XP framework
   *
   * @deprecated
   * @see      xp://text.apidoc.parser.GenericParser
   * @purpose  Parses sapis
   */
  class SapiParser extends GenericParser {
    var 
      $config   = 'sapi',
      $comments = array(),
      $defines  = array();
    
    /**
     * Parse
     *
     * @access  public
     * @param   util.log.LogCategory CAT default NULL a log category to print debug to
     * @return  array an associative array containing comments and defines
     */
    function parse($cat= NULL) {
      $this->comments= array(
        APIDOC_COMMENT_FILE     => array(),
        APIDOC_COMMENT_CLASS    => array(),
        APIDOC_COMMENT_FUNCTION => array()
      );
      $this->defines= array();
      
      if (FALSE === parent::parse($cat)) return FALSE;
      return array(
        'comments' => $this->comments,
        'defines'  => $this->defines
      );
    }
    
    /**
     * Callback function for defines
     *
     * @access  protected
     * @param   string const
     * @param   string val
     */
    function setDefine($const, $val) {
      $this->defines[substr($const, 1, -1)]= $val;
    }

    /**
     * Callback function for the "file comment" (this is the comment at
     * the top of the file)
     *
     * @access  protected
     * @param   string str the comment's content
     */
    function setFileComment($str) {
      $comment= &CommentFactory::factory(APIDOC_COMMENT_FILE);
      $comment->fromString($str);
      $this->comments[APIDOC_COMMENT_FILE]= &$comment;
    }
    
    /**
     * Callback function for the "class comment" (this is the comment
     * right above the class declaration)
     *
     * Example:
     * <pre>
     *   // {{{ final class sapi·cli
     * </pre>
     *
     * @access  protected
     * @param   string class the class' name
     * @param   string extends what this class extends
     * @param   string str the comment's content
     */
    function setClassComment($class, $extends, $definition) {
      if (!sscanf(
        trim($definition),
        '// {{{ %s class '.$class, 
        $model
      )) {
        return throw(new FormatException('Cannot parse definition "'.trim($definition).'"'));
      }

      with ($comment= &CommentFactory::factory(APIDOC_COMMENT_CLASS)); {
        $comment->setModel($model);
        $comment->setClassName($class);
        $comment->setExtends($extends);
      }
      $this->comments[APIDOC_COMMENT_CLASS]= &$comment;
    }
    
    /**
     * Callback function for "function comments" (these are the comments
     * above a function declaration)
     *
     * Example:
     * <pre>
     *   // {{{ internal string output(string buf)
     *   //     Output handler
     * </pre>
     *
     * The first line contains the function definition, the second line
     * the documentation.
     *
     * @access  protected
     * @param   string function the function's name
     * @param   string definition
     * @param   string description
     * @param   bool returnsReference default FALSE TRUE when this function returns its value by reference
     */
    function setFunctionComment($function, $definition, $description, $returnsReference= FALSE) {
      if (!sscanf(
        trim($definition),
        '// {{{ %s %s '.$function.'(%[^)]) throws %s', 
        $access, 
        $return,
        $paramlist,
        $exceptionlist
      )) {
        return throw(new FormatException('Cannot parse definition "'.trim($definition).'"'));
      }

      with ($comment= &CommentFactory::factory(APIDOC_COMMENT_FUNCTION)); {
        $comment->text= trim(substr($description, 2));
        $comment->setAccess($access);
        $comment->setReturn($return, NULL);

        // Handle parameter list
        if ($paramlist && 'void' != $paramlist) {
          foreach (explode(',', $paramlist) as $param) {
            sscanf(trim($param), '%s %[^=]=%s', $type, $name, $default);

            if (NULL !== $default) {
              $comment->addDefaultParam($type, $name, $default, NULL);
            } else {
              $comment->addParam($type, $name, NULL);
            }
          }
        }

        // Handle exception list
        if ($exceptionlist) {
          foreach (explode(',', $exceptionlist) as $exception) {
            $comment->addThrows(trim($exception), NULL);
          }
        }
        $comment->return->reference= $returnsReference;
      }
      $this->comments[APIDOC_COMMENT_FUNCTION][$function]= &$comment;
    }
  }
?>
