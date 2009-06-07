<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('org.gnome.GtkGladeDialogWindow');

  // Style: Buttons
  define('MB_OK',               0x0001);
  define('MB_CANCEL',           0x0002);
  define('MB_YES',              0x0004);
  define('MB_NO',               0x0008);
  define('MB_RETRY',            0x0010);
  define('MB_IGNORE',           0x0020);
  define('MB_ABORT',            0x0040);
  
  // Style: Buttons combined
  define('MB_OKCANCEL',         MB_OK | MB_CANCEL);
  define('MB_YESNO',            MB_YES | MB_NO);
  define('MB_YESNOCANCEL',      MB_YES | MB_NO | MB_CANCEL);
  define('MB_RETRYCANCEL',      MB_RETRY | MB_CANCEL);
  
  // Style: Icons  
  define('MB_ICONHAND',         0x0080);
  define('MB_ICONQUESTION',     0x0100);
  define('MB_ICONEXCLAMATION',  0x0200);
  define('MB_ICONASTERISK',     0x0300);
  define('MB_ICONMASK',         MB_ICONHAND | MB_ICONQUESTION | MB_ICONEXCLAMATION | MB_ICONASTERISK);

  // Style: Icons aliases
  define('MB_ICONINFORMATION',  MB_ICONASTERISK);
  define('MB_ICONERROR',        MB_ICONHAND);
  define('MB_ICONWARNING',      MB_ICONEXCLAMATION);
  
  // Style: Default (information icon and OK button)
  define('MB_DEFAULT',          MB_OK | MB_ICONINFORMATION);

  /**
   * Messagebox
   *
   * Usage:
   * <code>
   *   $m= new MessageBox('Hello world', 'Greeting', MB_OK | MB_ICONWARNING);
   *   $pressed= $m->show();
   * </code>
   *
   * Static usage:
   * <code>
   *   $pressed= MessageBox::display('Hello world');
   * </code>
   *
   * @test     xp://net.xp_framework.unittest.runner.gtk.UnitTestUI
   * @purpose  Widget
   */
  class MessageBox extends GtkGladeDialogWindow {
    public
      $message  = '',
      $caption  = '',
      $style    = 0,
      $pressed  = 0,
      $buttons  = array();
  
    /**
     * Constructor
     *
     * @param   string message
     * @param   string caption default 'Message'
     * @param   int style default MB_DEFAULT
     * @param   array buttons default array() user defined buttons
     */
    public function __construct(
      $message, 
      $caption= 'Message', 
      $style= MB_DEFAULT, 
      $buttons= array()
    ) {
      $this->message= $message;
      $this->caption= $caption;
      $this->style= $style;
      $this->buttons= $buttons; // TBI
      parent::__construct(dirname(__FILE__).'/messagebox.glade', 'messagebox');
    }

    /**
     * Public static method
     *
     * @param   string message
     * @param   string caption default 'Message'
     * @param   int style default MB_DEFAULT
     * @param   array buttons default array() user defined buttons
     * @return  int
     */
    public static function display(
      $message, 
      $caption= 'Message', 
      $style= MB_DEFAULT, 
      $buttons= array()
    ) {
      $m= new MessageBox($message, $caption, $style, $buttons);
      return $m->show();
    }
       
    /**
     * Initialize
     *
     */
    public function init() {
      $this->window->set_default_size(320, 140);
      
      // Message
      $this->label= $this->widget('label');
      
      // Icon
      $this->icon= $this->widget('icon');
      $loader= new GTKPixmapLoader($this->window->window, dirname(__FILE__));
      try {
        $this->pixmaps= $loader->load(array(
          'information',
          'warning',
          'question',
          'error'
        ));
      } catch (IOException $e) {
        $this->cat->error($e->getStackTrace());
        
        // Well, we'll be without icons, then
      }
      
      // Action area
      $this->actionarea= $this->window->action_area;
    }
    
    /**
     * Run this
     *
     */
    public function run() {
      static $map= array(
        MB_ICONHAND         => 'error',
        MB_ICONQUESTION     => 'question',
        MB_ICONEXCLAMATION  => 'warning',
        MB_ICONASTERISK     => 'information'
      );

      // Set window title
      $this->window->set_title($this->caption);
      
      // Set message text
      $this->label->set_text($this->message);
      
      // Set icon
      $idx= $map[$this->style & MB_ICONMASK];
      $this->cat->debug($this->style, $this->style & MB_ICONMASK);
      $this->icon->set($this->pixmaps['p:'.$idx], $this->pixmaps['m:'.$idx]);
      
      // Buttons
      foreach (array('OK', 'CANCEL', 'YES', 'NO', 'RETRY', 'IGNORE', 'ABORT') as $name) {
        if ($this->style & constant('MB_'.$name)) {
          $b= new GtkButton(ucfirst(strtolower($name)));    // TBD: Get via gettext?
          $b->set_name($name);
          $b->set_flags(GTK_CAN_DEFAULT);
          $b->show();

          $this->connect($b, 'clicked', 'onButtonClicked');
          $this->actionarea->pack_start($b);
        }
      }
      
      parent::run();
    }
    
    /**
     * Callback for buttons
     *
     * @param   php.GtkWidget widget
     */
    public function onButtonClicked($widget) {
      $this->pressed= constant('MB_'.$widget->get_name());
      $this->close();
    }
    
    /**
     * Show this messagebpx
     *
     * @return  int
     */
    public function show() {
      parent::show();
      return $this->pressed;
    }
  }
?>
