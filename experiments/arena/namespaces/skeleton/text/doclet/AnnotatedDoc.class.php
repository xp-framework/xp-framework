<?php
/* This class is part of the XP framework
 *
 * $Id: AnnotatedDoc.class.php 8974 2006-12-27 17:29:09Z friebe $ 
 */

  namespace text::doclet;

  uses('text.doclet.Doc', 'text.doclet.AnnotationDoc');

  /**
   * Represents annotated doc classes.
   *
   * @see      xp://text.doclet.ClassDoc
   * @see      xp://text.doclet.MethodDoc
   * @purpose  Base class
   */
  class AnnotatedDoc extends Doc {
    public
      $annotations= NULL;
    
    public
      $_parsed    = NULL;
      
    /**
     * Parse annotations from string
     *
     * @throws  lang.FormatException in case the annotations cannot be parsed
     */    
    protected function parse() {
      if (is_array($this->_parsed)) return;   // Short-cuircuit: We've already parsed it
      
      $this->_parsed= array();
      if (!$this->annotations) return;
      
      $expr= preg_replace(
        array('/@([a-z_]+),/i', '/@([a-z_]+)\(\'([^\']+)\'\)/i', '/@([a-z_]+)\(/i', '/([^a-z_@])([a-z_]+) *= */i'),
        array('\'$1\' => NULL,', '\'$1\' => \'$2\'', '\'$1\' => array(', '$1\'$2\' => '),
        trim($this->annotations, "[]# \t\n\r").','
      );
      if (!is_array($hash= eval('return array('.$expr.');'))) {
        throw(new lang::FormatException('Cannot parse '.$this->annotations.' ('.$expr.')'));
      }
      
      foreach ($hash as $name => $value) {
        $this->_parsed[$name]= new AnnotationDoc($name, $value);
      }
    }
     
    /**
     * Retrieves a list of all annotations
     *
     * @return  array
     */ 
    public function annotations() {
      $this->parse();
      return array_values($this->_parsed);
    }
  }
?>
