#!/usr/bin/perl
##
# This file is part of the XP Framework's experiments
#
# $Id$

package EASC::Tests::MockSocket;

sub new {
    my $classname= shift; 
    my $bytes= shift; 
    my $self= {
        bytes => $bytes || ''
    };

    bless ($self, $classname);
    return $self;
}

sub send {
    my $self= shift;
    my $bytes= shift;
    
    $self->{bytes}.= $bytes;
    return length($bytes);
}

sub recv {
    my $self= shift;
    
    die('Bytes argument must be > 0') unless ($_[1] > 0);
    
    # No more data
    return 0 if "" eq $self->{bytes};
    
    # Data is available
    $_[0]= substr($self->{bytes}, 0, $_[1]);
    $self->{bytes}= substr($self->{bytes}, $_[1], length($self->{bytes}));
    return $length;
}

1;
