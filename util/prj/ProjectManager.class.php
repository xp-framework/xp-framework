<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses (
    'ProjectManagerPopupMenu',
    'gui.gtk.GtkGladeApplication',
    'util.log.Logger',
    'util.log.FileAppender',
    'util.Properties',
    'util.cmd.ParamString',
    'ParserManager',
    'lang.System'
  );
  
  class ProjectManager extends GtkGladeApplication {
    var
      $tree=        NULL,
      $dialog=      NULL,
      $statusbar=   NULL,
      $timer=       NULL,
      $menu=        NULL,
      $pixmap=      array(),
      $files=       array();
    
    var
      $hash=        array(),
      $refdata=     array();
      
    /**
     * Constructor
     *
     * @access  public
     */
    function __construct(&$p) {
      $l= &Logger::getInstance();
      $this->log= &$l->getCategory ($this->getClassName());
      $this->log->addAppender (new FileAppender ('php://stdout'));

      $this->prop= &new Properties (getenv ('HOME').'/.prj.ini');
      parent::__construct($p, dirname(__FILE__).'/prj.glade', 'mainwindow');
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function _getRef($idx, $cidx= NULL, $fidx= NULL) {
      $arr= array();
      $arr[]= 'f='.$this->files[$idx]->filename;
      if (NULL !== $cidx) {
        $arr[]= 'c='.$this->files[$idx]->classes[$cidx]->name;
        if (NULL !== $fidx) $arr[]= 'fn='.$this->files[$idx]->classes[$cidx]->functions[$fidx]->name;
      } elseif (NULL !== $fidx) {
        $arr[]= 'fn='.$this->files[$idx]->functions[$fidx]->name;
      }
      
      return implode(PATH_SEPARATOR, $arr);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function _nodeExists($ref) {
      return isset ($this->hash[$ref]);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function _getNode($ref) {
      return $this->hash[$ref];
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function _setRefInfo($ref, $info) {
      $this->refdata[$ref]= $info;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function _getRefInfo($ref) {
      return $this->refdata[$ref];
    }    
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function _getObjectByRef($ref) {
      if (!isset($this->revhash[$ref]))
        return throw(new IllegalArgumentException('"'.$ref.'" is not a valid reference.'));
        
      list($idx, $cidx, $fidx)= $this->revhash[$ref];

      if (isset($fidx) && isset($cidx)) {
        return $this->files[$idx]->classes[$cidx]->functions[$fidx];
      } elseif (isset($cidx)) {
        return $this->files[$idx]->classes[$cidx];
      } elseif (isset($fidx)) {
        return $this->files[$idx]->functions[$fidx];
      } else {
        return $this->files[$idx];
      }
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function _getParentRef($ref) {
      return substr($ref, 0, strrpos($ref, ':'));
    }    
    
    /**
     * Initializes all windows
     *
     * @access  public
     */    
    function init() {
      parent::init();
      
      // Init window
      $this->window->set_default_size (600, 400);
      
      // Setup filedialog
      $this->dialog= &new FileDialog();
      
      // Init ClassTree
      $this->tree= &$this->widget ('classTree');
      $this->tree->connect ('select_row', array (&$this, 'onTreeSelectRow'));
      $this->tree->connect ('unselect_row', array (&$this, 'onTreeUnselectRow'));
      $this->tree->connect ('button_press_event', array (&$this, 'onTreeClick'));
      $this->tree->set_line_style (GTK_CTREE_LINES_DOTTED);

      // Init Statusbar
      $this->statusbar= &$this->widget ('statusbar');
      
      // Load pixmaps
      $l= &new GtkPixmapLoader ($this->window->window, dirname (__FILE__).'/xpm/');
      $this->pixmap= &$l->load (array (
        'sv_class',
        'sv_scalar', 
        'sv_session',
        'sv_private_scalar'
      ));

      // Handle commandline arguments
      for ($i= 1; $i<= $this->param->count; $i++) {
        try(); { 
          $a= $this->param->value ($i); 
        } if (catch ('Exception', $e)) { 
          break; 
        }
        if ('-' !== $a{0}) $this->_recursiveAddFile (new ParserManager($a));
        $this->updateList();
      }
      
      // Check every 60 seconds
      $timeout= $this->prop->readInteger ('main', 'autocheck', 60);
      $this->timer= gtk::timeout_add (1000 * $timeout, array (&$this, 'onAutoUpdate'));
    }
    
    function onAutoUpdate() {
    
      // Automatically reload all data
      $this->reparse();
      return TRUE;
    }
    
    function onTreeSelectRow(&$widget, $row, &$data, &$event) {
      $node= &$this->tree->node_nth ($row);
      $this->_selectedNode= &$node;
      
      $this->log->debug('Selected ref:', $this->tree->node_get_row_data($this->_selectedNode));
    }
    
    function onTreeUnselectRow(&$widget, $row, &$data, &$event) {
      if (isset ($this->_selectedRow))
        unset ($this->_selectedNode);
    }
    
    function onTreeClick(&$clist, &$event) {
    
      // Check for right-click
      if (3 == $event->button)
        return $this->onTreeRightClick($click, $event);

      if (1 == $event->button && GDK_2BUTTON_PRESS == $event->type)
        return $this->onFileOpenCtx();

      if (!isset ($this->_selectedNode)) return FALSE;
    }
    
    function onTreeRightClick(&$clist, &$event) {
      $this->menu= &new ProjectManagerPopupMenu ();
      $this->menu->setParent($this);
      $this->menu->addMenuItem ('Add file...', array (&$this->menu, 'addFile'));
      $this->menu->addMenuItem ('Reparse files', array (&$this->menu, 'reparse'));
      
      if (isset ($this->_selectedNode)) {
        $this->menu->addSeparator();
        $this->menu->addMenuItem ('Open', array (&$this, 'onFileOpenCtx'));
      }

      $this->menu->show(MENU_WANT_LEFTCLICK);
    }
    
    function reparse() {
      $reparse= FALSE;
      foreach (array_keys ($this->files) as $idx) {
        if ($this->files[$idx]->needsReparsing()) {
          $this->statusbar->push (1, 'Reparsing '.$this->files[$idx]->filename);
          $this->files[$idx]->parse();
          $this->statusbar->push (1, 'Reparsed '.$this->files[$idx]->filename.' at '.date ('H:i:s'));
      
          $reparse= TRUE;
        }
      }
      
      if ($reparse) $this->updateList();
      return TRUE;
    }
    
    function _recursiveAddFile(&$parser, $recurse= TRUE) {
    
      // Try to prevent adding of the same files twice
      foreach (array_keys ($this->files) as $idx) {
        if ($this->files[$idx]->filename === $parser->filename)
          return FALSE;
      }
      
      $parser->parse();
      $this->files[]= &$parser;

      $this->statusbar->push (1, 'Added file '.$parser->filename);
      $this->log && $this->log->info ('Added file '.$parser->filename);
      
      // Now pull in all the dependencies
      foreach ($parser->uses as $u) {
        foreach (explode (':', ini_get ('include_path')) as $p) {
          $fn= $p.'/'.str_replace ('.', DIRECTORY_SEPARATOR, $u).'.class.php';
          if (is_file ($fn)) {
            $this->_recursiveAddFile (new ParserManager(realpath($fn)));
            break;
          }
        }
      }
      
      foreach ($parser->requires as $r) {
        foreach (explode (':', ini_get ('include_path')) as $p) {
          $fn= $p.'/'.$r;
          if (file_exists ($fn)) {
            $this->_recursiveAddFile (new ParserManager(realpath($fn)));
            break;
          }
        }
      }
    }

    function addFile(&$parser) {
      if (!is_a ($parser, 'ParserManager'))
        return FALSE;
        
      $this->_recursiveAddFile ($parser);
      
      $this->updateList();
      return TRUE;
    }
    
    function &_addNode(&$parent, $label, $ref, $pixmap= 'sv_session') {
      $node= &$this->tree->insert_node (
        $parent,
        NULL,
        $label,
        0,
        $this->pixmap['p:'.$pixmap],
        $this->pixmap['m:'.$pixmap],
        $this->pixmap['p:'.$pixmap],
        $this->pixmap['m:'.$pixmap],
        FALSE,
        FALSE
      );
      
      $this->tree->node_set_row_data($node, $ref);
      $this->hash[$ref]= &$node;

      // $style= gtk::widget_get_default_style();
      $style= &new GtkStyle();
      $style->fg[GTK_STATE_NORMAL]= new GdkColor ('#cccccc');
      $this->tree->node_set_cell_style ($node, 1, $style);
      $this->tree->node_set_cell_style ($node, 2, $style);

      return $node;
    }
    
    function updateList() {
      $this->tree->freeze();
      // $this->tree->clear();
      
      if (empty($this->rootNode)) {
        $this->rootNode= &$this->tree->insert_node (
          NULL,
          NULL,
          array ('global program space'),
          0,
          $this->pixmap['p:sv_session'],
          $this->pixmap['m:sv_session'],
          $this->pixmap['p:sv_session'],
          $this->pixmap['m:sv_session'],
          FALSE,
          TRUE
        );
      }
      
      foreach (array_keys ($this->files) as $idx) {
        $file= &$this->files[$idx];
        
        foreach (array_keys ($file->classes) as $cIdx) {
          $c= &$file->classes[$cIdx];

          if (!$this->_nodeExists($this->_getRef($idx, $cIdx))) {
            $classNode= &$this->_addNode (
              $this->rootNode,
              array ($c->name),
              $this->_getRef($idx, $cIdx),
              'sv_class'
            );
          }
          
          $this->_setRefInfo($this->_getRef($idx, $cIdx), array(
            'file' => $file->filename,
            'line' => $c->line
          ));
          
          $classNode= &$this->_getNode($this->_getRef($idx, $cIdx));
          foreach (array_keys ($c->functions) as $fIdx) {
            $f= &$c->functions[$fIdx];

            $this->_setRefInfo($this->_getRef($idx, $cIdx, $fIdx), array(
              'file' => $file->filename,
              'line' => $f->line
            ));

            if ($this->_nodeExists($this->_getRef($idx, $cIdx, $fIdx)))
              continue;
              
            $this->_addNode(
              $classNode,
              array ($f->name),
              $this->_getRef($idx, $cIdx, $fIdx),
              'sv_scalar'
            );
          }
        }
      }
      
      // Now add global functions
      foreach (array_keys ($this->files) as $idx) {
        $file= &$this->files[$idx];
        
        foreach (array_keys ($file->functions) as $fIdx) {
          $f= &$file->functions[$fIdx];
          
          $this->_setRefInfo($this->_getRef($idx, NULL, $fIdx), array(
            'file' => $file->filename,
            'line' => $f->line
          ));
          
          if ($this->_nodeExists($this->_getRef($idx, NULL, $fIdx)))
            continue;

          $this->_addNode (
            $this->rootNode,
            array ($f->name),
            $this->_getRef($idx, NULL, $fIdx),
            'sv_private_scalar'
          );
        }
      }
      

      // $this->tree->columns_autosize();
      $this->tree->thaw();
    }
    
    /**
     * Open the file at the specified line in your editor
     *
     * @access  public
     * @param   &GtkMenuItem
     * @param   &GdkEvent
     */    
    function onFileOpenCtx() {
      $n= &$this->tree->node_get_row_data ($this->_selectedNode);
      $cmd= $this->prop->readString (
        'editor',
        'cmdline',
        'nedit -line %d %s'
      );
      
      $d= $this->_getRefInfo($n);

      $cmdLine= sprintf ($cmd,
        $d['line'],
        $d['file']
      );
      
      System::exec ($cmdLine, '1>/dev/null 2>/dev/null', TRUE);
    }
  }

?>
