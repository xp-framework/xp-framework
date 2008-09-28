<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  // HTTP methods
  define('HTTP_GET',     'GET');
  define('HTTP_POST',    'POST');
  define('HTTP_HEAD',    'HEAD');
  define('HTTP_PUT',     'PUT');
  define('HTTP_DELETE',  'DELETE');
  define('HTTP_OPTIONS', 'OPTIONS');
  define('HTTP_TRACE',   'TRACE');
  define('HTTP_CONNECT', 'CONNECT');

  // HTTP status codes
  define('HTTP_CONTINUE',                          100);
  define('HTTP_SWITCHING_PROTOCOLS',               101);
  define('HTTP_PROCESSING',                        102);
  define('HTTP_OK',                                200);
  define('HTTP_CREATED',                           201);
  define('HTTP_ACCEPTED',                          202);
  define('HTTP_NON_AUTHORITATIVE_INFORMATION',     203);
  define('HTTP_NO_CONTENT',                        204);
  define('HTTP_RESET_CONTENT',                     205);
  define('HTTP_PARTIAL_CONTENT',                   206);
  define('HTTP_MULTI_STATUS',                      207);
  define('HTTP_MULTIPLE_CHOICES',                  300);
  define('HTTP_MOVED_PERMANENTLY',                 301);
  define('HTTP_FOUND',                             302);
  define('HTTP_SEE_OTHER',                         303);
  define('HTTP_NOT_MODIFIED',                      304);
  define('HTTP_USE_PROXY',                         305);
  define('HTTP_TEMPORARY_REDIRECT',                307);
  define('HTTP_BAD_REQUEST',                       400);
  define('HTTP_AUTHORIZATION_REQUIRED',            401);
  define('HTTP_PAYMENT_REQUIRED',                  402);
  define('HTTP_FORBIDDEN',                         403);
  define('HTTP_NOT_FOUND',                         404);
  define('HTTP_METHOD_NOT_ALLOWED',                405);
  define('HTTP_NOT_ACCEPTABLE',                    406);
  define('HTTP_PROXY_AUTHENTICATION_REQUIRED',     407);
  define('HTTP_REQUEST_TIME_OUT',                  408);
  define('HTTP_CONFLICT',                          409);
  define('HTTP_GONE',                              410);
  define('HTTP_LENGTH_REQUIRED',                   411);
  define('HTTP_PRECONDITION_FAILED',               412);
  define('HTTP_REQUEST_ENTITY_TOO_LARGE',          413);
  define('HTTP_REQUEST_URI_TOO_LARGE',             414);
  define('HTTP_UNSUPPORTED_MEDIA_TYPE',            415);
  define('HTTP_REQUESTED_RANGE_NOT_SATISFIABLE',   416);
  define('HTTP_EXPECTATION_FAILED',                417);
  define('HTTP_UNPROCESSABLE_ENTITY',              422);
  define('HTTP_LOCKED',                            423);
  define('HTTP_FAILED_DEPENDENCY',                 424);
  define('HTTP_INTERNAL_SERVER_ERROR',             500);
  define('HTTP_METHOD_NOT_IMPLEMENTED',            501);
  define('HTTP_BAD_GATEWAY',                       502);
  define('HTTP_SERVICE_TEMPORARILY_UNAVAILABLE',   503);
  define('HTTP_GATEWAY_TIME_OUT',                  504);
  define('HTTP_HTTP_VERSION_NOT_SUPPORTED',        505);
  define('HTTP_VARIANT_ALSO_NEGOTIATES',           506);
  define('HTTP_INSUFFICIENT_STORAGE',              507);
  define('HTTP_NOT_EXTENDED',                      510);
  
  // HTTP versions
  define('HTTP_VERSION_0_9', '0.9');
  define('HTTP_VERSION_1_0', '1.0');
  define('HTTP_VERSION_1_1', '1.1');

  /**
   * HttpConstants defines basic HTTP names and all HTTP/1.1 protocol entity names.
   *
   * @see      http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html HTTP methods
   * @purpose  HTTP constants
   */
  class HttpConstants extends Object {
    const 
      GET     = 'GET',
      POST    = 'POST',
      HEAD    = 'HEAD',
      PUT     = 'PUT',
      DELETE  = 'DELETE',
      OPTIONS = 'OPTIONS',
      TRACE   = 'TRACE',
      CONNECT = 'CONNECT';

    const 
      STATUS_CONTINUE                          = 100,
      STATUS_SWITCHING_PROTOCOLS               = 101,
      STATUS_PROCESSING                        = 102,
      STATUS_OK                                = 200,
      STATUS_CREATED                           = 201,
      STATUS_ACCEPTED                          = 202,
      STATUS_NON_AUTHORITATIVE_INFORMATION     = 203,
      STATUS_NO_CONTENT                        = 204,
      STATUS_RESET_CONTENT                     = 205,
      STATUS_PARTIAL_CONTENT                   = 206,
      STATUS_MULTI_STATUS                      = 207,
      STATUS_MULTIPLE_CHOICES                  = 300,
      STATUS_MOVED_PERMANENTLY                 = 301,
      STATUS_FOUND                             = 302,
      STATUS_SEE_OTHER                         = 303,
      STATUS_NOT_MODIFIED                      = 304,
      STATUS_USE_PROXY                         = 305,
      STATUS_TEMPORARY_REDIRECT                = 307,
      STATUS_BAD_REQUEST                       = 400,
      STATUS_AUTHORIZATION_REQUIRED            = 401,
      STATUS_PAYMENT_REQUIRED                  = 402,
      STATUS_FORBIDDEN                         = 403,
      STATUS_NOT_FOUND                         = 404,
      STATUS_METHOD_NOT_ALLOWED                = 405,
      STATUS_NOT_ACCEPTABLE                    = 406,
      STATUS_PROXY_AUTHENTICATION_REQUIRED     = 407,
      STATUS_REQUEST_TIME_OUT                  = 408,
      STATUS_CONFLICT                          = 409,
      STATUS_GONE                              = 410,
      STATUS_LENGTH_REQUIRED                   = 411,
      STATUS_PRECONDITION_FAILED               = 412,
      STATUS_REQUEST_ENTITY_TOO_LARGE          = 413,
      STATUS_REQUEST_URI_TOO_LARGE             = 414,
      STATUS_UNSUPPORTED_MEDIA_TYPE            = 415,
      STATUS_REQUESTED_RANGE_NOT_SATISFIABLE   = 416,
      STATUS_EXPECTATION_FAILED                = 417,
      STATUS_UNPROCESSABLE_ENTITY              = 422,
      STATUS_LOCKED                            = 423,
      STATUS_FAILED_DEPENDENCY                 = 424,
      STATUS_INTERNAL_SERVER_ERROR             = 500,
      STATUS_METHOD_NOT_IMPLEMENTED            = 501,
      STATUS_BAD_GATEWAY                       = 502,
      STATUS_SERVICE_TEMPORARILY_UNAVAILABLE   = 503,
      STATUS_GATEWAY_TIME_OUT                  = 504,
      STATUS_STATUS_VERSION_NOT_SUPPORTED      = 505,
      STATUS_VARIANT_ALSO_NEGOTIATES           = 506,
      STATUS_INSUFFICIENT_STORAGE              = 507,
      STATUS_NOT_EXTENDED                      = 510;

    const 
      VERSION_0_9 = '0.9',
      VERSION_1_0 = '1.0',
      VERSION_1_1 = '1.1';
  }
?>
