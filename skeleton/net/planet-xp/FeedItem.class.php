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
      $title=             '',
      $link=              '',
      $content=           '',
      $author=            '',
      $guid=              '',
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
    function __construct($feed_id, $title, $link, $content, $author, $guid, &$pubDate) {
      $this->feed_id= $feed_id;
      $this->title= $title;
      $this->link= $link;
      $this->content= $content;
      $this->author= $author;
      $this->guid= $guid;
      $this->published= &$pubDate;
    }

    /**
     * Generate a GUID - a global unique id - for the article. If the feed
     * itself provided such a GUID, use this, otherwise fallback to calculate
     * with the MD5 algorithm.
     *
     * @access  public
     * @return  string guid
     */    
    function uniqueId() {
      if (!empty ($this->guid)) return $this->guid;
      return MD5::fromString($this->feed_id.$this->link);
    }
    
    /**
     * Synchronizes the feed with the database.
     *
     * @access  public
     * @return  int affected_rows
     */
    function update() {
      $cm= &ConnectionManager::getInstance();
      try(); {
        $db= &$cm->getByHost('syndicate', 0);
        
        $cnt= $db->update('
            syndicate.feeditem
          set
            title= %s,
            content= %s,
            link= %s,
            author= %s,
            published= %s,
            lastchange= %s,
            changedby= %s
          where feed_id= %d
            and guid= %s',
          $this->title,
          $this->content,
          $this->link,
          $this->author,
          $this->published,
          Date::now(),
          __CLASS__,
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
            $this->content,
            $this->link,
            $this->author,
            $this->published,
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
