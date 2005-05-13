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
      $headers= array();

      if (is('util.Date', $lastModified))
        $headers[]= &new Header('If-Modified-Since', $lastModified->toString('r'));

      try(); {
        $data= '';
        $client= &new HttpConnection($url);
        $response= &$client->get(NULL, $headers);
        
        while ($response && FALSE !== ($l= $response->readData())) { $data.= $l; }
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      
      if ($response && HTTP_NOT_MODIFIED == $response->getStatusCode()) {
        return TRUE;
      }
      
      if ($response && HTTP_OK != $response->getStatusCode()) {
        return throw(new IllegalStateException('Stream broken: HTTP-Status is '.$response->getStatusCode()));
      }
      
      // Construct RDF tree
      try(); {
      
        // Decode data if necessary:
        if (
          ($ctype= $response->getHeader('Content-Type')) && 
          preg_match('/.*; charset=(.*)$/', $ctype, $match) &&
          0 == strcasecmp('utf-8', $match[1])
        ) {
          $data= utf8_decode($data);
        }
      
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
