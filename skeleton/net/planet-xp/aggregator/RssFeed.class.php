<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'net.planet-xp.aggregator.FeedAggregator',
    'peer.http.HttpConnection',
    'xml.rdf.RDFNewsFeed'
  );

  /**
   * RssFeed aggregator class. This class fetches a feed from a
   * well known URL.
   *
   * @purpose  Aggregate one feed
   */
  class RssFeed extends FeedAggregator {

    /**
     * Aggregate a feed.
     *
     * @model   static
     * @access  public
     * @param   int feed_id
     * @param   string url
     * @param   util.Date lastModified
     * @return  &array
     */
    function &fetch($feed_id, $url, $lastModified= NULL) {
      $params= array();

      // TBI:
      // if (is('util.Date', $lastModified)) {
      //   $params['lastModified']= $date->toString('d.m.Y');
      // }

      try(); {
        $data= '';
        $client= &new HttpConnection($url);
        $response= &$client->get();
        
        while ($response && FALSE !== ($l= $response->readData())) { $data.= $l; }
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      
      // Construct RDF tree
      try(); {
        $rdf= &RDFNewsFeed::fromString($data);
      } if (catch('FormatException', $e)) {
        return throw($e);
      }
      
      $feed= &new Feed(
        $feed_id,
        $rdf->channel->title,
        $rdf->channel->link,
        $rdf->channel->description
      );
      
      $items= array();
      foreach (array_keys($rdf->items) as $idx) {
        $items[]= &new FeedItem(
          $feed_id,
          $rdf->items[$idx]->title,
          $rdf->items[$idx]->link,
          @$rdf->items[$idx]->description,
          @$rdf->items[$idx]->content,
          @$rdf->items[$idx]->author,
          @$rdf->items[$idx]->guid,
          @$rdf->items[$idx]->date
        );
      }
      
      return array(
        'feed'      => &$feed,
        'feeditems' => &$items
      );
    }
  }
?>
