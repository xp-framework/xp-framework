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

sub testRepresentationOfString {
    my $self= shift;
    $self->assertEquals('s:5:"Hello";', EASC::Protocol::Serializer::representationOf('Hello'));
}

sub testValueOfString {
    my $self= shift;
    $self->assertEquals('Hello', EASC::Protocol::Serializer::valueOf('s:5:"Hello";'));
}

sub testRepresentationOfUmlauts {
    my $self= shift;
    $self->assertEquals('s:5:"ÄÖÜßé";', EASC::Protocol::Serializer::representationOf('ÄÖÜßé'));
}

sub testValueOfUmlauts {
    my $self= shift;
    $self->assertEquals('ÄÖÜßé', EASC::Protocol::Serializer::valueOf('s:5:"ÄÖÜßé";'));
}

sub testRepresentationOfTrue {
    my $self= shift;
    $self->assertEquals('b:1;', EASC::Protocol::Serializer::representationOf(Bool::->new(1)));
    $self->assertEquals('b:1;', EASC::Protocol::Serializer::representationOf(Bool::->new(2)));
}

sub testValueOfTrue {
    my $self= shift;
    $self->assertEquals(1, EASC::Protocol::Serializer::valueOf('b:1;')->value());
}

sub testRepresentationOfFalse {
    my $self= shift;
    $self->assertEquals('b:0;', EASC::Protocol::Serializer::representationOf(Bool::->new(0)));
}

sub testValueOfFalse {
    my $self= shift;
    $self->assertEquals(0, EASC::Protocol::Serializer::valueOf('b:0;')->value());
}

sub testRepresentationOfIntegers {
    my $self= shift;

    foreach my $int (0, -1, 1, 65536) {
        $self->assertEquals('i:'.$int.';', EASC::Protocol::Serializer::representationOf(Integer::->new($int)));
    }
}

sub testValueOfIntegers {
    my $self= shift;

    foreach my $int (0, -1, 1, 65536) {
        $self->assertEquals($int, EASC::Protocol::Serializer::valueOf('i:'.$int.';')->value());
    }
}

sub testRepresentationOfLongs {
    my $self= shift;

    foreach my $long (-1, 0, 1, 65536) {
        $self->assertEquals('l:'.$long.';', EASC::Protocol::Serializer::representationOf(Long::->new($long)));
    }
}

sub testValueOfLongs {
    my $self= shift;

    foreach my $long (0, -1, 1, 65536) {
        $self->assertEquals($long, EASC::Protocol::Serializer::valueOf('l:'.$long.';')->value());
    }
}

1;
