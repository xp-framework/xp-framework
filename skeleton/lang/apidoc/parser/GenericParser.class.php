<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'io.File', 
    'util.Properties', 
    'util.text.PHPTokenizer'
  );

  /**
   * Generic parser
   *
   * @purpose Parse PHP files
   */
  class GenericParser extends Object {
    var 
      $file=       NULL,
      $prop=       NULL,
      $config=     'generic',
      $configured= FALSE;
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function __construct($filename= NULL) {
      $this->tokenizer= &new PHPTokenizer();
      $this->prop= &new Properties(dirname(__FILE__).'/parser.ini');
      if (NULL !== $filename) {
        $this->setFile(new File($filename));
      }
      parent::__construct();
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function __destruct() {
      $this->file->__destruct();
      $this->tokenizer->__destruct();
      $this->prop->__destruct();
      parent::__destruct();
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setFile($file) {
      $this->file= $file;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function configure() {
      try(); {
	    foreach ($this->prop->readArray('global', $this->config) as $section) {
          $this->tokenizer->addRule(
            $section, 
            $this->prop->readArray($section, 'match'),
            array(&$this, $this->prop->readString($section, 'call.func')),
            $this->prop->readArray($section, 'call.params')
          );
	    }
      } if (catch('Exception', $e)) {
	    return throw($e);
      }
      
      $this->configured= TRUE;
      return TRUE;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   util.log.LogCategory CAT default NULL a log category to print debug to
     * @return  
     */
    function parse($cat= NULL) {
      try(); {
        if (!$this->configured) $this->configure();

        $this->file->open(FILE_MODE_READ);
        $contents= $this->file->read($this->file->size());
	    $this->file->close();
      } if (catch('Exception', $e)) {
        $e->printStackTrace();
	    return throw($e);
      }

      $this->tokenizer->setTokens(token_get_all($contents));
      return $this->tokenizer->applyRules($cat);
    }
  }
?>
