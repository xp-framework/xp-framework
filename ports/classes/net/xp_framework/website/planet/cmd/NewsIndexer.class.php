<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'net.xp_framework.website.planet.cmd.Indexer',
    'net.xp_framework.website.planet.cmd.SqlImportIterator'
  );

  /**
   * Indexes XP Framework news
   *
   * @purpose  Indexer
   */
  class NewsIndexer extends Indexer {
    protected
      $conn= NULL;

    /**
     * Set s9y connection object
     *
     * @param   rdbms.DBConnection conn
     */
    #[@inject(type= 'rdbms.DBConnection', name= 'news')]
    public function setConnection($conn) {
      $this->conn= $conn;
    }

    /**
     * Returns the index iterator
     *
     * @return  de.schlund.intranet.search.index.ImportIterator
     */
    protected function importIterator() {
      return new SqlImportIterator('newsentries', 'id', $this->conn->query('
         select
           entry.id as id,
           entry.title as "title",
           concat(entry.author, " " , entry.body) as "summary"
         from
           serendipity_entries entry
         where isdraft = "false"
      '));
    }
  }
?>
