<?php
/* Demonstriert die Benutzung RDFNewsFeed-Klasse
 *
 * Hier ein paar RDF/RSS-URLs zum Testen:
 * - http://linuxtoday.com/backend/my-netscape.rdf
 * - http://www.salon.com/feed/RDF/salon_use.rdf
 * - http://advogato.org/rss/articles.xml
 * - http://barrapunto.com/barrapunto.rdf
 * - http://barrapunto.com/gnome.rdf
 * - http://www.bsdtoday.com/backend/bt.rdf
 * - http://beyond2000.com/b2k.rdf
 * - http://www.cnn.com/cnn.rss
 * - http://www.debianplanet.org/debianplanet/backend.php
 * - http://www.dictionary.com/wordoftheday/wotd.rss
 * - http://www.dvdreview.com/rss/newschannel.rss
 * - http://freshmeat.net/backend/fm.rdf
 * - http://news.gnome.org/gnome-news/rdf
 * - http://headlines.internet.com/internetnews/prod-news/news.rss
 * - http://www.hispalinux.es/backend.php
 * - http://www.kde.org/dotkdeorg.rdf
 * - http://www.kuro5hin.org/backend.rdf
 * - http://linuxgames.com/bin/mynetscape.pl
 * - http://linux.com/mrn/jobs/latest_jobs.rss
 * - http://linuxtoday.com/backend/my-netscape.rdf
 * - http://lwn.net/headlines/rss
 * - http://memepool.com/memepool.rss
 * - http://www.mozilla.org/news.rdf
 * - http://www.mozillazine.org/contents.rdf
 * - http://www.fool.com/about/headlines/rss_headlines.asp
 * - http://www.newsforge.com/newsforge.rss
 * - http://www.nanotechnews.com/nano/rdf
 * - http://www.pigdog.org/pigdog.rdf
 * - http://www.python.org/channews.rdf
 * - http://www.quotationspage.com/data/mqotd.rss
 * - http://www.salon.com/feed/RDF/salon_use.rdf
 * - http://slashdot.org/slashdot.rdf
 * - http://www.theregister.co.uk/tonys/slashdot.rdf
 * - http://www.thinkgeek.com/thinkgeek.rdf
 * - http://www.webreference.com/webreference.rdf
 * - http://redcarpet.ximian.com/red-carpet.rdf
 *
 * Zum einfachen ausprobieren:
 * <pre>
 * for url in `grep '* - ' rdf.php | cut -d ' ' -f 4` ; do php -q rdf.php $url 1 | less ; done
 * </pre>
 * 
 * $Id$
 */
 
  require('lang.base.php');
  uses(
    'xml.rdf.RDFNewsFeed', 
    'io.File'
  );
  
  if (!isset($_SERVER['argv'][1])) die(printf("Usage: %s <rdf_file> [<show_xml>]\n", basename($_SERVER['argv'][0])));
  $file= $_SERVER['argv'][1];
  printf("===> Input %s\n", $file);
  
  $rdf= &new RDFNewsFeed();
  try(); {
    $rdf->fromFile(new File($file));
  } if (catch('Exception', $e)) {
    die($e->printStackTrace());
  }
  
  // Titel
  printf(
    "===> RDF News Channel '%s', located at %s\n".
    "     %s\n".
    "     Language %s, published by %s on %s\n",
    $rdf->channel->title,
    $rdf->channel->link,
    $rdf->channel->description,
    isset($rdf->channel->language) ? $rdf->channel->language : 'unknown',
    isset($rdf->channel->publisher) ? $rdf->channel->publisher : 'unknown',
    isset($rdf->channel->date) ? $rdf->channel->date->toString() : 'unknown'
  );
  
  // Image
  if ($rdf->image) printf(
    "---> Image '%s' source %s\n".
    "     %s\n",
    $rdf->image->title,
    $rdf->image->url,
    $rdf->image->link
  );
  
  // Items
  printf("---> Items [%d]\n", sizeof($rdf->items));
  foreach ($rdf->items as $item) {
    printf("     >> %s\n        [%s]\n", $item->title, $item->link);
  }

  // XML
  if (isset($_SERVER['argv'][2])) echo "===> Source:\n", $rdf->getSource(0);
  echo "\n";
?>
