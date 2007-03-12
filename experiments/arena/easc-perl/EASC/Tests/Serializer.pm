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

sub testValueOfHash {
    my $self= shift;

    $h= EASC::Protocol::Serializer::valueOf('a:2:{s:3:"key";s:5:"value";s:6:"number";i:6100;}');
    $self->assertEquals('value', $h->{key});
    $self->assertEquals(6100, $h->{number}->value());
}

sub testRepresentationOfArrayList {
    my $self= shift;

    @list= [ 'key', 'value' ];
    $self->assertEquals('A:2:{s:3:"key";s:5:"value";}', EASC::Protocol::Serializer::representationOf(@list));
}


sub testValueOfArrayList {
    my $self= shift;

    $l= EASC::Protocol::Serializer::valueOf('A:2:{s:3:"key";s:5:"value";}');
    $self->assertEquals('key', $l->[0]);
    $self->assertEquals('value', $l->[1]);
}


sub testValueOfObject {
    my $self= shift;

    $p= EASC::Protocol::Serializer::valueOf(
        'O:39:"net.xp_framework.unittest.remote.Person":2:{s:2:"id";i:1549;s:4:"name";s:11:"Timm Friebe";}'
    );
    $self->assertEquals('net.xp_framework.unittest.remote.Person', $p->{classname});
    $self->assertEquals(1549, $p->{id}->value());
    $self->assertEquals('Timm Friebe', $p->{name});
}

sub testValueOfException {
    my $self= shift;
    
    $e= EASC::Protocol::Serializer::valueOf(
        'E:46:"java.lang.reflect.UndeclaredThrowableException":3:{'.
        's:7:"message";s:12:"*** BLAM ***";'.
        's:5:"trace";a:1:{i:0;t:4:{s:4:"file";s:9:"Test.java";s:5:"class";s:4:"Test";s:6:"method";s:4:"main";s:4:"line";i:10;}}'.
        's:5:"cause";N;'.
        '}'
    );

    $self->assertEquals('java.lang.reflect.UndeclaredThrowableException', $e->{classname});
    $self->assertEquals('*** BLAM ***', $e->{message});
    $self->assertEquals('Test.java', $e->{trace}{'0'}{'file'});
    $self->assertEquals(10, $e->{trace}{'0'}{'line'}->value());
    $self->assertEquals('Test', $e->{trace}{'0'}{'class'});
    $self->assertEquals('main', $e->{trace}{'0'}{'method'});
    $self->assertEquals(undef, $e->{cause});
}

sub testValueOfEmptyData {
    my $self= shift;
    
    eval { EASC::Protocol::Serializer::valueOf(''); };
    $self->assertEquals(1, $@ =~ /^Cannot deserialize ""/);
}


1;
