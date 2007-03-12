#!/usr/bin/perl
##
# This file is part of the XP Framework's experiments
#
# $Id$

package Test::TestCase;

sub new {
    my $classname= shift; 
    my $self= {};
    my $name= shift; 

    bless ($self, $classname);
    $self->{'name'}= $name;
    return $self;
}

sub run {
    my $self= shift;

    eval {
        UNIVERSAL::can($self, $self->{'name'})->($self);
    };

    print '- ', ref $self, '::', $self->{'name'}, ": ", $@ || "OK\n"; 
}

sub assertEquals {
    my $self= shift;
    my $expect= shift;
    my $actual= shift;
    
    if ($expect ne $actual) {
        die 'Expected [', $expect, '] but was [', $actual, ']';
    }
    
}

package Test;

sub run {
    my $class= shift;

    eval "use $class";
    if ($@) {
        die($@);
    }

    while (($key, $val)= each(%{*{"$class\::"}})) {
	    if (defined $val && defined *$val{CODE} && ($key =~ /^test/)) {
            $class->new($key)->run();
        }
    }
}

1;
