<?php
/* This class is part of the XP framework
 *
 * $Id: HttpUtil.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace peer::http;

  ::uses(
    'peer.http.HttpConnection',
    'peer.http.UnexpectedResponseException'
  );
  
  define('REDIRECT_LIMIT',      0xA);

  /**
   * The HttpUtil class provides an easy way to retrieve a complete 
   * URL's contents into a string.
   *
   * Example:
   * <code>
   *   uses('peer.http.HttpUtil');
   *
   *   try(); {
   *     $buf= HttpUtil::get(new HttpConnection('http://localhost/'));
   *   } if (catch('UnexpectedResponseException', $e)) {
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   *
   *   echo $buf;
   * </code>
   *
   * @see      xp://peer.http.HttpConnection
   * @purpose  Utility class
   */
  class HttpUtil extends lang::Object {
  
    /**
     * Fetch an URL's content. Follows redirects up until the defined 
     * constant REDIRECT_LIMIT times.
     *
     * @param   peer.http.HttpConnection connection
     * @param   array params default array()
     * @param   array headers default array()
     * @return  string
     * @throws  peer.http.UnexpectedResponseException
     */
    public static function get($connection, $params= array(), $headers= array()) {
      $redirected= 0;
      do {
        try {
          $response= $connection->get($params, $headers);
        } catch (::Exception $e) {
          throw(new UnexpectedResponseException(
            $e->getMessage(),
            -1
          ));
        }

        // Check return code
        switch ($sc= $response->getStatusCode()) {
          case 200:             // 200 OK - fetch data
            $content= '';
            while (FALSE !== ($buf= $response->readData())) {
              $content.= $buf;
            }      
            return $content;

          case 301:             // 301 Moved permanently or
          case 302:             // 302 Moved temporarily - redirect
            if (!($loc= $response->getHeader('Location'))) {
              throw(new UnexpectedResponseException(
                'Redirect status '.$sc.', but no location header in '.$response->toString(),
                $sc
              ));
            }
            if ($redirected >= REDIRECT_LIMIT) {
              throw(new UnexpectedResponseException(
                'Redirection limit ('.REDIRECT_LIMIT.') reached @ '.$loc,
                $sc
              ));
            }
            $redirected++;
            $connection->request= HttpRequestFactory::factory(new peer::URL($loc));
            break;

          default:              // Any other code
            throw(new UnexpectedResponseException(
              'Unexpected answer '.$response->toString(),
              $sc
            ));
        }
      } while ($redirected < REDIRECT_LIMIT + 1);
    }
  }
?>
