<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  require('lang.base.php');
  uses('gui.gtk.GTKGladeApplication', 'io.Folder');

  /**
   * File dialog
   *
   * @purpose Provide a widget for file dialogs
   */
  class FileDialog extends GTKGladeApplication {
    var
      $filename = NULL,
      $dir,
      $filter	= '.*';

    /**
     * Constructor
     *
     * @access  public
     */
    function __construct($dir= '.') {
      $this->dir= $dir;
      //$this->rcFile= dirname(__FILE__).'/filedialog.gtkrc';
      parent::__construct(
        'filedialog', 
        dirname(__FILE__).'/filedialog.glade',
        'filedialog'
      );
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function buttons_connect($b) {
      foreach ($b as $name=> $callback) {
        $this->buttons[$name]= &$this->widget('button_'.$name);
        $this->buttons[$name]->connect_after('clicked', array(&$this, $callback));
      }
    }
    
    /**
     * OK, cancel pressed
     *
     * @access  
     * @param   
     * @return  
     */
    function onClose(&$widget) {
      $this->log($widget->get_name());
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function onUpDirClicked(&$widget) {
      $this->setDir(substr(
        $this->dir, 
        0, 
        strrpos(substr($this->dir, 0, -1), '/')
      ).'/');
    }


    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function onHomeClicked(&$widget) {
      $info= posix_getpwuid(posix_getuid());
      $this->setDir($info['dir']);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function onPNClicked(&$widget) {
      $this->log($widget->get_name(), $this->history, $this->history_offset);
      $this->history_offset+= ('button_prev' == $widget->get_name()) ? -1 : 1;
      $this->log($widget->get_name(), $this->history_offset, $this->history[$this->history_offset]);
      $this->setDir($this->history[$this->history_offset], FALSE);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  public
     */
    function init() {
      parent::init();
      $this->window->set_default_size(400, 420);
      
      // File list
      $this->files= &$this->widget('clist_files');
      $this->files->set_row_height(26);
      $this->files->set_sort_column(1); // Type
      $this->files->connect('select_row', array(&$this, 'onEntrySelected'));
      
      // Location
      $this->location= &$this->widget('entry_location');
      
      // Combo
      $this->combo= &$this->widget('combo_dir');
      
      // Buttons
      $this->buttons_connect(array(
        'ok'	 => 'onClose',
        'cancel' => 'onClose',
        'up'	 => 'onUpDirClicked',
        'home'	 => 'onHomeClicked',
        'next'   => 'onPNClicked',
        'prev'   => 'onPNClicked',
      ));
      
      // Favorites
      $this->favorites= &$this->widget('bar_favorites');
      $this->favorites->set_button_relief(GTK_RELIEF_NONE);
      $view= &$this->widget('view_favorites');
      $style= Gtk::widget_get_default_style();
      $style->base[GTK_STATE_NORMAL]= $style->mid[GTK_STATE_NORMAL];
      $view->set_style($style);
      
      // History
      $this->history= array();
      $this->history_offset= 0;

      // Load pixmaps
	  $this->pixmaps= array();
      $if= &new Folder(dirname(__FILE__).'/icons/');
      try(); {
        while ($entry= $if->getEntry()) {
          if ('.xpm' != substr($entry, -4)) continue;
          $entry= substr($entry, 0, -4);
          list(
            $this->pixmaps['p:'.$entry],
            $this->pixmaps['m:'.$entry]
          )= Gdk::pixmap_create_from_xpm(
            $this->window->window, 
            new GdkColor(0, 0, 0), 
            $if->uri.$entry.'.xpm'
          );
        }
        $if->close();
      } if (catch('IOException', $e)) {
        $e->printStackTrace();
      }
      $this->log(sizeof($this->pixmaps), 'pixmaps loaded');
      
      // Read files
      $this->setDir();
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function onEntrySelected(&$widget, $row, $data, $event) {
      $this->log($widget);
      $ftype= $widget->get_text($row, 1);
      $entry= $widget->get_pixtext($row, 0);
      
      // Check if an item was double clicked
      if ('' == $ftype && isset($event) && GDK_2BUTTON_PRESS == $event->type) {
        return $this->setDir($this->dir.$entry[0]);
      }
      
      $this->filename= $entry[0];
      $this->log($row, 'selected, uri is', $this->filename, 'event', $data, $event);
      
      // Update location entry
      $this->location->set_text($entry[0]);
      
      // Set OK button sensitive if file type is not empty (indicating a directory)
      $this->buttons['ok']->set_sensitive('' != $ftype);
    }
    
    /**
     * Format a size
     *
     * @access  private
     * @param   int s size
     * @return  string formatted output
     */
    function _size($s) {
      if ($s < 1024) return sprintf('%d Bytes', $s);
      if ($s < 1048576) return sprintf('%0.2f KB', $s / 1024);
      if ($s < 1073741824) return sprintf('%0.2f MB', $s / 1048576);
      return sprintf('%0.2f GB', $s / 1073741824);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setDir($dir= NULL, $update_offset= TRUE) {
      if (NULL !== $dir) $this->dir= $dir;
      $this->log('Change dir to', $this->dir);
      
      // Disable Up button if we are at top
      $this->buttons['up']->set_sensitive(strlen($this->dir) > 1);
      
      // Update combo
      $this->history[]= $this->dir;
      $size= sizeof($this->history)- 1;
      $this->combo->set_popdown_strings(array_unique($this->history));
      
      if ($update_offset) $this->history_offset= $size;
      
      // "Previous" is available if this is not the first call
      $this->buttons['prev']->set_sensitive($this->history_offset > 0);
      $this->buttons['next']->set_sensitive($this->history_offset < $size);

      $this->readFiles();
    }
            
    /**
     * (Insert method's description here)
     *
     * @access  public
     */  
    function readFiles() {
      $f= &new Folder($this->dir);

      // Disable Up button if we are at top
      $this->buttons['up']->set_sensitive(strlen($this->dir) > 1);
      
      // Update entry
      $entry= $this->combo->entry;
      $entry->set_text($f->uri);

      // Update list
      $this->files->freeze();
      $this->files->clear();
      try(); {
        while ($entry= $f->getEntry()) {
          if (!preg_match(':'.$this->filter.':i', $entry)) continue;
          
          $icon= $mask= NULL;
          if ($dir= is_dir($f->uri.$entry)) {
          
            // Set folder icon
            $icon= $this->pixmaps['p:folder'];
            $mask= $this->pixmaps['m:folder'];
          } else {
            $ext= '(n/a)';
  	        if (FALSE !== ($p= strrpos($entry, '.')) && $p > 0) $ext= substr($entry, $p+ 1);
            
            // Check for "special" files
            if (preg_match('#README|TODO|INSTALL|COPYRIGHT|NEWS#', $entry)) {
              $idx= 'special.readme';
            } else {
              $idx= isset($this->pixmaps['p:ext.'.$ext]) ? 'ext.'.$ext : 'none';
            }
            
            // Set icon
            $icon= $this->pixmaps['p:'.$idx];
            $mask= $this->pixmaps['m:'.$idx];
          }
          
          // Get file owner's name
          $owner= posix_getpwuid(fileowner($f->uri.$entry));
          
          // $this->log($f->uri.$entry, 'dir?', $dir, 'ext', $ext); $this->log($entry, $owner);
          
		  $this->files->set_pixtext(
            $this->files->append(array(
              $entry,
              $dir ? '' : $ext,
              $this->_size(filesize($f->uri.$entry)),
              date('Y-m-d H:i', filemtime($f->uri.$entry)),
              $owner['name'],
              substr(sprintf("%o", fileperms($f->uri.$entry)), 3- $dir)
            )),
		    0, 
		    $entry,
		    4,
		    $icon,
	        $mask
          );

        }
        
        // Copy folder's URI (will be full path)
        $this->dir= $f->uri;
        $f->close();
      } if (catch('IOException', $e)) {
        $e->printStackTrace();
      }
      $this->files->sort();
      $this->files->thaw();
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function show() {
      $this->init();
      $this->run();
      $this->done();
      return (NULL == $this->filename) ? FALSE : $this->filename;
    }
  }
  
  $dlg= &new FileDialog(posix_getcwd());
  if ($dlg->show()) {
    printf("File selected: %s%s\n", $dlg->dir, $dlg->filename);
  }
?>
