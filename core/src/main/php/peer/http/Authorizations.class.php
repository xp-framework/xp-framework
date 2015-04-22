<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'lang.XPClass',
    'lang.reflect.TargetInvocationException',
    'security.SecureString',
    'peer.http.HttpResponse',
    'peer.http.HttpConstants',
    'lang.IllegalStateException'
  );

  /**
   * Authorization factory class for HTTP
   *
   */
  class Authorizations extends Object {
    const AUTH_HEADER= 'WWW-Authenticate';
    protected $impl= array(
      array('startsWith' => 'Basic ', 'impl' => 'peer.http.BasicAuthorization'),
      array('startsWith' => 'Digest ', 'impl' => 'peer.http.DigestAuthorization')
    );

    public function required(HttpResponse $response) {
      return HttpConstants::STATUS_AUTHORIZATION_REQUIRED == $response->getStatusCode();
    }

    public function create(HttpResponse $response, $user, SecureString $pass) {
      if (!$this->required($response)) {
        throw new IllegalStateException('Request had not been rejected, will not create authorization.');
      }

      if (1 != sizeof($response->header(self::AUTH_HEADER))) {
        throw new IllegalStateException('No authentication type indicated.');
      }

      $header= this($response->header(self::AUTH_HEADER), 0);
      foreach ($this->impl as $impl) {
        if (0 == strncmp($impl['startsWith'], $header, strlen($impl['startsWith']))) {
          try {
            return XPClass::forName($impl['impl'])->getMethod('fromChallenge')->invoke(
              null,
              array($header, $user, $pass)
            );
          } catch (TargetInvocationException $e) {
            throw $e->getCause();
          }
        }
      }

      throw new IllegalStateException('Unknown authorization type.');
    }

    /**
     * Create authorization from challenge data from given
     * HTTP request.
     *
     * @param  peer.http.HttpResponse $response
     * @param  string $user
     * @param  security.SecureString $pass
     * @return peer.http.Authorization
     * @throws lang.IllegalStateException If request hadn't challenged
     * @throws lang.IllegalStateException If HTTP status not equal 401
     * @throws lang.IllegalStateException If Unknown authorization type was used
     */
    public static function fromResponse(HttpResponse $response, $user, SecureString $pass) {
      return create(new self())->create($response, $user, $pass);
    }
  }
?>