<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  require_once ('lang.base.php');

  uses (
    'ProjectManagerPopupMenu',
    'gui.gtk.GtkGladeApplication',
    'util.log.Logger',
    'util.log.FileAppender',
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
      
      $l= &Logger::getInstance();
      $this->log= &$l->getCategory ($this->getClassName());
      $this->log->addAppender (new FileAppender ('php://stdout'));
      
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
      
      // Load pixmaps
      $l= &new GtkPixmapLoader ($this->window->window, dirname (__FILE__).'/xpm/');
      $this->pixmap= &$l->load (array (
        'sv_class',
        'sv_scalar', 
        'sv_session',
        'sv_private_scalar'
      ));
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
            $this->addFile (new PHPParser (realpath($fn)));
            break;
          }
        }
      }
      
      foreach ($parser->requires as $r) {
        foreach (explode (':', ini_get ('include_path')) as $p) {
          $fn= $p.'/'.$r;
          if (file_exists ($fn)) {
            $this->addFile (new PHPParser (realpath($fn)));
            break;
          }
        }
      }
      
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
        $this->pixmap['p:sv_session'],
        $this->pixmap['m:sv_session'],
        $this->pixmap['p:sv_session'],
        $this->pixmap['m:sv_session'],
        FALSE,
        TRUE
      );
      
      foreach (array_keys ($this->files) as $idx) {
        $file= &$this->files[$idx];
        
        foreach (array_keys ($file->classes) as $cIdx) {
          $c= &$file->classes[$cIdx];

          $classNode= &$this->tree->insert_node (
            $rootNode,
            NULL,
            array ($c->name, $c->line, $c->endsAt),
            0,
            $this->pixmap['p:sv_class'],
            $this->pixmap['m:sv_class'],
            $this->pixmap['p:sv_class'],
            $this->pixmap['m:sv_class'],
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
              $this->pixmap['p:sv_scalar'],
              $this->pixmap['m:sv_scalar'],
              $this->pixmap['p:sv_scalar'],
              $this->pixmap['m:sv_scalar'],
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
      
      // Now add global functions
      foreach (array_keys ($this->files) as $idx) {
        $file= &$this->files[$idx];
        
        foreach (array_keys ($file->functions) as $fIdx) {
          $f= &$file->functions[$fIdx];

          $funcNode= &$this->tree->insert_node (
            $rootNode,
            NULL,
            array ($f->name, $f->line, $f->endsAt),
            0,
            $this->pixmap['p:sv_private_scalar'],
            $this->pixmap['m:sv_private_scalar'],
            $this->pixmap['p:sv_private_scalar'],
            $this->pixmap['m:sv_private_scalar'],
            FALSE,
            FALSE
          );
          $node= &new StdClass();
          $node->file= &$file;
          $node->object= &$f;

          $this->tree->node_set_row_data ($funcNode, $node);
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
      $this->menu->menu->hide();
      
      $n= &$this->tree->node_get_row_data ($this->_selectedNode);

      $line= 0;
      if (isset ($n->object))
        $line= $n->object->line;

      System::exec (sprintf ('n -line %d %s',
        $line,
        $n->file->filename        
      ), '2>&1', TRUE);
    }
  }

?>
