<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('security.checksum.MD5');

  /**
   * Class representing an planet-xp.net feeditem.
   *
   * @purpose  Aggregate feeds
   */
  class FeedItem extends Object {
    var
      $feeditem_id=       0,
      $feed_id=           0,
      $title=             NULL,
      $link=              NULL,
      $description=       NULL,
      $content=           NULL,
      $author=            NULL,
      $guid=              NULL,
      $published=         NULL;
    
    /**
     * Constructor.
     *
     * @access  public
     * @param   int feed_id
     * @param   string title
     * @param   string link
     * @param   string content
     * @param   string author
     * @param   string guid the global unique id
     * @param   &util.Date pubDate
     */
    function __construct($feed_id, $title, $link, $description, $content, $author, $guid, &$pubDate) {
      $this->feed_id= $feed_id;
      $this->title= $title;
      $this->link= $link;
      
      if (!empty($description)) $this->description= $description;
      if (!empty($content)) $this->content= $content;
      if (!empty($author)) $this->author= $author;
      
      // Set a Global Unique ID (or use link which should be unique, too).
      $this->guid= (empty($guid) ? $link : $guid);
      
      // Set a published date, default to NULL.
      if (is('util.Date', $pubDate)) $this->published= &$pubDate;
    }

    /**
     * Generate a GUID - a global unique id - for the article. If the feed
     * itself provided such a GUID, use this, otherwise fallback to use it's
     * URL as GUID (which may or may not be safe).
     *
     * @access  public
     * @return  string guid
     */    
    function uniqueId() {
      if (!empty ($this->guid)) return $this->guid;
      return MD5::fromString($this->feed_id.$this->link);
    }
    
    /**
     * Return content for feed.
     *
     * @access  public
     * @return  string content
     */
    function getContent() {
      if (!empty ($this->content)) return $this->content;
      if (!empty ($this->description)) return $this->description;
      
      return NULL;
    }
    
    /**
     * Synchronizes the feed with the database.
     *
     * @access  public
     * @return  int affected_rows
     */
    function update() {
      $cm= &ConnectionManager::getInstance();
      
      // Check XML conformance of entry
      try(); {
        $parser= &new XMLParser();
        $content= str_replace('&', '&amp;', $this->getContent());
        
        // Parse content
        $parser->parse(
          "<?xml version=\"1.0\" ?>\n".
          "<root>".$content."</root>"
        );
      } if (catch('XMLFormatException', $e)) {
        
        // Content is not well-formed, sanitize
        $content= strip_tags($content, '<a><br>');
        // $content= preg_replace('#<br(break=["\']all["\']| )>#', '<br\1/>', $content);
      }
      
      $content= wordwrap($content, 80);
      
      try(); {
        $db= &$cm->getByHost('syndicate', 0);
        
        // When updating, only override publish date, if it's being explicitly
        // given. When defaulting to NULL (and thus to now()), omit it on later syncs.
        $cnt= $db->update('
            syndicate.feeditem
          set
            title= %s,
            content= %s,
            link= %s,
            author= %s,
            lastchange= %s,
            changedby= %s
            %c
          where feed_id= %d
            and guid= %s',
          $this->title,
          $content,
          $this->link,
          $this->author,
          Date::now(),
          __CLASS__,
          (NULL !== $this->published ? $db->prepare(', published= %s', $this->published) : ''),
          $this->feed_id,
          $this->uniqueId()
        );

        if (0 === $cnt) {
          $cnt= $db->insert('
            syndicate.feeditem (
              feed_id,
              title,
              content,
              link,
              author,
              published,
              guid,
              lastchange,
              changedby
            ) values (
              %d,
              %s,
              %s,
              %s,
              %s,
              %s,
              %s,
              %s,
              %s
            )',
            $this->feed_id,
            $this->title,
            $content,
            $this->link,
            $this->author,
            (NULL !== $this->published ? $this->published : Date::now()),
            $this->uniqueId(),
            Date::now(),
            __CLASS__
          );
        }
      } if (catch('SQLException', $e)) {
        return throw($e);
      }

      return $cnt;
    }
  }
?>
