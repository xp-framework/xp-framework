#!/usr/bin/perl
##
# This file is part of the XP Framework's experiments
#
# $Id$

package EASC::Tests::Remote;

use Test::TestCase;
use EASC::Remote;

use base qw(Test::TestCase);

sub testLocalhostDsn {
    my $self= shift;

    $dsn= EASC::Remote::parseDsn('xp://localhost');
    $self->assertEquals('localhost', $dsn->{host});
    $self->assertEquals(6448, $dsn->{port});
    $self->assertEquals(undef, $dsn->{user});
    $self->assertEquals(undef, $dsn->{pass});
}

sub testLocalhostDsnWithPort {
    my $self= shift;

    $dsn= EASC::Remote::parseDsn('xp://localhost:8448');
    $self->assertEquals('localhost', $dsn->{host});
    $self->assertEquals(8448, $dsn->{port});
    $self->assertEquals(undef, $dsn->{user});
    $self->assertEquals(undef, $dsn->{pass});
}

sub testLocalhostDsnWithPortAndCredentials {
    my $self= shift;

    $dsn= EASC::Remote::parseDsn('xp://user:pass@localhost:8448');
    $self->assertEquals('localhost', $dsn->{host});
    $self->assertEquals(8448, $dsn->{port});
    $self->assertEquals('user', $dsn->{user});
    $self->assertEquals('pass', $dsn->{pass});
}

sub testDsnWithIpAddress {
    my $self= shift;

    $dsn= EASC::Remote::parseDsn('xp://172.17.29.5');
    $self->assertEquals('172.17.29.5', $dsn->{host});
    $self->assertEquals(6448, $dsn->{port});
    $self->assertEquals(undef, $dsn->{user});
    $self->assertEquals(undef, $dsn->{pass});
}

1;
