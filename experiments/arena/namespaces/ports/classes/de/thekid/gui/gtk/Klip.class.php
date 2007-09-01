<?php
/* This file is part of the klip port
 *
 * $Id: Klip.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace de::thekid::gui::gtk;

  define('XML_HEADER',  '<?xml version="1.0" encoding="iso-8859-1"?>');

  ::uses(
    'org.gnome.GtkGladeApplication',
    'org.gnome.util.GTKPixmapLoader',
    'peer.http.HttpConnection',
    'io.File',
    'util.Hashmap',
    'lang.System',
    'xml.XSLProcessor',
    'xml.rdf.RDFNewsFeed'
  );

  /**
   * A viewer for a single klip 
   *
   * @see      http://www.serence.com/site.php?action=ser_products,prod_klipfolio
   * @ext      gtk
   * @purpose  Klip viewer
   */
  class Klip extends  {
    public
      $conn     = NULL,
      $proc     = NULL,
      $view     = NULL,
      $visited  = NULL,
      $timer    = NULL,
      $prop     = NULL;
      
    /**
     * Constructor
     *
     * @param   &util.cmd.ParamString p
     * @param   &util.Properties prop
     */
    public function __construct($p, $prop) {
      parent::__construct($p, dirname($p->value(0)).'/../ui/klip.glade', 'main');

      // Initialize visited hashmap to an empty set
      $this->visited= new util::Hashmap();
      
      // Set up HTTP connection from property file
      $this->conn= new peer::http::HttpConnection($prop->readString('settings', 'url'));
      
      // Set refresh interval, defaulting to 60 seconds
      $timer= ::timeout_add(
        $prop->readString('settings', 'refresh', 60) * 1000,
        array($this, 'onRefresh')
      );

      // Set up XSL processor
      $this->proc= new xml::XSLProcessor();
      $this->proc->setXSLFile(sprintf(
        '%s/../ui/skins/%s.xsl',
        dirname($p->value(0)),
        $p->value('skin', 's', $prop->readString('settings', 'skin', 'default'))
      ));
      $this->proc->setSchemeHandler(array('get_all' => array($this, 'onScheme')));
      
      $this->prop= $prop;
    }
    
    /**
     * Initialize application
     *
     */
    public function init() {
      parent::init();
      
      // Try to remove all decorations
      with ($w= $this->window->window); {
        method_exists($w, 'set_decorations') && $w->set_decorations(0);
      }

      // Set up GtkHTML
      $this->view= new ();
      $this->view->connect_url_request(array($this, 'onURLRequest'));
      $this->view->connect('link-clicked', array($this, 'onLinkClicked'));
      $this->view->connect('title-changed', array($this, 'onTitleChanged'));
      with ($stream= $this->view->begin()); {
        $this->streamWrite($stream, '<html></html>');
        $this->view->end($stream, GTK_HTML_STREAM_OK);
      }

      // Add the GTKHtml widget to the viewport container and
      // connect the "external" scrollbar
      with ($viewport= $this->widget('viewport'), $scroll= $this->widget('scroll')); {
        $viewport->add($this->view);
        $scroll->set_adjustment($viewport->get_vadjustment());
      }
      
      // Connect refresh button's clicked event
      $this->connect($this->widget('btn_refresh'), 'after:clicked', 'onRefresh');
      
      // Refresh view
      $this->processEvents();
      $this->onRefresh($this->window);
    }
    
    /**
     * Scheme handler
     *
     * @param   &resource proc
     * @param   string scheme
     * @param   string rest
     * @return  string
     */
    public function onScheme($proc, $scheme, $rest) {
      switch ($scheme) {
        case 'rdf':
          $this->cat->debug('Retrieving', $scheme.'('.$rest.')');
          $c= new peer::http::HttpConnection(substr($rest, 1));
          try {
            if ($response= $c->get()) {
              $this->cat->debug('Response headers:', $response->getHeaders());
              $src= '';
              while ($buf= $response->readData(0x2000, $binary= TRUE)) {
                $src.= $buf;
              }
              
              // Parse into a RDFNewsFeed object and create XML strings
              if ($rdf= xml::rdf::RDFNewsFeed::fromString($src)) {
                $s= sizeof($rdf->items);
                $xml= $rdf->getDeclaration().sprintf('<items count="%d">', $s);
                for ($i= 0; $i < $s; $i++) {
                  $xml.= sprintf(
                    '<item link="%s" read="%d">%s</item>',
                    htmlspecialchars($rdf->items[$i]->link),
                    $this->visited->get($rdf->items[$i]->link),
                    htmlspecialchars($rdf->items[$i]->title)
                  );
                }
                $xml.= '</items>';
              }
            }
          } catch (io::IOException $e) {
            $this->cat->error('Retrieving url', $url, 'failed:', $e);
            $xml= '<items count="0" error="retrieval-error:'.$e->getMessage().'"/>';
          } catch (::Exception $e) {
            $this->cat->error('Format error in', $url, ':', $e, $src);
            $xml= '<items count="0" error="format-error:'.$e->getMessage().'"/>';
          }
          delete($c);
          break;
        
        default:
          $this->cat->warn('Unsupported scheme', $scheme, 'rest', $rest);
          $xml= '<items count="0" error="not-supported:'.$scheme.'"/>';
      }

      $this->cat->debug('Returning', $xml);
      return $xml;
    }
    
    /**
     * Callback for when a link is clicked
     *
     * @param   &php.gtk.GtkWidget widget
     * @param   string url
     */
    public function onLinkClicked($widget, $url) {
      $this->cat->debug('Clicked:', $url, 'in widget', $widget);
      $this->visited->put($url, TRUE);

      try {
        lang::System::exec(
          sprintf($this->prop->readString('settings', 'browser', 'galeon %s'), $url),
          '2>/dev/null 1>/dev/null', 
          TRUE
        );
      } catch ( $e) {
        $this->cat->error('Opening browser failed', $e);
        return FALSE;
      }

      // Refresh view to reflect changes in read/unread status
      $this->refreshView();
      return TRUE;
    }

    /**
     * Callback for when the title changes
     *
     * @param   &php.gtk.GtkWidget widget
     * @param   string title
     */
    public function onTitleChanged($widget, $title) {
      $this->window->set_title('Klip: '.$title);
    }
    
    /**
     * Callback for when a URL is requested
     *
     * @param   string uri
     * @param   &php.gtk.GtkHTMLStream stream
     */
    public function onURLRequest($uri, $stream) {
      $this->cat->debug('Requested:', $uri, $stream);
      
      $url= new peer::URL($uri);
      switch ($url->getScheme()) {
        case 'chrome':
          $f= new io::File(dirname($this->param->value(0)).'/../ui/'.$url->getPath());
          $this->cat->debug('Loading chrome ui element', $url->getURL());
          try {
            $f->open(FILE_MODE_READ);
            $this->streamWrite($stream, $f->read($f->size()), $f->size());
            $f->close();
          } catch (io::IOException $e) {
            $this->cat->error('Loading chrome element', $g->getURI(), 'failed:', $e);
            // Fall through
          }
          delete($f);
          break;
        
        case 'http':
          $c= new peer::http::HttpConnection($url);
          $this->cat->debug('Loading HTTP element', $url);
          try {
            if ($response= $c->get()) {
              $this->cat->debug('Response headers:', $response->getHeaders());
              while ($buf= $response->readData(0x2000, $binary= TRUE)) {
                $this->streamWrite($stream, $buf);
              }
            }
          } catch (io::IOException $e) {
            $this->cat->error('Retrieving url', $url, 'failed:', $e);
            // Fall through
          }
          delete($c);
          break;
      }
      
      $this->view->end($stream, GTK_HTML_STREAM_OK);
    }
    
    /**
     * Helper method that writes to a given stream, calculating the length 
     * if omitted.
     *
     * @param   php.gtk.GtkHTMLStream stream
     * @param   string str
     * @param   int len default -1
     * @return  mixed
     */
    public function streamWrite($stream, $str, $len= -1) {
      return $this->view->write($stream, $str, (-1 == $len ? strlen($str) : $len));
    }
    
    /**
     * Refresh view - run processor on previously downloaded information
     *
     */
    public function refreshView() {
      try {
        $this->proc->run();
      } catch ( $e) {
        $this->cat->error($e);
        $this->cat->debug($this->proc);
        return;
      }
      
      // Update GtkHTML view
      with ($html= $this->proc->output(), $stream= $this->view->begin()); {
        $this->cat->debug('Writing to', $this->view, 'html', strlen($html), 'bytes');
        $this->streamWrite($stream, $html);
        $this->view->end($stream, GTK_HTML_STREAM_OK);
      }
    }
    
    /**
     * Callback for when btn_refresh widget is clicked. Also handles
     * the interval timer, thus always returning TRUE to keep the timer
     * going...
     *
     * @param   &php.gtk.GtkWidget widget
     * @return  bool
     */
    public function onRefresh($widget) {
      
      // Retrieve klip. Prepend header to make encoding default to iso-8859-1,
      // map some known characters (Klip looks like XML but actually isn't 
      // conform - the KlipFolio parser seems to ignore this...) and strip off
      // junk after document element
      try {
        if ($response= $this->conn->get()) {
          $klipsrc= XML_HEADER;
          while ($buf= $response->readData()) {
            $klipsrc.= strtr($buf, array(
              '©'     => '&#169;'
            ));
            $this->processEvents();
          }
          
          $this->proc->setXMLBuf(substr($klipsrc, 0, strpos($klipsrc, '</klip>')+ 7));
        }
      } catch (::Exception $e) {
        $this->cat->error($e);
        return TRUE;
      }
      
      $this->refreshView();
      return TRUE;
    }
  }
?>
