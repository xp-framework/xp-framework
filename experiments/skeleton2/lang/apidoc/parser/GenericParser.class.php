<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'io.File',
    'util.Properties',
    'text.PHPTokenizer'
  );

  /**
   * Generic parser
   *
   * @purpose Parse PHP files
   */
  class GenericParser extends Object {
    public
      $file=       NULL,
      $prop=       NULL,
      $config=     'generic',
      $configured= FALSE;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string filename default NULL
     */
    public function __construct($filename= NULL) {
      $this->tokenizer= new PHPTokenizer();
      $this->prop= new Properties(dirname(__FILE__).'/parser.ini');
      if (NULL !== $filename) {
        self::setFile(new File($filename));
      }
    }
    
    /**
     * Destructor
     *
     * @access  public
     */
    public function __destruct() {
      unset($this->file);
      unset($this->tokenizer);
      unset($this->prop);
    }
    
    /**
     * Set file
     *
     * @access  public
     * @param   &io.File file
     */
    public function setFile(File $file) {
      $this->file= $file;
    }
    
    /**
     * Configure
     *
     * @access  public
     * @return  bool success
     * @throws  Exception
     */
    public function configure() {
      try {
        foreach ($this->prop->readArray('global', $this->config) as $section) {
          $this->tokenizer->addRule(
            $section, 
            $this->prop->readArray($section, 'match'),
            array(&$this, $this->prop->readString($section, 'call.func')),
            $this->prop->readArray($section, 'call.params')
          );
        }
      } catch (XPException $e) {
        throw ($e);
      }
      
      $this->configured= TRUE;
      return TRUE;
    }
    
    /**
     * Parse
     *
     * @access  public
     * @param   util.log.LogCategory CAT default NULL a log category to print debug to
     * @return  bool success
     */
    public function parse($cat= NULL) {
      try {
        if (!$this->configured) self::configure();

        $this->file->open(FILE_MODE_READ);
        $contents= $this->file->read($this->file->size());
        $this->file->close();
      } catch (XPException $e) {
        $e->printStackTrace();
        throw ($e);
      }

      $this->tokenizer->setTokens(token_get_all($contents));
      return $this->tokenizer->applyRules($cat);
    }
  }
?>
