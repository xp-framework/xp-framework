<?php
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */
 
  uses('xml.Tree', 'util.Date');
  
  // Verschiedene Typen
  define('RDF_NEWS_RDF',        0x0000);
  define('RDF_NEWS_RSS',        0x0001);
 
  /**
   * Kapselt RDF- und RSS-Newsfeeds
   *
   * Diese sehen bspw. so aus:
   * <xmp>
   * <rdf:RDF
   *  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
   *  xmlns:rc="http://www.ximian.com/"
   *  xmlns="http://my.netscape.com/rdf/simple/0.9/"
   * >
   *   <channel>
   *     <title>Ximian Red Carpet News</title>
   *     <link>http://www.ximian.com/news/</link>
   *     <description>Ximian Red Carpet News</description>
   *   </channel>
   *   <image>
   *     <title>Ximian Red Carpet News</title>
   *     <url>http://redcarpet.ximian.com/html/monkeybutton.png</url>
   *     <link>http://www.ximian.com/news/</link>
   *   </image>
   *   <item>
   *     <title>OpenOffice 1.0 available in Red Carpet</title>
   *     <link>http://www.openoffice.org</link>
   *      <rc:icon>http://red-carpet.ximian.com/openoffice/common/channel.png</rc:icon>
   *      <rc:summary>OpenOffice 1.0 is now available to all Red Carpet users</rc:summary>
   *      <rc:channel>OpenOffice</rc:channel>
   *      <rc:date>1022731200</rc:date>
   *    </item>
   *  </rdf:RDF>
   * </xmp>
   * 
   * <xmp>
   * <rss version="0.91">
   *   <channel>
   *     <title>Advogato</title>
   *     <link>http://www.advogato.org/article/</link>
   *     <description>Recent Advogato articles</description>
   *     <language>en-us</language>
   *     <item>
   *       <title>The positive things happening in Peru</title>
   *       <link>http://www.advogato.org/article/517.html</link>
   *       <description>For most of us, the first mention of free software in Peru [...]</description>
   *     </item>
   *   </channel>
   *   </rss>
   * </xmp>
   *
   * @see http://www.w3.org/RDF/
   * @see http://dublincore.org/2001/08/14/dces#
   * @see http://dublincore.org/2001/08/14/dces_deDE
   */
  class RDFNewsFeed extends Tree {
    var 
      $channel,
      $image,
      $items;
      
    var
      $type= RDF_NEWS_RDF;

    /**
     * Constructor
     *
     * @access public
     */
    function __construct() {
      parent::__construct();
      $this->root= &new Node(array(
        'name'          => 'rdf:RDF',
        'attribute'     => array(
          'xmlns:rdf'   => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
          'xmlns:dc'    => 'http://purl.org/dc/elements/1.1/',
          'xmlns'       => 'http://my.netscape.com/rdf/simple/0.9/'
        )
      ));
      $this->channel= &new stdClass();
      $this->image= &new stdClass();
      $this->items= array();
    }
    
    /**
     * Setzt den Channel
     *
     * @access  public
     * @param   string title Titel
     * @param   string link Link
     * @param   string description default '' Beschreibung
     * @param   string util.Date default NULL Datum, default auf jetzt
     * @param   string language default '' Sprache, bspw. en_US oder de_DE
     * @param   string creator default '' Creator
     * @param   string publisher default '' Publisher
     * @param   string rights default '' Copyright
     */
    function setChannel($title, $link, $description= '', $date= NULL, $language= '',
                        $creator= '', $publisher= '', $rights= '') {
      if (NULL === $date) $date= &new Date(time());
      
      $this->channel->title= $title;
      $this->channel->link= $link;
      $this->channel->description= $description;
      $this->channel->date= $date;
      $this->channel->language= $language;
      $this->channel->creator= $creator;
      $this->channel->publisher= $publisher;
      $this->channel->copyright= $rights;
      
      $node= &Node::fromArray(array(
        'title'         => $title,
        'link'          => $link,
        'description'   => $description,
        'dc:language'   => $language,
        'dc:date'       => $date->toString('Y-m-d\TH:i:s'),
        'dc:creator'    => $creator,
        'dc:publisher'  => $publisher,
        'dc:rights'     => $rights
      ), 'channel');
      if (!isset($this->channel->node)) $node= &$this->root->addChild($node);
      $this->channel->node= &$node;
    }
    
    /**
     * Setzt das Image
     *
     * @access  public
     * @param   string title Titel
     * @param   string url Bild-URL
     * @param   string link default '' Link
     */
    function setImage($title, $url, $link= '') {
      $this->image->title= $title;
      $this->image->url= $url;
      $this->image->title= $title;

      $node= &Node::fromArray(array(
        'title'         => $title,
        'url'           => $url,
        'link'          => $link
      ), 'image');
      if (!isset($this->image->node)) $node= &$this->root->addChild($node);
      $this->image->node= &$node;
    }
    
    /**
     * Fügt ein Element hinzu
     *
     * @access  public
     * @param   string title Titel
     * @param   string link Link
     * @param   string description default '' Beschreibung
     * @param   string util.Date default NULL Datum, default auf jetzt
     * @return  object Das hinzugefügte Element
     */
    function &addItem($title, $link, $description= '', $date= NULL) {
      if (NULL === $date) {
        $date= isset($this->channel->date) ? $this->channel->date : new Date(time());
      }
      
      $item= &new stdClass();
      $item->title= $title;
      $item->link= $link;
      $item->description= $description;
      
      $node= &Node::fromArray(array(
        'title'         => $title,
        'link'          => $link,
        'description'   => $description,
        'dc:date'       => $date->toString('Y-m-d\TH:i:s')
      ), 'item');
      $item->node= &$this->root->addChild($node);
      $this->items[]= &$item;
      
      return $item;
    }

    /**
     * Gibt einen XPath-Ausdruck zurück
     *
     * @access  private
     * @return  string Pfadname, /rdf:rdf/item/rc:summary/
     */
    function _pathname() {
      $path= '';
      for ($i= $this->_cnt; $i> 0; $i--) {
        $path= strtolower($this->_objs[$i]->name).'/'.$path;
      }
      return '/'.$path;
    }

    /**
     * Private Callback-Funktion
     *
     * @access private
     */
    function _pCallStartElement($parser, $name, $attrs) {
      parent::_pCallStartElement($parser, $name, $attrs);
      
      switch ($this->_pathname()) {
        case '/rss/':
          $this->type= RDF_NEWS_RSS;
          break;
          
        case '/rdf:rdf/':
          $this->type= RDF_NEWS_RDF;
          break;

        case '/rdf:rdf/channel/': 
        case '/rss/channel/':
          $this->channel->node= &$this->_objs[$this->_cnt];
          break;
          
        case '/rdf:rdf/image/': 
        case '/rss/image/':
          $this->image->node= &$this->_objs[$this->_cnt];
          break;
          
        case '/rdf:rdf/item/': 
        case '/rss/channel/item/':
          $this->items[]= &new stdClass();
          $this->items[sizeof($this->items)- 1]->node= &$this->_objs[$this->_cnt];
          break;
      }
    }          

    /**
     * Private Callback-Funktion
     *
     * @access private
     */
    function _pCallEndElement($parser, $name) {
      static $trans;
      
      $path= $this->_pathname();
      parent::_pCallEndElement($parser, $name);
      if ($this->_cnt <= 0) return;

      // &lt; &amp;, &#XX; etc. ersetzen
      if (!isset($trans)) $trans= array_flip(get_html_translation_table(HTML_ENTITIES));
      $cdata= preg_replace(
        '/&#([0-9]+);/me', 
        'chr(\'\1\')', 
        strtr(trim($this->_objs[$this->_cnt+ 1]->content), $trans)
      );
      
      // Welches Element?
      $name= strtr(substr($path, 0, -1), array(
        '/rdf:rdf/' => '',
        '/rss/'     => ''
      ));
      switch ($name) {
        case 'channel/title':
          $this->channel->title= $cdata;
          break;
          
        case 'channel/link':
          $this->channel->link= $cdata;
          break;
          
        case 'channel/description':
          $this->channel->description= $cdata;
          break;

        case 'channel/language':
        case 'channel/dc:language':
          $this->channel->language= $cdata;
          break;

        case 'channel/copyright':
        case 'channel/dc:rights':
          $this->channel->copyright= $cdata;
          break;

        case 'channel/pubdate':         // 14 May 2002
        case 'channel/dc:date':         // 2002-07-12T15:59
          $this->channel->date= &new Date(str_replace('T', ' ', $cdata));
          break;

        case 'channel/dc:publisher':
          $this->channel->publisher= $cdata;
          break;

        case 'channel/dc:creator':
          $this->channel->creator= $cdata;
          break;

        case 'channel/image/url':
        case 'image/url':
          $this->image->url= $cdata;
          break;
        
        case 'channel/image/title':
        case 'image/title':
          $this->image->title= $cdata;
          break;

        case 'channel/image/link':
        case 'image/link':
          $this->image->link= $cdata;
          break;

        case 'channel/item/title':
        case 'item/title':
          $this->items[sizeof($this->items)- 1]->title= $cdata;
          break;

        case 'channel/item/description':
        case 'item/description':
          $this->items[sizeof($this->items)- 1]->description= $cdata;
          break;
         
        case 'channel/item/link': 
        case 'item/link':
          $this->items[sizeof($this->items)- 1]->link= $cdata;
          break;
          
        case 'channel/item/date':
        case 'item/dc:date':         // 2002-07-12T15:59
          $this->items[sizeof($this->items)- 1]->date= &new Date(str_replace('T', ' ', $cdata));
          break;


        default:
          // echo '???::'.$name."::\n";
      }
    }
  }
?>
