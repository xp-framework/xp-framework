<?php namespace net\xp_framework\unittest\peer\http;

use unittest\TestCase;
use peer\http\HttpResponse;
use peer\http\Authorizations;
use io\streams\MemoryInputStream;
use security\SecureString;
use peer\http\BasicAuthorization;

class AuthorizationsTest extends TestCase {
  const USER= 'foo';
  const PASS= 'bar';

  public function setUp() {
    $this->cut= new Authorizations();
  }

  #[@test]
  public function create_basic_auth() {
    $res= new HttpResponse(new MemoryInputStream(
      "HTTP/1.1 401 Authentication required.\r\n".
      "WWW-Authenticate: Basic realm=\"Auth me!\"\r\n\r\n"
    ));

    $this->assertInstanceof(
      'peer.http.BasicAuthorization',
      $this->cut->create($res, self::USER, new SecureString(self::PASS))
    );
  }

  #[@test]
  public function create_digest_auth() {
    $res= new HttpResponse(new MemoryInputStream(
      "HTTP/1.1 401 Authentication required.\r\n".
      "WWW-Authenticate: Digest realm=\"Auth me!\", qop=\"auth\", nonce=\"12345\"\r\n\r\n"
    ));

    $this->assertInstanceof(
      'peer.http.DigestAuthorization',
      $this->cut->create($res, self::USER, new SecureString(self::PASS))
    );
  }

  #[@test, @expect('lang.IllegalStateException')]
  public function unknown_type_throws_exception() {
    $res= new HttpResponse(new MemoryInputStream(
      "HTTP/1.1 401 Authentication required.\r\n".
      "WWW-Authenticate: Bloafed realm=\"Auth me!\", qop=\"auth\", nonce=\"12345\"\r\n\r\n"
    ));

    $this->assertInstanceof(
      'peer.http.DigestAuthorization',
      $this->cut->create($res, self::USER, new SecureString(self::PASS))
    );
  }

  #[@test]
  public function requires_a_401() {
    $res= new HttpResponse(new MemoryInputStream('HTTP/1.1 401 Authentication required.'."\r\n\r\n"));
    $this->assertTrue($this->cut->required($res));
  }

  #[@test]
  public function not_required_without_401() {
    $res= new HttpResponse(new MemoryInputStream('HTTP/1.1 200 Ok'."\r\n\r\n"));
    $this->assertFalse($this->cut->required($res));
  }
}