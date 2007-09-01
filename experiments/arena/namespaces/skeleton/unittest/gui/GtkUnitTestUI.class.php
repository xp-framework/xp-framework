<?php
/* This class is part of the XP framework
 *
 * $Id: GtkUnitTestUI.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace unittest::gui;

  ::uses(
    'org.gnome.GtkGladeApplication',
    'org.gnome.widgets.FileDialog',
    'org.gnome.widgets.MessageBox',
    'org.gnome.util.GTKPixmapLoader',
    'unittest.TestSuite',
    'util.Properties'
  );

  /**
   * Mail box monitor
   *
   * @ext      gtk
   * @purpose  Application
   */
  class GtkUnitTestUI extends org::gnome::GtkGladeApplication {
    public
      $pixmaps          = array(),
      $hierarchy        = NULL,
      $trace            = NULL,
      $statusbar        = NULL,
      $progress         = NULL,
      $dialog           = NULL,
      $node             = array(),
      $suite            = NULL,
      $loaded           = array();
      
    /**
     * Constructor
     *
     * @param   util.cmd.ParamString p
     */
    public function __construct($p) {
      parent::__construct($p, dirname(__FILE__).'/gtkui.glade', 'mainwindow');
    }
    
    /**
     * Initialize application
     *
     */
    public function init() {
      $this->suite= new unittest::TestSuite();
      
      // File Open Dialog
      $this->dialog= new org::gnome::widgets::FileDialog(SKELETON_PATH.'/../util/tests');
      $this->dialog->setFilter('ini$');
      $this->dialog->setModal(TRUE);
      
      // Hierarchy tree
      $this->hierarchy= $this->widget('hierarchy');
      $this->hierarchy->set_row_height(20);
      $this->hierarchy->set_line_style(GTK_CTREE_LINES_DOTTED);
      $this->connect($this->hierarchy, 'tree_select_row', 'onSelectTest');
      
      // Labels
      $this->labels= array();
      foreach (array('total', 'succeeded', 'failed', 'skipped') as $name) {
        $this->labels[$name]= $this->widget('label_'.$name);
      }

      // Trace
      $this->trace= $this->widget('trace');
      $this->trace->set_row_height(20);
      $this->trace->set_line_style(GTK_CTREE_LINES_DOTTED);
      
      // Progress bar
      $this->progress= $this->widget('progressbar');
      
      // Status bar
      $this->statusbar= $this->widget('statusbar');
      $this->setStatusText('Select test suite configuration');
      
      // Pixmaps
      $loader= new org::gnome::util::GTKPixmapLoader($this->window->window, dirname(__FILE__));
      $this->pixmaps= $loader->load(array(
        'suite', 
        'test', 
        'collection', 
        'test_failed', 
        'test_skipped', 
        'test_succeeded',
        'traceelement',
        'exception'
      ));

      // Buttons
      $this->connect($this->widget('select'), 'clicked');
      $this->connect($this->widget('run'), 'clicked');
      $this->connect($this->widget('clear'), 'clicked');
      
      if ($this->param->exists(1)) {
        $this->addConfiguration(
          $this->param->value(1),
          $this->param->value(2, NULL, FALSE)
        );
      }
    }
    
    /**
     * Set statusbar text and waits for the GUI to process
     * whatever events are still pending.
     *
     * @param   string fmt
     * @param   mixed* args
     */
    public function setStatusText($fmt) {
      $args= func_get_args();
      $text= vsprintf($args[0], array_slice($args, 1));
      $this->statusbar->push(1, $text);
      
      // Leave the GUI time to repaint
      $this->processEvents();
    }
    
    /**
     * Update test
     *
     * @param   string id
     * @param   string result
     * @param   mixed data
     */
    public function updateTest($id, $result, $data) {
      $content= $this->hierarchy->node_get_pixtext($this->node[$id], 0);
      $this->hierarchy->node_set_pixtext(
        $this->node[$id], 
        0,
        $content[0],
        $content[1],
        $this->pixmaps['p:test_'.$result],
        $this->pixmaps['m:test_'.$result]
      );
      $this->hierarchy->node_set_row_data($this->node[$id], $data);
    }

    /**
     * Callback for when a row is selected in the hierarchy ctree
     *
     * @param   php.GtkWidget widget the ctree
     * @param   php.GtkNode node the selected node
     */
    public function onSelectTest($widget, $node) {
    
      // Only make test nodes selectable
      if (!$node->is_leaf) return;
      
      // Get data
      $data= $widget->node_get_row_data($node);
      $this->cat->debug('onSelectTest', $data);
      if (empty($data)) return;
      
      // Update trace
      $this->trace->clear();
      if (::is('Throwable', $data)) {
        $type= 'exception';
        $caption= $data->getClassName().' ('.$data->getMessage().')';
        $trace= $data->getStackTrace();
      } else {
        // TBI
      }
      
      if (isset($type)) {
        $node= $this->trace->insert_node(
          NULL,
          NULL,
          array($caption),
          4,
          $this->pixmaps['p:'.$type],
          $this->pixmaps['m:'.$type],
          $this->pixmaps['p:'.$type],
          $this->pixmaps['m:'.$type],
          FALSE,
          TRUE
        );
        if (!empty($trace)) {
          for ($i= 0, $s= sizeof($trace); $i < $s; $i++) {
            $this->trace->insert_node(
              $node,
              NULL,
              array($trace[$i]->toString()),
              4,
              $this->pixmaps['p:traceelement'],
              $this->pixmaps['m:traceelement'],
              $this->pixmaps['p:traceelement'],
              $this->pixmaps['m:traceelement'],
              FALSE,
              FALSE
            );
          }
        }
      }
      $this->trace->columns_autosize();
    }

    /**
     * Callback
     *
     * @param   php.GtkWidget widget
     */
    public function onClearClicked($widget) {
      $this->hierarchy->clear();
      $this->trace->clear();
      $this->trace->columns_autosize();
      $this->node= array();
      $this->loaded= array();
      $this->suite->clearTests();
      $this->setStatusText('Tests cleared');
    }
    
    /**
     * Callback
     *
     * @param   php.GtkWidget widget
     */
    public function onRunClicked($widget) {
      $numtests= $this->suite->numTests();
      $this->progress->configure(0.0, 0.0, $numtests);
      $result= new TestResult();

      $this->setStatusText('Running suite, %d tests', $numtests);
      for ($i= 0; $i < $numtests; $i++) {
        $this->suite->runTest($this->suite->testAt($i), $result);
        
        // Update progress bar
        $this->progress->set_value($i+ 1);
        $this->processEvents();
      }
      $this->labels['total']->set_text('Total: '.$result->count());
      $this->cat->debug($result);
      
      $this->labels['succeeded']->set_text('Succeeded: '.$result->successCount());
      foreach ($result->succeeded as $id => $success) {
        $this->updateTest($id, 'succeeded', $success->result);
      }
      $this->labels['skipped']->set_text('Skipped: '.$result->skipCount());
      foreach ($result->skipped as $id => $skipped) {
        $this->updateTest($id, 'skipped', $skipped->reason);
      }
      $this->labels['failed']->set_text('Failed: '.$result->failureCount());
      foreach ($result->failed as $id => $failed) {
        $this->updateTest($id, 'failed', $failed->reason);
      }
      
      $this->setStatusText('Ready');
    }
    
    /**
     * Adds tests from a section
     *
     * @param   util.Properties config
     * @param   string section
     */
    public function addTestsFromSection($config, $section) {
      try {
        $class= lang::XPClass::forName($config->readString($section, 'class'));
      } catch (lang::ClassNotFoundException $e) {
        org::gnome::widgets::MessageBox::display('Section '.$section.': "'.$e->getMessage(), 'Error', MB_OK | MB_ICONERROR);
        exit(-2);
      }

      $this->cat->debug('Processing section', $section);
      $sectionNode= $this->hierarchy->insert_node(
        NULL,
        NULL,
        array($config->getFilename().'::'.$section, ''),
        4,
        $this->pixmaps['p:collection'],
        $this->pixmaps['m:collection'],
        $this->pixmaps['p:collection'],
        $this->pixmaps['m:collection'],
        FALSE,
        TRUE
      );

      for ($methods= $class->getMethods(), $i= 0, $s= sizeof($methods); $i < $s; $i++) {
        if (!$methods[$i]->hasAnnotation('test')) continue;

        if ($methods[$i]->hasAnnotation('ignore')) {
          util::cmd::Console::writeLinef(
            '     >> Ignoring %s::%s (%s)', 
            $class->getName(), 
            $methods[$i]->getName(),
            $methods[$i]->getAnnotation('ignore')
          );
          continue;
        }

        // Add test method
        $this->cat->debugf('     >> Adding test %s::%s', $class->getName(), $methods[$i]->getName());
        $test= call_user_func_array(array($class, 'newInstance'), array_merge(
          (array)$methods[$i]->getName(),
          $config->readArray($section, 'args')
        ));
        
        // Insert into hierarchy tree
        if (!isset($this->node[$section.$test->getClassName()])) {
          $this->node[$section.$test->getClassName()]= $this->hierarchy->insert_node(
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
        $this->node[$test->hashCode()]= $this->hierarchy->insert_node(
          $this->node[$section.$test->getClassName()],
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
    }
    
    /**
     * Add a configuration
     *
     * @param   string uri
     * @param   string section default NULL
     * @return  bool
     */
    public function addConfiguration($uri, $section= NULL) {
      if (isset($this->loaded[$uri])) {
        org::gnome::widgets::MessageBox::display('Cannot load the same test config twice!', 'Error', MB_OK | MB_ICONWARNING);
        return FALSE;
      }
      $this->loaded[$uri]= TRUE;

      $config= new util::Properties($uri);
      if ($section) {
        $this->addTestsFromSection($config, $section);
      } else {
        $section= $config->getFirstSection();
        do {
          $this->addTestsFromSection($config, $section);
        } while ($section= $config->getNextSection());
      }
      
      $this->setStatusText('Configuration '.basename($uri).' added');
      $this->cat->info($this->suite);
      
      return TRUE;
    }
    
    /**
     * Callback
     *
     * @param   php.GtkWidget widget
     */
    public function onSelectClicked($widget) {
      do {
      
        // If anything else than "OK" is pressed in the dialog, break
        if (MB_OK != $this->dialog->show()) break;
      } while (!$this->addConfiguration($this->dialog->getDirectory().$this->dialog->getFileName()));
    }
  }
?>
