<?php
/* This class is part of the XP framework
 *
 * $Id: ParserManager.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace de::document-root::gui::gtk::project;

  ::uses('text.PHPParser');

  /**
   * Class to manage different types of parsers
   *
   * @ext      token
   * @purpose  Manager parsers
   */
  class ParserManager extends lang::Object {
    public
      $functions= array(),
      $classes=   array(),
      $uses=      array(),
      $requires=  array(),
      $sapis=     array(),
      $history=   array(),
      $filename=     NULL;
    
    public
      $_utimeLastChange=  0;
  
    /**
     * Constructor.
     *
     * @param   string filename
     */
    public function __construct($file) {
      $this->filename= $file;
    }
    
    /**
     * Check whether the file has been modified since last
     * parsing and thus reparsing is necessary.
     *
     * @return  bool
     */
    public function needsReparsing() {
      clearstatcache();
      if (FALSE !== ($mtime= filemtime ($this->filename)))
        return ($mtime > $this->_utimeLastChange);
      
      return TRUE;
    }
    
    /**
     * Get the time of the last parsing.
     *
     * @return  int
     */
    public function getLastChange() {
      return $this->_utimeLastChange;
    }
    
    /**
     * Reparse the file.
     *
     */
    public function parse() {
      
      // Try to parse file
      try {
        $parser= new text::PHPParser($this->filename);
        $parser->parse();
      } catch (io::IOException $e) {
        throw($e);
      }
      
      $this->requires=  $parser->requires;
      $this->::uses=      $parser->uses;
      $this->sapis=     $parser->sapis;
      
      // Use function names as array keys
      $this->functions= array();
      foreach (array_keys($parser->functions) as $idx) {
        $f= $parser->functions[$idx];
        $this->functions[$f->name]= $f;
      }
      
      // Use class- and function-names as array_keys
      foreach (array_keys($parser->classes) as $idx) {
        $c= $parser->classes[$idx];
        
        // Intentional copy
        $this->classes[$c->name]= $c;
        $this->classes[$c->name]->functions= array();
        
        foreach (array_keys($c->functions) as $fidx) {
          $f= $c->functions[$fidx];
          $this->classes[$c->name]->functions[$f->name]= $f;
        }
      }
      
      $this->_utimeLastChange= time();
      unset ($parser);
    }
  }
?>
