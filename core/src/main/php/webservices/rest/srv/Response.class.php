<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * REST response
   *
   */
  class Response extends Object {
    public $status;
    public $headers= array();
    public $payload;

    public static function ok() {
      $self= new self();
      $self->status= 200;
      return $self;
    }

    public static function created($location) {
      $self= new self();
      $self->status= 201;
      $self->headers['Location']= $location;
      return $self;
    }

    public static function noContent() {
      $self= new self();
      $self->status= 204;
      return $self;
    }

    public static function see($location) {
      $self= new self();
      $self->status= 302;
      $self->headers['Location']= $location;
      return $self;
    }

    public static function notModified() {
      $self= new self();
      $self->status= 304;
      return $self;
    }

    public static function notFound() {
      $self= new self();
      $self->status= 404;
      return $self;
    }

    public static function notAcceptable() {
      $self= new self();
      $self->status= 406;
      return $self;
    }

    public static function error() {
      $self= new self();
      $self->status= 500;
      return $self;
    }

    public static function status($status) {
      $self= new self();
      $self->status= $status;
      return $self;
    }

    public function withHeader($name, $value) {
      $this->headers[$name]= $value;
      return $this;
    }

    public function withPayload($data) {
      $this->payload= $data;
      return $this;
    }
  }
?>