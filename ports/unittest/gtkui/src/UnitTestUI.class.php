<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'gui.gtk.GtkGladeApplication',
    'gui.gtk.widgets.FileDialog',
    'gui.gtk.widgets.MessageBox',
    'gui.gtk.util.GTKPixmapLoader',
    'util.profiling.unittest.TestSuite',
    'util.Properties'
  );

  /**
   * Mail box monitor
   *
   * @ext      gtk
   * @purpose  Application
   */
  class UnitTestUI extends GtkGladeApplication {
    var
      $pixmaps          = array(),
      $hierarchy        = NULL,
      $trace            = NULL,
      $statusbar        = NULL,
      $progress         = NULL,
      $dialog           = NULL,
      $node             = array(),
      $suite            = NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   &util.cmd.ParamString p
     */
    function __construct(&$p) {
      parent::__construct(dirname(__FILE__).'/gtkui.glade', 'mainwindow');
    }
    
    /**
     * Initialize application
     *
     * @access  public
     */
    function init() {
      $this->suite= &new TestSuite();
      
      $this->dialog= &new FileDialog('/home/thekid/devel/xp/util/tests');
      $this->dialog->setFilter('ini$');
      $this->dialog->setModal(TRUE);
      
      $this->hierarchy= &$this->widget('hierarchy');
      $this->hierarchy->set_row_height(20);
      
      $loader= &new GTKPixmapLoader($this->window->window, dirname(__FILE__));
      $this->pixmaps= $loader->load(array(
        'suite', 
        'test', 
        'collection', 
        'test_failed', 
        'test_skipped', 
        'test_succeeded'
      ));

      $this->connect($this->widget('select'), 'clicked');
      $this->connect($this->widget('run'), 'clicked');
    }
    
    /**
     * Callback
     *
     * @access  protected
     * @param   &php.GtkWidget widget
     */
    function onRunClicked(&$widget) {
      $result= &$this->suite->run();
      
      foreach ($result->succeeded as $id => $success) {
        $content= $this->hierarchy->node_get_pixtext($this->node[$id], 0);
        $this->hierarchy->node_set_pixtext(
          $this->node[$id], 
          0,
          $content[0],
          $content[1],
          $this->pixmaps['p:test_succeeded'],
          $this->pixmaps['m:test_succeeded']
        );
      }
    }
    
    /**
     * Callback
     *
     * @access  protected
     * @param   &php.GtkWidget widget
     */
    function onSelectClicked(&$widget) {
      static $loaded= array();

      do {
        if (!$this->dialog->show()) return;
        
        $idx= md5($this->dialog->getDirectory().$this->dialog->getFileName());
        if (!isset($loaded[$idx])) break;

        MessageBox::display('Cannot load the same test config twice!', 'Error', MB_OK | MB_ICONWARNING);
      } while (1);
      $loaded[$idx]= TRUE;
      
      $config= &new Properties($this->dialog->getDirectory().$this->dialog->getFileName());
      $section= $config->getFirstSection();
      do {
        if (-1 == ($numtests= $config->readInteger($section, 'numtests', -1))) {
          MessageBox::display('Section '.$section.': key "numtests" missing', 'Error', MB_OK | MB_ICONERROR);
          return;
        }
        
        $sectionNode= &$this->hierarchy->insert_node(
          NULL,
          NULL,
          array($this->dialog->getFileName().'::'.$section, $numtests.' test(s)'),
          4,
          $this->pixmaps['p:collection'],
          $this->pixmaps['m:collection'],
          $this->pixmaps['p:collection'],
          $this->pixmaps['m:collection'],
          FALSE,
          TRUE
        );

        for ($i= 0; $i < $numtests; $i++) {
          try(); {
            $class= &XPClass::forName($config->readString($section, 'test.'.$i.'.class'));
          } if (catch('ClassNotFoundException', $e)) {
            $this->cat->error('Test group "'.$section.'", test #'.$i.':: ', $e->getStackTrace());
            MessageBox::display($e->getMessage(), 'Error', MB_OK | MB_ICONERROR);
            return;
          }

          // Create a new instance
          $name= $config->readString($section, 'test.'.$i.'.name');
          $args= array_merge($name, $config->readArray($section, 'test.'.$i.'.args', array()));
          $test= &call_user_func_array(array(&$class, 'newInstance'), $args);
          
          // Insert into hierarchy tree
          if (!isset($this->node[$test->getClassName()])) {
            $this->node[$test->getClassName()]= &$this->hierarchy->insert_node(
              $sectionNode,
              NULL,
              array($test->getClassName(), ''),
              4,
              $this->pixmaps['p:suite'],
              $this->pixmaps['m:suite'],
              $this->pixmaps['p:suite'],
              $this->pixmaps['m:suite'],
              FALSE,
              TRUE
            );
          }
          $this->node[$test->getClassName().'::'.$test->getName()]= &$this->hierarchy->insert_node(
            $this->node[$test->getClassName()],
            NULL,
            array($test->getName(), ''),
            4,
            $this->pixmaps['p:test'],
            $this->pixmaps['m:test'],
            $this->pixmaps['p:test'],
            $this->pixmaps['m:test'],
            TRUE,
            TRUE
          );
          
          $this->suite->addTest($test);
        }
      } while ($section= $config->getNextSection());
      
      $this->cat->info($this->suite);
    }
  }
?>
