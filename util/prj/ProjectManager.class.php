<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses (
    'ProjectManagerPopupMenu',
    'gui.gtk.GtkGladeApplication',
    'util.log.Logger',
    'util.Properties',
    'util.cmd.ParamString',
    'text.PHPParser',
    'lang.System'
  );
  
  class ProjectManager extends GtkGladeApplication {
    var
      $tree=    NULL,
      $files=   array();
      
    /**
     * Constructor
     *
     * @access  public
     */
    function __construct() {
      $p= &new ParamString();
      
      parent::__construct(dirname(__FILE__).'/prj.glade', 'mainwindow');
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
      
      // Init ClassTree
      $this->tree= &$this->widget ('classTree');
      $this->tree->connect ('select_row', array (&$this, 'onTreeSelectRow'));
      $this->tree->connect ('button_press_event', array (&$this, 'onTreeClick'));
      
      // Init Statusbar
      $this->statusbar= &$this->widget ('statusbar');
    }
    
    function onAutoUpdate(&$widget) {
      // Automatically reload all data
    }
    
    function onTreeSelectRow(&$widget, $row, &$data, &$event) {
      $node= &$this->tree->node_nth ($row);
      $this->_selectedNode= &$node;
    }
    
    function onTreeClick(&$clist, &$event) {
      // Check for right-click
      if ($event->button == 3)
        return $this->onTreeRightClick($click, $event);

      if (!isset ($this->_selectedNode)) return FALSE;
    }
    
    function onTreeRightClick(&$clist, &$event) {
      $this->menu= &new ProjectManagerPopupMenu ($this);
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
          $this->files[$idx]->parse();
          $reparse= TRUE;
        }
      }
      
      if ($reparse) $this->updateList();
      return TRUE;
    }
    
    function addFile(&$parser) {
      if (!is_a ($parser, 'PHPParser'))
        return FALSE;
      
      $parser->parse();
      $this->files[]= &$parser;
      $this->statusbar->push (1, 'Added file '.$parser->filename);
      
      $this->updateList();
      return TRUE;
    }
    
    function updateList() {
      $this->tree->freeze();
      $this->tree->clear();
      
      $rootNode= &$this->tree->insert_node (
        NULL,
        NULL,
        array ('global program space', '0', '0'),
        0,
        NULL,
        NULL,
        NULL,
        NULL,
        FALSE,
        TRUE
      );
      
      foreach (array_keys ($this->files) as $idx) {
        $file= &$this->files[$idx];
        
        $fileNode= &$this->tree->insert_node (
          $rootNode,
          NULL,
          array (basename ($file->filename), '', ''),
          0,
          NULL,
          NULL,
          NULL,
          NULL,
          FALSE,
          FALSE
        );
        $node= &new StdClass();
        $node->file= &$f;
        
        $this->tree->node_set_row_data ($fileNode, $node);
        
        foreach (array_keys ($file->classes) as $cIdx) {
          $c= &$file->classes[$cIdx];

          $classNode= &$this->tree->insert_node (
            $fileNode,
            NULL,
            array ($c->name, $c->line, $c->endsAt),
            0,
            NULL,
            NULL,
            NULL,
            NULL,
            FALSE,
            FALSE
          );
          $node= &new StdClass();
          $node->file= &$file;
          $node->object= &$c;
          
          $this->tree->node_set_row_data ($classNode, $node);
          
          foreach (array_keys ($c->functions) as $fIdx) {
            $f= &$c->functions[$fIdx];
            $funcNode= &$this->tree->insert_node (
              $classNode,
              NULL,
              array ($f->name, $f->line, $f->endsAt),
              0,
              NULL,
              NULL,
              NULL,
              NULL,
              FALSE,
              FALSE
            );
            $node= &new StdClass();
            $node->file= &$file;
            $node->object= &$f;
            
            $this->tree->node_set_row_data ($funcNode, $node);
          }
        }
      }
      
      $this->tree->columns_autosize();
      $this->tree->thaw();
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */    
    function onFileOpenCtx(&$menuItem, &$event) {
      $n= &$this->tree->node_get_row_data ($this->_selectedNode);

      $line= 0;
      if (isset ($n->object))
        $line= $n->object->line;

      System::exec (sprintf ('nedit -line %d %s',
        $line,
        $n->file->filename        
      ), '2>&1', TRUE);
    }
  }

?>
