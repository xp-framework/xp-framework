<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'xml.Tree',
    'util.Date'
  );

  /**
   * RDF- and RSS- newsfeeds
   *
   * Examples of XML source
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
    const
      RDF_NEWS_RDF = 0x0000,
      RDF_NEWS_RSS = 0x0001;

    public 
      $channel,
      $image,
      $items;
      
    public
      $type= RDF_NEWS_RDF;

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct() {
      parent::__construct();
      $this->root= new Node(array(
        'name'          => 'rdf:RDF',
        'attribute'     => array(
          'xmlns:rdf'   => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
          'xmlns:dc'    => 'http://purl.org/dc/elements/1.1/',
          'xmlns'       => 'http://my.netscape.com/rdf/simple/0.9/'
        )
      ));
      $this->channel= new stdClass();
      $this->image= new stdClass();
      $this->items= array();
    }
    
    /**
     * Sets the channel element
     *
     * @access  public
     * @param   string title
     * @param   string link
     * @param   string description default ''
     * @param   string util.Date default NULL date defaulting to the current time
     * @param   string language default '' e.g. en_US, de_DE, fr_FR, ...
     * @param   string creator default ''
     * @param   string publisher default ''
     * @param   string rights default ''
     */
    public function setChannel($title, $link, $description= '', $date= NULL, $language= '',
                        $creator= '', $publisher= '', $rights= '') {
      if (NULL === $date) $date= new Date(time());
      
      $this->channel->title= $title;
      $this->channel->link= $link;
      $this->channel->description= $description;
      $this->channel->date= $date;
      $this->channel->language= $language;
      $this->channel->creator= $creator;
      $this->channel->publisher= $publisher;
      $this->channel->copyright= $rights;
      
      $node= Node::fromArray(array(
        'title'         => $title,
        'link'          => $link,
        'description'   => $description,
        'dc:language'   => $language,
        'dc:date'       => $date->toString('Y-m-d\TH:i:s'),
        'dc:creator'    => $creator,
        'dc:publisher'  => $publisher,
        'dc:rights'     => $rights
      ), 'channel');
      if (!isset($this->channel->node)) $node= $this->root->addChild($node);
      $this->channel->node= $node;
    }
    
    /**
     * Set the channel image
     *
     * @access  public
     * @param   string title
     * @param   string url
     * @param   string link default ''
     */
    public function setImage($title, $url, $link= '') {
      $this->image->title= $title;
      $this->image->url= $url;
      $this->image->title= $title;

      $node= Node::fromArray(array(
        'title'         => $title,
        'url'           => $url,
        'link'          => $link
      ), 'image');
      if (!isset($this->image->node)) $node= $this->root->addChild($node);
      $this->image->node= $node;
    }
    
    /**
     * Adds an item
     *
     * @access  public
     * @param   string title
     * @param   string link
     * @param   string description default ''
     * @param   string util.Date default NULL date defaulting to current date/time
     * @return  object the added item
     */
    public function addItem($title, $link, $description= '', $date= NULL) {
      if (NULL === $date) {
        $date= isset($this->channel->date) ? $this->channel->date : new Date(time());
      }
      
      $item= new stdClass();
      $item->title= $title;
      $item->link= $link;
      $item->description= $description;
      
      $node= Node::fromArray(array(
        'title'         => $title,
        'link'          => $link,
        'description'   => $description,
        'dc:date'       => $date->toString('Y-m-d\TH:i:s')
      ), 'item');
      $item->node= $this->root->addChild($node);
      $this->items[]= $item;
      
      return $item;
    }

    /**
     * Private helper
     *
     * @access  private
     * @return  string path, e.g. /rdf:rdf/item/rc:summary/
     */
    private function _pathname() {
      $path= '';
      for ($i= $this->_cnt; $i> 0; $i--) {
        $path= strtolower($this->_objs[$i]->name).'/'.$path;
      }
      return '/'.$path;
    }

    /**
     * Callback for XML parser
     *
     * @see     xp://xml.XMLParser
     * @access  public
     */
    public function onStartElement($parser, $name, $attrs) {
      parent::onStartElement($parser, $name, $attrs);
      
      switch (self::_pathname()) {
        case '/rss/':
          $this->type= RDF_NEWS_RSS;
          break;
          
        case '/rdf:rdf/':
          $this->type= RDF_NEWS_RDF;
          break;

        case '/rdf:rdf/channel/': 
        case '/rss/channel/':
          $this->channel->node= $this->_objs[$this->_cnt];
          break;
          
        case '/rdf:rdf/image/': 
        case '/rss/image/':
          $this->image->node= $this->_objs[$this->_cnt];
          break;
          
        case '/rdf:rdf/item/': 
        case '/rss/channel/item/':
          $this->items[]= new stdClass();
          $this->items[sizeof($this->items)- 1]->node= $this->_objs[$this->_cnt];
          break;
      }
    }          

    /**
     * Callback for XML parser
     *
     * @see     xp://xml.XMLParser
     * @access  public
     * @throws  lang.FormatException in case an unrecognized element is encountered
     */
    public function onEndElement($parser, $name) {
      static $trans;
      
      $path= self::_pathname();
      parent::onEndElement($parser, $name);
      if ($this->_cnt <= 0) return;

      // &lt; &amp;, &#XX; etc. ersetzen
      if (!isset($trans)) $trans= array_flip(get_html_translation_table(HTML_ENTITIES));
      $cdata= preg_replace(
        '/&#([0-9]+);/me', 
        'chr(\'\1\')', 
        strtr(trim($this->_objs[$this->_cnt+ 1]->content), $trans)
      );
      
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
          $this->channel->date= new Date(str_replace('T', ' ', $cdata));
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
          $this->items[sizeof($this->items)- 1]->date= new Date(str_replace('T', ' ', $cdata));
          break;


        default:
          throw (new FormatException('Unrecognized element "'.$name.'"'));
      }
    }
  }
?>
