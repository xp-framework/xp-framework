<?php
/* This class is part of the XP framework
 *
 * $Id: ProjectManager.class.php 9039 2006-12-29 16:09:19Z friebe $
 */

  namespace de::document-root::gui::gtk::project;

  ::uses (
    'de.document-root.gui.gtk.project.ProjectManagerPopupMenu',
    'de.document-root.gui.gtk.project.ParserManager',
    'org.gnome.GtkGladeApplication',
    'lang.System'
  );
  
  class ProjectManager extends org::gnome::GtkGladeApplication {
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
     */
    function __construct($p, $base) {
      $pm= util::PropertyManager::getInstance();
      
      $l= util::log::Logger::getInstance();
      $this->log= $l->getCategory ($this->getClassName());

      try {
        $this->prop= $pm->getProperties('prj');
      } catch(io::IOException $e) {
        // Ignore exception, but take it off the stack
      }
      
      $this->base= $base;
      parent::__construct($p, $this->base.'/ui/prj.glade', 'mainwindow');
    }
    
    /**
     * Retrieve a symbolic reference to the object identified by the
     * given arguments.
     *
     * Note that this is not a "real" reference. The references objects are
     * referenced by sematics, not by a real reference. That is, after reparsing
     * there are new objects, the old ones destroyed. This reference will then
     * automatically point to the object replacing the old one.
     *
     * @param   string idx
     * @param   string cidx
     * @param   string fidx
     * @return  string reference
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
     * Check whether a given reference has already been created
     * for the node.
     *
     * @param   string reference
     * @return  bool
     */
    function _nodeExists($ref) {
      return isset ($this->hash[$ref]);
    }
    
    /**
     * Retrieve the node object associated to the
     * reference.
     *
     * @param   string ref
     * @return  &GtkCTreeItem
     */
    function _getNode($ref) {
      return $this->hash[$ref];
    }
    
    /**
     * Supply application defined information associated
     * to a given reference.
     *
     * @param   string reference
     * @param   mixed data
     */
    function _setRefInfo($ref, $info) {
      $this->refdata[$ref]= $info;
    }
    
    /**
     * Retrieve given data for a reference
     *
     * @param   string reference
     * @return  mixed 
     */
    function _getRefInfo($ref) {
      return $this->refdata[$ref];
    }
    
    /**
     * Check whether the object referenced for the given argument
     * is still known.
     *
     * @param   string reference
     * @return  bool
     */
    function _existsRefObject($ref) {
      if (!preg_match('/^f=([^:]+)(:c=([^:]+))?(:fn=([^:$]+))?$/', $ref, $match)) {
        $this->log->error($ref, 'does not match!');
        return FALSE;
      }
      
      @list(, $file, , $class, , $func)= $match;
      if (!empty($class) && !empty($func)) {
        return isset($this->files[$file]->classes[$class]->functions[$func]);
      } elseif (!empty($func)) {
        return isset($this->files[$file]->functions[$func]);
      } elseif (!empty($class)) {
        return isset($this->files[$file]->classes[$class]);
      } else {
        return isset($this->files[$file]);
      }
      
      return FALSE;
    }
    
    /**
     * Initializes all windows
     *
     */    
    function init() {
      parent::init();
      
      $this->log->info('Initializing...');
      
      // Init window
      $this->window->set_default_size (600, 400);
      
      // Setup filedialog
      $this->dialog= new ();
      
      // Init ClassTree
      $this->tree= $this->widget ('classTree');
      $this->tree->connect ('select_row', array ($this, 'onTreeSelectRow'));
      $this->tree->connect ('unselect_row', array ($this, 'onTreeUnselectRow'));
      $this->tree->connect ('button_press_event', array ($this, 'onTreeClick'));
      $this->tree->set_line_style (GTK_CTREE_LINES_DOTTED);

      // Init Statusbar
      $this->statusbar= $this->widget ('statusbar');
      
      // Load pixmaps
      $l= new  ($this->window->window, $this->base.'/ui/xpm/');
      $this->pixmap= $l->load (array (
        'sv_class',
        'sv_scalar', 
        'sv_session',
        'sv_private_scalar'
      ));

      // Handle commandline arguments
      for ($i= 1; $i <= $this->param->count; $i++) {
        try { 
          $a= $this->param->value ($i); 
        } catch(::Exception $e) { 
          break; 
        }
        if ('-' !== $a{0}) $this->_recursiveAddFile ($a);
        $this->updateList();
      }
      
      // Check every 60 seconds
      $timeout= $this->prop->readInteger ('main', 'autocheck', 60);
      $this->timer= ::timeout_add (1000 * $timeout, array ($this, 'onAutoUpdate'));
    }
    
    function onAutoUpdate() {
    
      // Automatically reload all data
      $this->reparse();
      return TRUE;
    }
    
    /**
     * Retrieve the realpath
     *
     * @param   string path
     * @return  string
     */    
    function getRealpath($path) {
      static $cache= array();
      
      if (!isset($cache[$path])) {
        $cache[$path]= realpath($path);
      }
      
      return $cache[$path];
    }
    
    function onTreeSelectRow($widget, $row, $data, $event) {
      $node= $this->tree->node_nth ($row);
      $this->_selectedNode= $node;
    }
    
    function onTreeUnselectRow($widget, $row, $data, $event) {
      if (isset ($this->_selectedRow))
        unset ($this->_selectedNode);
    }
    
    function onTreeClick($clist, $event) {
    
      // Check for right-click
      if (3 == $event->button)
        return $this->onTreeRightClick($click, $event);

      if (1 == $event->button && GDK_2BUTTON_PRESS == $event->type) {
        if (!$this->tree->is_hot_spot($event->x, $event->y))
          return $this->onFileOpenCtx();
      }

      return TRUE;
    }
    
    function onTreeRightClick($clist, $event) {
      $this->menu= new ProjectManagerPopupMenu();
      $this->menu->setParent($this);
      $this->menu->addMenuItem ('Add file...', array ($this->menu, 'addFile'));
      $this->menu->addMenuItem ('Reparse files', array ($this->menu, 'reparse'));
      
      if (isset ($this->_selectedNode)) {
        $this->menu->addSeparator();
        $this->menu->addMenuItem('Open', array ($this, 'onFileOpenCtx'));
        $this->menu->addMenuItem('cvs diff', array($this, 'onCvsDiff'));
      }

      $this->menu->show(MENU_WANT_LEFTCLICK);
    }
    
    function reparse() {
      $reparse= FALSE;
      foreach (array_keys ($this->files) as $idx) {
        if ($this->files[$idx]->needsReparsing()) {
          $this->log && $this->log->info('Reparsing', $this->files[$idx]->filename);
          $this->statusbar->push (1, 'Reparsing '.$this->files[$idx]->filename);
          $this->files[$idx]->parse();
          $this->statusbar->push (1, 'Reparsed '.$this->files[$idx]->filename.' at '.date ('H:i:s'));
          
          // FIXME: If new uses() or require()'s are found, add them to the project as well...

          $reparse= TRUE;
        }
      }
      
      if ($reparse) $this->updateList();
      return TRUE;
    }
    
    function _recursiveAddFile($filename, $recurse= TRUE) {
    
      // Only add files...
      if (!is_file($filename))
        return FALSE;
      
      // Resolve into real filename...
      $filename= realpath($filename);
        
      // Try to prevent adding of the same files twice
      foreach (array_keys ($this->files) as $idx) {
        if ($this->files[$idx]->filename === $filename)
          return FALSE;
      }
      
      $parser= new ParserManager($filename);
      $parser->parse();

      $this->files[$filename]= $parser;

      $this->statusbar->push (1, 'Added file '.$parser->filename);
      $this->log && $this->log->info ('Added file '.$parser->filename);
      
      // Now pull in all the dependencies
      foreach ($parser->::uses as $u) {
        foreach (explode (':', ini_get ('include_path')) as $p) {
          $fn= $p.'/'.str_replace ('.', DIRECTORY_SEPARATOR, $u).'.class.php';
          if (is_file ($fn)) {
            $this->_recursiveAddFile($fn);
            break;
          }
        }
      }
      
      foreach ($parser->requires as $r) {
        foreach (explode (':', ini_get ('include_path')) as $p) {
          $fn= $p.'/'.$r;
          if (file_exists ($fn)) {
            $this->_recursiveAddFile ($fn);
            break;
          }
        }
      }
    }
    
    function getFqcnFromFile($filename) {
      foreach (explode(PATH_SEPARATOR, ini_get('include_path')) as $include) {
        $include= $this->getRealpath($include);
        $filename= $this->getRealpath($filename);
        
        if (substr($filename, 0, strlen($include)) == $include) {
          
          // The file seems to originate from this include path
          $file= substr($filename, strlen($include) + 1);
          $fqdn= str_replace(DIRECTORY_SEPARATOR, '.', str_replace('.class.php', '', $file));
          return $fqdn;
        }
      }
      
      return FALSE;
    }
    
    function classifyFile($filename) {
      
      // Handle special case
      if (substr($filename, -13) == 'lang.base.php')
        return 'core';
        
      $classname= $this->getFqcnFromFile($filename);
      
      foreach ($this->prop->readArray('classes', 'core', array('core')) as $core) {
        if (substr($classname, 0, strlen($core)) == $core)
          return 'core';
      }
      
      foreach ($this->prop->readArray('classes', 'self', array()) as $self) {
        if (substr($classname, 0, strlen($self)) == $self)
          return $self;
      }
      
      foreach ($this->prop->readArray('classes', 'ignore', array()) as $ignore) {
        if (substr($classname, 0, strlen($ignore)) == $ignore)
          return 'ignore';
      }
      
      return 'framework';
    }

    function addFile($filename) {
      $this->_recursiveAddFile ($filename);
      
      $this->updateList();
      return TRUE;
    }
    
    function _addNode($parent, $label, $ref, $pixmap= 'sv_session') {
      $this->log && $this->log->info('Added ref', $ref);
      $node= $this->tree->insert_node (
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
      $this->hash[$ref]= $node;

      // $style= gtk::widget_get_default_style();
      $style= new ();
      $style->fg[GTK_STATE_NORMAL]= new  ('#cccccc');
      $this->tree->node_set_cell_style ($node, 1, $style);
      $this->tree->node_set_cell_style ($node, 2, $style);

      return $node;
    }
    
    function getNode($name) {
      if (!isset($this->nodes[$name])) {
        $this->log && $this->log->debug('Adding node', $name);
        $this->nodes[$name]= $this->tree->insert_node(
          $this->nodes['root'],
          NULL,
          array($name),
          0,
          $this->pixmap['p:sv_session'],
          $this->pixmap['m:sv_session'],
          $this->pixmap['p:sv_session'],
          $this->pixmap['m:sv_session'],
          FALSE,
          TRUE
        );
      }

      return $this->nodes[$name];
    }
    
    function updateList() {
      $this->tree->freeze();
      // $this->tree->clear();
      
      if (empty($this->nodes['root'])) {
        $this->nodes['root']= $this->tree->insert_node (
          NULL,
          NULL,
          array('Workspace'),
          0,
          $this->pixmap['p:sv_session'],
          $this->pixmap['m:sv_session'],
          $this->pixmap['p:sv_session'],
          $this->pixmap['m:sv_session'],
          FALSE,
          TRUE
        );
      }
      
      if (empty($this->nodes['core'])) {
        $this->nodes['core']= $this->tree->insert_node(
          $this->nodes['root'],
          NULL,
          array('Core classes'),
          0,
          $this->pixmap['p:sv_session'],
          $this->pixmap['m:sv_session'],
          $this->pixmap['p:sv_session'],
          $this->pixmap['m:sv_session'],
          FALSE,
          FALSE
        );
      }

      if (empty($this->nodes['framework'])) {
        $this->nodes['framework']= $this->tree->insert_node(
          $this->nodes['root'],
          NULL,
          array('Framework classes'),
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
        $file= $this->files[$idx];

        $classification= $this->classifyFile($file->filename);
        
        $this->log && $this->log->debug('Classified', $file->filename, 'as', $classification);
        
        if ($classification == 'ignore')
          continue;
          
        foreach (array_keys ($file->classes) as $cIdx) {
          $c= $file->classes[$cIdx];
          
          if (!$this->_nodeExists($this->_getRef($idx, $cIdx))) {
            $classNode= $this->_addNode (
              $this->getNode($classification),
              array ($c->name),
              $this->_getRef($idx, $cIdx),
              'sv_class'
            );
          }
          
          $this->_setRefInfo($this->_getRef($idx, $cIdx), array(
            'file' => $file->filename,
            'line' => $c->line
          ));
          
          $classNode= $this->_getNode($this->_getRef($idx, $cIdx));
          foreach (array_keys ($c->functions) as $fIdx) {
            $f= $c->functions[$fIdx];

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
        $file= $this->files[$idx];
        
        foreach (array_keys ($file->functions) as $fIdx) {
          $f= $file->functions[$fIdx];
          
          $this->_setRefInfo($this->_getRef($idx, NULL, $fIdx), array(
            'file' => $file->filename,
            'line' => $f->line
          ));
          
          if ($this->_nodeExists($this->_getRef($idx, NULL, $fIdx)))
            continue;

          $this->_addNode (
            $this->getNode($classification),
            array ($f->name),
            $this->_getRef($idx, NULL, $fIdx),
            'sv_private_scalar'
          );
        }
      }
      
      // Remove nodes without reference
      foreach (array_keys($this->hash) as $ref) {
        
        if (!$this->_existsRefObject($ref)) {
          
          // Remove node
          $this->log && $this->log->warn('Removing node for unreferenced ref', $ref);
          $this->tree->remove_node($this->hash[$ref]);
          unset($this->hash[$ref]);
        }
      }

      $this->tree->set_sort_type(GTK_SORT_ASCENDING);
      $this->tree->set_sort_column(0);
      $this->tree->sort_node($this->nodes['framework']);
      $this->tree->sort_node($this->nodes['core']);
      // $this->tree->columns_autosize();
      $this->tree->thaw();
    }
    
    /**
     * Open the file at the specified line in your editor
     *
     * @param   &GtkMenuItem
     * @param   &GdkEvent
     */    
    function onFileOpenCtx() {
      $n= $this->tree->node_get_row_data ($this->_selectedNode);
      $cmd= $this->prop->readString (
        'editor',
        'cmdline',
        'nedit -line %LINE% %FILE%'
      );
      
      $d= $this->_getRefInfo($n);

      $vars= array(
        '%LINE%'  => $d['line'],
        '%FILE%'  => $d['file'],
        '%SESS%'  => 'prj:'.getmypid()
      );
      $cmd= str_replace(array_keys($vars), array_values($vars), $cmd);
      $this->log && $this->log->debug($cmd);
      
      lang::System::exec ($cmd, '1>/dev/null 2>/dev/null', TRUE);
    }
    
    /**
     * Execute 'cvs diff' command on selected file.
     *
     */
    function onCvsDiff() {
      $n= $this->tree->node_get_row_data($this->_selectedNode);
      $d= $this->_getRefInfo($n);
      
      $tmpFile= lang::System::tempDir().DIRECTORY_SEPARATOR.md5($d['file']).'.diff';
      $cmd= sprintf('cvs diff -u %s', basename($d['file']));

      $curdir= getcwd(); chdir(dirname($d['file']));
      lang::System::exec($cmd, sprintf('1>%1$s 2>%1$s', $tmpFile));
      chdir($curdir);
      
      $cmd= $this->prop->readString (
        'editor',
        'cvsview',
        'nedit %FILE%'
      );
      $cmd= str_replace('%FILE%', $tmpFile, $cmd);
      
      lang::System::exec($cmd, '1>/dev/null 2>/dev/null', TRUE);
      return TRUE;
    }
  }

?>
