<?php
/* This file is part of the klip port
 *
 * $Id$
 */

  uses(
    'gui.gtk.GtkGladeApplication',
    'gui.gtk.util.GTKPixmapLoader',
    'peer.http.HttpConnection',
    'io.File',
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
  class Klip extends GTKGladeApplication {
    var
      $conn = NULL,
      $proc = NULL,
      $view = NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   &util.cmd.ParamString p
     */
    function __construct(&$p) {
      parent::__construct($p, dirname($p->value(0)).'/../ui/klip.glade', 'main');

      // Set up HTTP connection
      $this->conn= &new HttpConnection($p->value(1));

      // Set up XSL processor
      $this->proc= &new XSLProcessor();
      $this->proc->setXSLFile(dirname($p->value(0)).'/../ui/skins/'.$p->value('skin', 's', 'default.xsl'));
      $this->proc->setSchemeHandler(array('get_all' => array(&$this, 'onScheme')));
    }
    
    /**
     * Initialize application
     *
     * @access  public
     */
    function init() {
      parent::init();

      // Set up GtkHTML
      $this->view= &new GtkHtml();
      $this->view->connect_url_request(array(&$this, 'onURLRequest'));
      $this->view->connect('link-clicked', array(&$this, 'onLinkClicked'));
      $this->view->connect('title-changed', array(&$this, 'onTitleChanged'));
      with ($stream= &$this->view->begin()); {
        $this->streamWrite($stream, '<html></html>');
        $this->view->end($stream, GTK_HTML_STREAM_OK);
      }

      // Add the GTKHtml widget to the scroll container
      with ($scroll= &$this->widget('scroll')); {
        $scroll->add($this->view);
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
     * @access  protected
     * @param   &resource proc
     * @param   string scheme
     * @param   string rest
     * @return  string
     */
    function onScheme(&$proc, $scheme, $rest) {
      switch ($scheme) {
        case 'rdf':
          $this->cat->debug('Retrieving', $scheme.'('.$rest.')');
          $c= &new HttpConnection(substr($rest, 1));
          try(); {
            if ($response= &$c->get()) {
              $this->cat->debug('Response headers:', $response->getHeaders());
              $src= '';
              while ($buf= $response->readData(0x2000, $binary= TRUE)) {
                $src.= $buf;
              }
              
              // Parse into a RDFNewsFeed object and create XML strings
              if ($rdf= &RDFNewsFeed::fromString($src)) {
                $s= sizeof($rdf->items);
                $xml= $rdf->getDeclaration().sprintf('<items count="%d">', $s);
                for ($i= 0; $i < $s; $i++) {
                  $xml.= sprintf(
                    '<item link="%s">%s</item>',
                    $rdf->items[$i]->link,
                    $rdf->items[$i]->title
                  );
                }
                $xml.= '</items>';
              }
            }
          } if (catch('IOException', $e)) {
            $this->cat->error('Retrieving url', $url, 'failed:', $e);
            $xml= '<items count="0" error="retrieval-error:'.$e->getMessage().'"/>';
          } if (catch('Exception', $e)) {
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
     * @access  protected
     * @param   &php.gtk.GtkWidget widget
     * @param   string url
     */
    function onLinkClicked(&$widget, $url) {
      $this->cat->debug('Clicked:', $url, 'in widget', $widget);
      
      // TBI: Make browser command line and redirection configurable
      try(); {
        System::exec('galeon '.$url, '2>/dev/null 1>/dev/null', TRUE);
      } if (catch('SystemException', $e)) {
        $this->cat->error('Opening browser failed', $e);
        return FALSE;
      }
      return TRUE;
    }

    /**
     * Callback for when the title changes
     *
     * @access  protected
     * @param   &php.gtk.GtkWidget widget
     * @param   string title
     */
    function onTitleChanged(&$widget, $title) {
      $this->window->set_title('Klip: '.$title);
    }
    
    /**
     * Callback for when a URL is requested
     *
     * @access  protected
     * @param   string uri
     * @param   &php.gtk.GtkHTMLStream stream
     */
    function onURLRequest($uri, &$stream) {
      $this->cat->debug('Requested:', $uri, $stream);
      
      $url= &new URL($uri);
      switch ($url->getScheme()) {
        case 'chrome':
          $f= &new File(dirname($this->param->value(0)).'/../ui/'.$url->getPath());
          $this->cat->debug('Loading chrome ui element', $url);
          try(); {
            $f->open(FILE_MODE_READ);
            $this->streamWrite($stream, $f->read($f->size()), $f->size());
            $f->close();
          } if (catch('IOException', $e)) {
            $this->cat->error('Loading chrome element', $g->getURI(), 'failed:', $e);
            // Fall through
          }
          delete($f);
          break;
        
        case 'http':
          $c= &new HttpConnection($url);
          $this->cat->debug('Loading HTTP element', $url);
          try(); {
            if ($response= &$c->get()) {
              $this->cat->debug('Response headers:', $response->getHeaders());
              while ($buf= $response->readData(0x2000, $binary= TRUE)) {
                $this->streamWrite($stream, $buf);
              }
            }
          } if (catch('IOException', $e)) {
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
     * @access  private
     * @param   php.gtk.GtkHTMLStream stream
     * @param   string str
     * @param   int len default -1
     * @return  mixed
     */
    function streamWrite(&$stream, $str, $len= -1) {
      return $this->view->write($stream, $str, (-1 == $len ? strlen($str) : $len));
    }
    
    /**
     * Callback for when btn_refresh widget is clicked
     *
     * @access  protected
     * @param   &php.gtk.GtkWidget widget
     */
    function onRefresh(&$widget) {
      
      // Retrieve klip. Prepend header to make encoding default to iso-8859-1,
      // map some known characters (Klip looks like XML but actually isn't 
      // conform - the KlipFolio parser seems to ignore this...) and strip off
      // junk after document element
      try(); {
        if ($response= &$this->conn->get()) {
          $klipsrc= '<?xml version="1.0" encoding="iso-8859-1"?>';
          while ($buf= $response->readData()) {
            $klipsrc.= strtr($buf, array(
              '©'     => '&#169;'
            ));
            $this->processEvents();
          }
          
          $this->proc->setXMLBuf(substr($klipsrc, 0, strpos($klipsrc, '</klip>')+ 7));
          $this->proc->run();
        }
      } if (catch('TransformerException', $e)) {
        $this->cat->error($e);
        $this->cat->debug($klipsrc);
        return FALSE;
      } if (catch('Exception', $e)) {
        $this->cat->error($e);
        return FALSE;
      }
      
      // Update GtkHTML view
      with ($html= $this->proc->output(), $stream= &$this->view->begin()); {
        $this->cat->debug('Writing to', $this->view, 'html', strlen($html), 'bytes');
        $this->streamWrite($stream, $html);
        $this->view->end($stream, GTK_HTML_STREAM_OK);
      }
    }
  }
?>
