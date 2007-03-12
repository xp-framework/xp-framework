#!/usr/bin/perl
##
# This file is part of the XP Framework's experiments
#
# $Id$

package EASC::Protocol::Datatypes;

use warnings;
use strict;

package Object;

sub new {
    my $classname = shift; 
    my $self = {};
    my $value = shift; 
    %$self = %$value;
    bless($self, $classname);

    return $self;
}

package Exception;

sub new {
    my ($classname, $value)  = @_;
    my $self = {};
    %$self = %$value;
    bless($self, $classname);

    return $self;
}

sub throw {
    my ($self) = @_;
    my $trace = "Trace:\n ";

    foreach my $key (sort { $self->{trace}->{$b} <=> $self->{trace}->{$a} } keys %{$self->{trace}} )  {
        foreach my $bugfix ('class','method','file','line') {
            $self->{trace}->{$key}->{$bugfix} ||= '??';
        }
        $trace .= $self->{trace}->{$key}->{class} . ': ' . $self->{trace}->{$key}->{method} . '() at '. $self->{trace}->{$key}->{file} . ':' . $self->{trace}->{$key}->{line} . "\n ";
    }
    $self->{classname} ||= '??';
    $self->{message} ||= '??'; 
    die $self->{classname}."\n".$self->{message}."\n\n".$trace."\n";
}

package Bool;

# Overload the string-operator:
# this means:
# # print Integer::->new(12);
# will print out 12 instead of Integer::(0x123);
# because it turns into print Integer::->new(12)->value();
# 
use overload '""' => \&value;
sub new {
    my ($classname, $value) = @_;
    my $self = {value => $value ? 1 : 0 };
    bless($self, $classname);
    return $self;
}
sub value {  return shift->{value} ? 1 : 0; }


package Integer;

use overload '""' => \&value;
sub new {
    my ($classname, $value) = @_;
    my $self = {value => int($value)};
    bless($self, $classname);
    return $self;
}
sub value {  return shift->{value}; }


package Long;

use overload '""' => \&value;
sub new {
    my ($classname, $value) = @_;
    my $self = {value => $value};
    bless($self, $classname);
    return $self;
}
sub value {  
    my $value = shift->{value}; 
    my $number = $value;

    # Workaround, kinda dirty rounding... 
    if ($value =~ m/(.*)\.(.*)e([+|-])(.*)/) {
        my $count = $4 - length($2); 
        my $zero = ('0' x $count);
        my $part2 = $2 -1;
        $number = "$1$part2$zero";
    }
    return $number;
}

package Double;
use overload '""' => \&value;
sub new {
    my ($classname,$value) = @_;
    my $self = {value => $value};
    bless($self, $classname);
    return $self;
}
sub value {  return shift->{value}; }


package Float;
use overload '""' => \&value;
sub new {
    my ($classname ,$value) = @_;
    my $self = {value => $value};
    bless($self, $classname);
    return $self;
}
sub value {  return shift->{value}; }

package ObjInterface;
# Dummy-Class for I:-Answers 
# 
# We create a ObjInterface if the EASC-Server returns us a I:objectid:{classname} 
#
# we can call invoke on this Object ourself or just let the Autoload-mapper call
# it for us


#
# Constructor
#
# @param string Objectname
# @param int Objectid
# @param handler (XpProtoHandler)

sub new {
    my ($classname,$objname,$oid, $handler) = @_;

    my $self = { objname => $objname, oid => $oid, handler => $handler };

    bless($self, $classname);
    return $self;
}

# invoke
#
# @param string method
# @param args[]
# @return whatever the Server returns
#
# This will call the invoke-method of our handler:
#
# $foo = ObjInterface::->new('myClass', '123', $handler);
# $result = $foo->invoke('myMethod', 'asdf');
# will be the same as:
# $handler->invoke('123', 'myMethod', 'asdf');

sub invoke {
    my ($self,$method, @args) = @_;
    return @args ? $self->{handler}->invoke($self->{oid}, $method, @args) : $self->{handler}->invoke($self->{oid}, $method);
}

# AUTOLOAD
#
# you DONT want to call this manually!!!
#
# this method will be called if the methodname is unknown. 
# It calls invoke('methodname', @args) in this case:
#
# $foo = ObjInterface::->new('myClass', '123', $handler);
# $result = $foo->myMethod('asdf');
# will be the same as:
# $handler->invoke('123', 'myMethod', 'asdf');

sub AUTOLOAD {
    my ($self, @args) = @_;
    my $subname = our $AUTOLOAD; 
    $subname =~ s/.*:://;
    return @args ? $self->invoke($subname, @args) : $self->invoke($subname);
}
sub DESTROY {}
1;
