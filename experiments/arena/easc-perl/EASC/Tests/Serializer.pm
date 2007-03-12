#!/usr/bin/perl
##
# This file is part of the XP Framework's experiments
#
# $Id$

package EASC::Tests::Serializer;

use Test::TestCase;
use EASC::Protocol::Serializer;
use EASC::Protocol::Datatypes;

use base qw(Test::TestCase);

sub testString {
    my $self= shift;
    $self->assertEquals('s:5:"Hello";', EASC::Protocol::Serializer::representationOf('Hello'));
}

sub testUmlauts {
    my $self= shift;
    $self->assertEquals('s:5:"ÄÖÜßé";', EASC::Protocol::Serializer::representationOf('ÄÖÜßé'));
}

sub testTrue {
    my $self= shift;
    $self->assertEquals('b:1;', EASC::Protocol::Serializer::representationOf(Bool::->new(1)));
    $self->assertEquals('b:1;', EASC::Protocol::Serializer::representationOf(Bool::->new(2)));
}

sub testFalse {
    my $self= shift;
    $self->assertEquals('b:0;', EASC::Protocol::Serializer::representationOf(Bool::->new(0)));
}

sub testIntegers {
    my $self= shift;

    foreach my $int (0, -1, 1, 65536) {
        $self->assertEquals('i:'.$int.';', EASC::Protocol::Serializer::representationOf(Integer::->new($int)));
    }
}

sub testLongs {
    my $self= shift;

    foreach my $long (-1, 0, 1, 65536) {
        $self->assertEquals('l:'.$long.';', EASC::Protocol::Serializer::representationOf(Long::->new($long)));
    }
}

1;
