<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'text.doclet.SeeTaglet',
    'text.doclet.ParamTaglet',
    'text.doclet.ReturnTaglet',
    'text.doclet.ThrowsTaglet',
    'text.doclet.SimpleTaglet',
    'text.doclet.TestTaglet',
    'text.doclet.ModelTaglet'
  );

  /**
   * Manages the taglets used by doclets. 
   *
   * @purpose  purpose
   */
  class TagletManager extends Object {
    protected static
      $instance = NULL;

    public
      $taglets  = array();
      
    static function __static() {
      with (self::$instance= new self()); {
        self::$instance->taglets['see']= new SeeTaglet();
        self::$instance->taglets['param']= new ParamTaglet();
        self::$instance->taglets['return']= new ReturnTaglet();
        self::$instance->taglets['throws']= new ThrowsTaglet();
        self::$instance->taglets['test']= new TestTaglet;
        
        // Simple taglets
        $s= new SimpleTaglet();
        self::$instance->taglets['purpose']= $s;
        self::$instance->taglets['deprecated']= $s;
        self::$instance->taglets['experimental']= $s;
        self::$instance->taglets['platform']= $s;
        self::$instance->taglets['doc']= $s;
        self::$instance->taglets['ext']= $s;
      }
    }

    /**
     * Constructor
     * 
     */
    protected function __construct() {
    }

    /**
     * Return the TagletManager's instance
     * 
     * @return  text.doclet.TagletManager
     */
    public static function getInstance() {
      return self::$instance;
    }
    
    /**
     * Add a new tag
     *
     * @param   string kind
     * @param   text.doclet.Taglet taglet
     */
    public function addCustomTag($kind, $taglet) {
      $this->taglets[$kind]= $taglet;
    }

    /**
     * Factory method
     *
     * @param   text.doclet.Doc holder
     * @param   string kind
     * @param   string text
     * @return  text.doclet.Tag
     */
    public function make($holder, $kind, $text) {
      if (!isset($this->taglets[$kind])) {
        throw new IllegalArgumentException(sprintf(
          'Unknown taglet kind "%s" in %s named "%s"',
          $kind,
          $holder->getClassName(),
          $holder->name()
        ));
      }
      
      return $this->taglets[$kind]->tagFrom($holder, $kind, $text);
    }
  }
?>
