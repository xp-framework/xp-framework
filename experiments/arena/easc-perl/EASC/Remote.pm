#!/usr/bin/perl
##
# This file is part of the XP Framework's experiments
#
# $Id$

package EASC::Remote;

use EASC::Protocol::XpProtocolHandler;

sub parseDsn {
    my $dsn= shift;

    $dsn =~ /([^:]+):\/\/(([^:]+):([^@]+)@)?([^\/:]+)(:([0-9]+))?/
        ? return {
            versionMajor    => 1,
            versionMinor    => 0,
            host            => $5,
            port            => $7 || 6448,
            user            => $3,
            pass            => $4
        }
        : die 'Malformed DSN "', $dsn, '"';
    ;
}

sub forName {
    return EASC::Protocol::XpProtocolHandler::->new(parseDsn(shift));
}

1;
