<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Highlights PHP syntax
   *
   * Example:
   * <code>
   *   $p= new PHPSyntaxHighlighter(new File(__FILE__));
   *   echo $p->getHighlight();
   * </code>
   *
   * @see php://highlight_string
   */
  class PHPSyntaxHighlighter extends Object {
  
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed input default NULL a string or a file object
     */
    public function __construct($input= NULL) {
      if (is_a($input, 'File')) {
        self::setFile($input);
      } else {
        self::setSource($input);
      }
      
      // Set some defaults
      self::setStyle('string',  'color: darkblue');
      self::setStyle('comment', 'color: gray');
      self::setStyle('keyword', 'color: darkred; font-weight: bold');
      self::setStyle('default', 'color: black');
      self::setStyle('html',    'color: lightgray');
      self::setStyle('variable','color: darkblue; font-weight: bold');
      
    }
    
    /**
     * Sets style for keywords
     * Keywords are: string, comment, keyword, default, html, variable
     *
     * @access  public
     * @param   string what one of the keywords listed above
     * @param   string style anything which will work within style="??????"
     */
    public function setStyle($what, $style) {
      $this->styles[$what]= $style;
      ini_set('highlight.'.$what, $style);
    }
    
    /**
     * Sets style for strings
     *
     * @access  public
     * @param   string style
     * @see     #setStyle
     */
    public function setStringStyle($style) {
      self::setStyle('string', $style);
    }

    /**
     * Sets style for comments
     *
     * @access  public
     * @param   string style
     * @see     #setStyle
     */
    public function setCommentStyle($style) {
      self::setStyle('comment', $style);
    }
  
    /**
     * Sets style for keywords
     *
     * @access  public
     * @param   string style
     * @see     #setStyle
     */
    public function setKeywordStyle($style) {
      self::setStyle('keyword', $style);
    }
    
    /**
     * Sets default Style
     *
     * @access  public
     * @param   string Style
     * @see     #setStyle
     */
    public function setDefaultStyle($style) {
      self::setStyle('default', $style);
    }

    /**
     * Sets style for HTML 
     *
     * @access  public
     * @param   string style
     * @see     #setStyle
     */
    public function setHtmlStyle($style) {
      self::setStyle('html', $style);
    }

    /**
     * Sets sourcecode string to higlight. Will require the leading
     * <?php and an ?> at the end.
     *
     * @access  publuc
     * @param   string source 
     */
    public function setSource($source) {
      $this->source= $source;
    }

    /**
     * Sets file to highlight
     *
     * @access  public
     * @param   io.File file
     * @throws  IOException
     */    
    public function setFile(&$file) {
      $file->open(FILE_MODE_READ);
      $this->source= $file->read($file->size());
      $file->close();
    }
    
    /**
     * Retrieve highlighted code. Will be XML-conform since &nbsp;
     * is replaced by &#160;. The deprecated <font>-Tag is replaced
     * by 
     *
     * @access  public
     * @return  string highlighted source
     */
    public function getHighlight() {
      ob_start();
      highlight_string($this->source);
      $s= ob_get_contents();
      ob_end_clean();
      return preg_replace(
        array(
          ',&nbsp;,', 
          ',<font color="([^"]+)">,', 
          ',</font>,',
          ',\$[a-z0-9_]+,i',
          ',\b(uses|implements|is|try|catch|throw|finally)\b,'
        ),
        array(
          '&#160;', 
          '<span style="$1">', 
          '</span>',
          '<span style="'.$this->styles['variable'].'">$0</span>',
          '<span style="'.$this->styles['keyword'].'">$1</span>'
        ),
        $s
      );
    }
  }
?>
