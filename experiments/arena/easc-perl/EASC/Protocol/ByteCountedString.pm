#!/usr/bin/perl
##
# This file is part of the XP Framework's experiments
#
# $Id$

package EASC::Protocol::ByteCountedString; 

use warnings;
use strict;
use Encode qw(from_to);
use POSIX;

require Exporter;

our $VERSION = 1.0;
our @ISA = qw(Exporter);
our @EXPORT= qw(&sendTo &readFrom);
our %EXPORT_TAGS = ( );
our @EXPORT_OK = qw();

use vars qw();

# Size of a chunk to send
sub BCS_DEFAULT_CHUNK_SIZE { 0xFFFF };

# Constructor
#
# @param string to store
# @return reference to the object
sub new {
    my ($classname, $string) = @_;
    my $self = {};
    bless ($self, $classname);

    $self->{string}= $string || '';
    from_to($self->{string}, "iso-8859-1", "utf8");
    
    return $self;
}

# Length
# @return int length of packet(s) in bytes
#
# Counts how many bytes the string has and how many headers are needed

sub slength {
    my ($self, $chunksize)   = @_;
    $chunksize ||= BCS_DEFAULT_CHUNK_SIZE;
    my $length = length ($self->{string});
    return $length + 3 * POSIX::ceil($length / $chunksize);
}

# writeTo
#
# @param socket
# @param chunksize optional
#
# Writes the (UTF8)-String to the given socket

sub writeTo {
    my ($self,$sock,$chunksize) = @_;
    $chunksize ||= BCS_DEFAULT_CHUNK_SIZE;
    my $length = length $self->{string};
    my $offset = 0;

    do {
        my $chunk = $length > $chunksize ? $chunksize : $length;

        # Pack the header (2 bytes length + 1 to tell if there will be more chunks)
        my $packet = pack('nc', $chunk, $length - $chunk > 0);

        # Send it to the socket
        $sock->send($packet); # header
        $sock->send(substr($self->{string}, $offset, $chunk)); # string

        $offset += $chunk;
        $length -= $chunk;  
    } while ($length > 0);

    return 1;
}

# ReadFully
#
# @param sockt to read from
# @param int number of bytes to read

sub readFully {
    my ($sock, $length) = @_;
    my $return = '';
    while (length($return) < $length) {
        my $buf;
        $sock->recv($buf, $length - length $return);
        return unless defined $buf; # We read nothing
        $return .= $buf;
    }
    return $return;
}

# readFrom 
#
# @param socket to read from
# @return string it read
sub readFrom {
    my ($sock) = @_;
    my $s = '';
    my $r_length; 
    my $r_next = 1;

    while ($r_next) {
        # Read the header (3 bytes)
        ($r_length, $r_next) = unpack('nc', EASC::Protocol::ByteCountedString::readFully($sock, 3));
        return unless $r_length;

        # And read how much the header says...
        $s.= EASC::Protocol::ByteCountedString::readFully($sock, $r_length);
    }

    from_to($s, "utf8", "iso-8859-1");
    return $s;
}

sub DESTROY { }

1;
