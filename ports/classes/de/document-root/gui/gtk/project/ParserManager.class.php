<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('text.PHPParser');

  /**
   * Class to manage different types of parsers
   *
   * @ext      token
   * @purpose  Manager parsers
   */
  class ParserManager extends Object {
    var
      $functions= array(),
      $classes=   array(),
      $uses=      array(),
      $requires=  array(),
      $sapis=     array(),
      $history=   array(),
      $filename=     NULL;
    
    var
      $_utimeLastChange=  0;
  
    /**
     * Constructor.
     *
     * @access  public
     * @param   string filename
     */
    function __construct($file) {
      $this->filename= $file;
    }
    
    /**
     * Check whether the file has been modified since last
     * parsing and thus reparsing is necessary.
     *
     * @access  public
     * @return  bool
     */
    function needsReparsing() {
      if (FALSE !== ($mtime= filemtime ($this->filename)))
        return ($mtime > $this->getLastChange());
      
      return TRUE;
    }
    
    /**
     * Get the time of the last parsing.
     *
     * @access  public
     * @return  int
     */
    function getLastChange() {
      return $this->_utimeLastChange;
    }
    
    /**
     * Reparse the file.
     *
     * @access  public
     */
    function parse() {
      $this->history= array(
        'functions' => $this->functions,
        'classes'   => $this->classes,
        'uses'      => $this->uses,
        'requires'  => $this->requires,
        'sapis'     => $this->sapis
      );
      
      $parser= &new PHPParser($this->filename);
      $parser->parse();
      
      $this->functions= &$parser->functions;
      $this->classes=   &$parser->classes;
      $this->uses=      &$parser->uses;
      $this->requires=  &$parser->requires;
      $this->sapis=     &$parser->sapis;
      
      unset ($parser);
    }
  }
?>
