#!/usr/bin/perl
##
# This file is part of the XP Framework's experiments
#
# $Id$

package EASC::Tests::ByteCountedString;

use Test::TestCase;
use EASC::Protocol::ByteCountedString;

use base qw(Test::TestCase);

sub testUsAsciiString {
    my $self= shift;

    $bcs= EASC::Protocol::ByteCountedString::->new('Hello');
    $self->assertEquals('Hello', $bcs->{string});
    $self->assertEquals(8, $bcs->slength);
}

sub testUmlautString {
    my $self= shift;

    $bcs= EASC::Protocol::ByteCountedString::->new('Hallöle');
    $self->assertEquals('HallÃ¶le', $bcs->{string});
    $self->assertEquals(11, $bcs->slength);
}

1;
