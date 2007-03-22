#!/usr/bin/perl
##
# This file is part of the XP Framework's experiments
#
# $Id$

package EASC::Tests::ByteCountedString;

use Test::TestCase;
use EASC::Protocol::ByteCountedString;
use EASC::Tests::MockSocket;

use base qw(Test::TestCase);

sub testUsAsciiString {
    my $self= shift;

    $bcs= EASC::Protocol::ByteCountedString::->new('Hello');
    $self->assertEquals('Hello', $bcs->{string});
    $self->assertEquals(8, $bcs->slength);
}

sub testUmlautString {
    my $self= shift;

    $bcs= EASC::Protocol::ByteCountedString::->new('HallÃ¶le');
    $self->assertEquals('HallÃƒÂ¶le', $bcs->{string});
    $self->assertEquals(13, $bcs->slength);
}

sub testutf8String {
    my $self= shift;

    $bcs= EASC::Protocol::ByteCountedString::->new('Hallöle');
    $self->assertEquals('HallÃ¶le', $bcs->{string});
    $self->assertEquals(11, $bcs->slength);
}

sub testSending {
    my $self= shift;

    $bcs= EASC::Protocol::ByteCountedString::->new('Hallöle');
    $sock= EASC::Tests::MockSocket::->new();

    $bcs->writeTo($sock);
    $self->assertEquals("\x00\x08\x00HallÃ¶le", $sock->{bytes});
}

sub testReading {
    my $self= shift;

    $sock= EASC::Tests::MockSocket::->new("\x00\x08\x00HallÃ¶le");

    $bcs= EASC::Protocol::ByteCountedString::readFrom($sock);
    $self->assertEquals('Hallöle', $bcs);
}

sub testReadingTwoChunks {
    my $self= shift;

    $sock= EASC::Tests::MockSocket::->new(
      "\x00\x08\x01HallÃ¶le".
      "\x00\x05\x00 Moto"
    );

    $bcs= EASC::Protocol::ByteCountedString::readFrom($sock);
    $self->assertEquals('Hallöle Moto', $bcs);
}

1;
