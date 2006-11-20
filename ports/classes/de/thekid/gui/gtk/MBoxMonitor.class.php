<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'org.gnome.GtkGladeApplication',
    'org.gnome.util.GTKWidgetUtil',
    'org.gnome.util.GTKPixmapLoader',
    'peer.mail.store.Pop3Store',
    'peer.mail.store.ImapStore',
    'org.gnome.widgets.MessageBox',
    'peer.URL'
  );

  /**
   * Mail box monitor
   *
   * @ext      gtk
   * @purpose  Application
   */
  class MBoxMonitor extends GTKGladeApplication {
    var
      $stor         = NULL,
      $statusbar    = NULL,
      $list         = NULL,
      $pixmaps      = array(),
      $dsn          = '';
      
    /**
     * Constructor
     *
     * @access  public
     * @param   &util.cmd.ParamString p
     */
    function __construct(&$p) {
      if (!$p->exists(1)) {
        printf(
          "Usage: gtkphp %1\$s protocol://user:password@server\n".
          "       Supported protocols: pop3, imap, imaps\n",
          $p->value(0)
        );
        exit(-1);
      }
    
      // Find protocol
      $this->dsn= &new URL($p->value(1));
      switch ($this->dsn->getScheme()) {
        case 'pop3': 
          $this->stor= &new Pop3Store(); 
          break;
          
        case 'imap': 
        case 'imaps':
          $this->stor= &new ImapStore(); 
          break;
          
        default: 
          printf("Protocol %s not supported\n", $this->dsn->getScheme()); 
          exit(-2);
      }

      parent::__construct($p, dirname($p->value(0)).'/../ui/mboxmonitor.glade');
    }
    
    /**
     * Initialize application
     *
     * @access  public
     */
    function init() {
      parent::init();
      $this->window->set_title(sprintf(
        'MailboxMonitor [%s] %s@%s',
        $this->dsn->getScheme(),
        $this->dsn->getUser(),
        $this->dsn->getHost()
      ));
      
      // Connect buttons
      GTKWidgetUtil::connect($this->widget('button_receive'), array(
        ':clicked'  => array(&$this, 'onReceiveButtonClicked')
      ));
      GTKWidgetUtil::connect($this->widget('button_expunge'), array(
        ':clicked'  => array(&$this, 'onExpungeButtonClicked')
      ));
      
      // Set up additional widgets we need to access via name
      $this->statusbar= &$this->widget('statusbar');
      $this->menu= &$this->widget('menu1');
      GTKWidgetUtil::connectChildren($this->menu, array(
        ':activate'             => array(&$this, 'onMenuItemActivated'),
      ));
      
      $this->tree= &$this->widget('ctree_messages');
      $this->tree->set_row_height(18);
      $this->tree->set_line_style(GTK_CTREE_LINES_NONE);
      $this->tree->set_expander_style(GTK_CTREE_EXPANDER_TRIANGLE);
      GTKWidgetUtil::connect($this->tree, array(
        ':click_column'         => array(&$this, 'onListColumnClicked'),
        ':button_press_event'   => array(&$this, 'onListButtonPressed'),
      ));
      
      // Load art
      $p= &new GTKPixmapLoader($this->window->window, dirname($this->param->value(0)).'/../ui');
      $this->pixmaps= $p->load(array(
        'priority-high',
        'mail-new',
        'mail-deleted',
        'attachment'
      ));
    }
    
    /**
     * Callback for "delete"
     *
     * @access  protected
     * @param   int[] selection
     */
    function onDeleteMenuItemActivated($selection) {
      $this->tree->freeze();
      foreach (array_values($this->tree->selection) as $idx) {
        $msg= &$this->tree->node_get_row_data($idx);
        
        try(); {
          $msg->folder->deleteMessage($msg);
        } if (catch('Exception', $e)) {
          $this->cat->error($e);
          MessageBox::display($e->getMessage(), 'Error', MB_OK | MB_ICONEXCLAMATION, MB_OK | MB_ICONEXCLAMATION);
          $this->setStatusText('Error updating message status');
          continue;
        }
        
        $row= $this->tree->node_get_pixtext($idx, 0);
        $this->tree->node_set_pixtext(
          $idx, 
          0,
          $row[0],
          $row[1],
          $this->pixmaps['p:mail-deleted'], 
          $this->pixmaps['m:mail-deleted']
        );       
      }
      $this->tree->thaw();
    }

    /**
     * Callback for "undelete"
     *
     * @access  protected
     * @param   int[] selection
     */
    function onUndeleteMenuItemActivated($selection) {
      $this->tree->freeze();
      foreach (array_values($this->tree->selection) as $idx) {
        $msg= &$this->tree->node_get_row_data($idx);
        
        try(); {
          $msg->folder->undeleteMessage($msg);
        } if (catch('Exception', $e)) {
          $this->cat->error($e);
          MessageBox::display($e->getMessage(), 'Error', MB_OK | MB_ICONEXCLAMATION);
          $this->setStatusText('Error updating message status');
          continue;
        }
        
        $row= $this->tree->node_get_pixtext($idx, 0);
        $this->tree->node_set_pixtext(
          $idx, 
          0,
          $row[0],
          $row[1],
          $this->pixmaps['p:mail-new'], 
          $this->pixmaps['m:mail-new']
        );       
      }
      $this->tree->thaw();
    }
    
    /**
     * Callback for _all_ menu items in popup menu
     *
     * @access  protected
     * @param   &php.GtkWidget item
     */
    function onMenuItemActivated(&$item) {
      return call_user_func(
        array(&$this, sprintf('on%sMenuItemActivated', ucfirst($item->get_name()))),
        $this->tree->selection
      );
    }
    
    /**
     * Callback for mouse clicks in the list
     *
     * @access  protected
     * @param   &php.GtkWidget widget
     * @param   &php.GdkEvent event
     */
    function onListButtonPressed(&$widget, &$event) {
      if (3 != $event->button || empty($widget->selection)) return;
      
      // Show context menu
      $this->menu->popup(
        NULL, 
        NULL, 
        NULL,
        $event->button,
        $event->time
      );
    }
    
    /**
     * Callback for column clicks
     *
     * @access  protected
     * @param   &php.GtkWidget widget
     * @param   int column
     */
    function onListColumnClicked(&$widget, $column) {
      $this->cat->debug('Column', $column, 'clicked, sorting...');
      $widget->set_sort_column($column);
      $widget->sort();
    }
    
    /**
     * Set statusbar text and waits for the GUI to process
     * whatever events are still pending.
     *
     * @access  protected
     * @param   string fmt
     * @param   mixed* args
     */
    function setStatusText($fmt) {
      $args= func_get_args();
      $text= vsprintf($args[0], array_slice($args, 1));
      $this->statusbar->push(1, $text);
      
      // Leave the GUI time to repaint
      $this->processEvents();
    }
    
    /**
     * Add a message to the list
     *
     * @access  protected
     * @param   &peer.mail.Message msg
     */
    function addMessage(&$msg) {
      static $style= NULL;

      // Set up child row style
      if (!$style) {
        $style= $this->tree->style;
        $style= $style->copy();
        $style->fg[GTK_STATE_NORMAL]= $style->fg[GTK_STATE_INSENSITIVE];
      }
      
      $node= &$this->tree->insert_node(
        NULL, 
        NULL, 
        array(
          NULL,     // Pixmap
          NULL,     // Attachment
          NULL,     // Status
          $msg->from->personal.' <'.$msg->from->localpart.'@'.$msg->from->domain.'>',
          $msg->getSubject(),
          $msg->date->toString('Y/m/d H:i'),
          sprintf('%0.2f KB', $msg->size / 1024)
        ),
        1,
        $this->pixmaps['p:mail-new'],
        $this->pixmaps['m:mail-new'],
        $this->pixmaps['p:mail-new'],
        $this->pixmaps['m:mail-new'],
        FALSE,
        FALSE
      );

      // Attachment
      if (is_a($msg, 'MimeMessage')) $this->tree->node_set_pixtext(
        $node, 
        1,
        'TRUE',
        4,
        $this->pixmaps['p:attachment'], 
        $this->pixmaps['m:attachment']
      );

      // Priority
      if (MAIL_PRIORITY_HIGH >= $msg->priority) $this->tree->node_set_pixtext(
        $node, 
        2,
        $msg->priority,
        4,
        $this->pixmaps['p:priority-high'], 
        $this->pixmaps['m:priority-high']
      );
      
      // Copy message
      $this->tree->node_set_row_data($node, $msg);
      
      foreach (array(TO, CC, BCC) as $type) {
        foreach ($msg->getRecipients($type) as $r) {
          if (!is('InternetAddress', $r)) {
            $this->cat->warn($type, xp::typeOf($r), $msg);
            continue;
          }
          
          $child= &$this->tree->insert_node(
            $node,
            NULL,
            array(
              NULL,     // Pixmap
              NULL,     // Attachment
              NULL,     // Status
              ucfirst($type),
              $r->personal.' <'.$r->localpart.'@'.$r->domain.'>',
              '',
              ''
            ),
            1,
            NULL,
            NULL,
            NULL,
            NULL,
            TRUE,
            FALSE
          );
          $this->tree->node_set_selectable($child, FALSE);
        }
      }
      
      // Add headers
      foreach ($msg->headers as $key => $val) {
        $child= &$this->tree->insert_node(
          $node,
          NULL,
          array(
            NULL,     // Pixmap
            NULL,     // Attachment
            NULL,     // Status
            $key,
            $val,
            '',
            ''
          ),
          1,
          NULL,
          NULL,
          NULL,
          NULL,
          TRUE,
          FALSE
        );
        $this->tree->node_set_selectable($child, FALSE);
        $this->tree->node_set_row_style($child, $style);
      }
      
      // Leave the GUI time to repaint
      $this->processEvents();
    }
    
    
    /**
     * Callback for when expunge button is clicked
     *
     * @access  protected
     * @param   &php.GtkWidget widget
     */
    function onExpungeButtonClicked(&$widget) {
      try(); {
        $this->stor->expunge();
      } if (catch('Exception', $e)) {
        $this->cat->error($e);
        MessageBox::display($e->getMessage(), 'Error', MB_OK | MB_ICONEXCLAMATION);
        $this->setStatusText('Error expunging!');
        return;
      }
      
      $this->setStatusText('Expunged');
      
      // Reread list
      $this->onReceiveButtonClicked($widget);
    }
    
    /**
     * Callback for when receive button is clicked
     *
     * @access  protected
     * @param   &php.GtkWidget widget
     */
    function onReceiveButtonClicked(&$widget) {

      // Set receive button unsensitive
      $r= &$this->widget('button_receive');
      $r->set_sensitive(FALSE);
      
      // Freeze list
      $this->tree->set_sensitive(FALSE);
      $this->tree->freeze();
      
      try(); {
        $this->stor->cache->expunge();
        
        // Check if a mailbox was specified. Else, use "INBOX"
        if ('' === ($mbox= substr($this->dsn->getPath(), 1))) {
          $mbox= 'INBOX';
        }
        $this->cat->info('Using mailbox', $mbox);
        
        if (!$this->stor->isConnected()) {
          $this->setStatusText('Connecting...');
          $this->stor->connect($this->dsn->getURL()); 
        }
        
        $this->setStatusText('Getting folder contents for '.$mbox);
        if ($f= &$this->stor->getFolder($mbox)) {
          $f->open();
          
          $this->tree->clear();
          while ($msg= &$f->getMessage()) {
            $this->setStatusText('Retrieving message %s', $msg->uid);
            $this->addMessage($msg);
          }
        }
      } if (catch('Exception', $e)) {
        $this->setStatusText('Error retrieving messages');
        MessageBox::display($e->getMessage(), 'Error', MB_OK | MB_ICONEXCLAMATION);
        $this->cat->error($e);
        $r->set_sensitive(TRUE);
        
        // Unfreeze list
        $this->tree->set_sensitive(TRUE);
        $this->tree->thaw();
        return;
      }
      
      // Set receive button sensitive again
      $r->set_sensitive(TRUE);
      
      // Set expunge button sensitive
      $e= &$this->widget('button_expunge');
      $e->set_sensitive(TRUE);
      
      // Unfreeze list
      $this->tree->set_sensitive(TRUE);
      $this->tree->thaw();
      
      $this->setStatusText('Done');
    }
    
    /**
     * Close application
     *
     * @access  public
     */
    function done() {
      if ($this->stor->isConnected()) {
        $this->stor->close();
      }
      parent::done();
    }
  }
?>
