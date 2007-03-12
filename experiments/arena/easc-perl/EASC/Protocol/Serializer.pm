#!/usr/bin/perl
##
# This file is part of the XP Framework's experiments
#
# $Id$

package EASC::Protocol::Serializer;

use strict;
use warnings;
use EASC::Protocol::Datatypes;

require Exporter;

our $VERSION = 1.0;
our @ISA = qw(Exporter);
our @EXPORT= qw(&representationOf &valueOf);
our %EXPORT_TAGS = ( ); 
our @EXPORT_OK = qw();

use vars qw();

# representationOf
#
# @param data structure
# @return  (iso-)string representation of data
#
sub representationOf {
    my ($value) = @_;
    my $return;
    my $l;

    # see if we have a Scalar, Array, Hash or Object
    my $ref = ref ( $value );  

    if ( $ref eq 'ARRAY' ) {
        # We serialize an Array to: A:number_elements:{first_element;second_element;...;}
        $return .= 'A:' . scalar @$value . ':{';
        for (my $i = 0; $i < @$value; $i++) {
            $return .= EASC::Protocol::Serializer::representationOf($value->[$i]);
        }
        $return .= '}';
    } elsif ( $ref eq 'HASH' ) {
        # We serialize a Hash to: 
        # a:number_elements:{first_key;first_value;second_key;second_value;...;}
        my @keys = keys %$value;
        $return = 'a:' . scalar @keys . ':{';
        for (my $i = 0; $i < @keys; $i++) {
            $return .= EASC::Protocol::Serializer::representationOf($keys[$i]) . EASC::Protocol::Serializer::representationOf($value->{$keys[$i]});
        }
        $return .= '}';
    } elsif ( $ref ) {              # We have an Object here
      if ($ref eq 'Integer') {
          $return = 'i:' . $value->value() . ';';
      } elsif ($ref eq 'Long') {
          $return = 'l:' . $value->value() . ';';
      } elsif ($ref eq 'Bool') {
          $return = 'b:' . $value . ';';
      } elsif ($ref eq 'Float') {
          $return = 'f:' . $value . ';';
      } elsif ($ref eq 'Double') {
          $return = 'd:' . $value . ';';
      } elsif ($ref eq 'Exception') {
          # we wont send exceptions ;)
          die "we wont send exceptions";
      } elsif ($ref eq 'Object') {
          my @keys = keys %$value;

          my $classname= $value->{pop(@keys)};
          $value ||= '';
          my $length = $classname ? length $classname : 0;
          $return = 'O:'.$length.':"'.$classname.'":'.scalar @keys . ':{';
          for (my $i = 0; $i < @keys; $i++) {
            $return .= EASC::Protocol::Serializer::representationOf($keys[$i]) . EASC::Protocol::Serializer::representationOf($value->{$keys[$i]});
          }
          $return .= '}';
      } else {
          die "Unknown kind of object: $ref ($value).";
      }
    } else {    # Scalar... This has to be a String. Anything else shoult be a Object ;)
        $value ||= '';
        my $length = $value ? length $value : 0;
        return "s:$length:\"$value\";";
    }

    return $return;
}

# valueOf
#
# @param string serialized String (in iso *not utf8)
# @return data structure represented by the string in scalar-context
# @return array of datastructure and length used in the $serialized-string
#  second is important for arrays and hashes where we need to know how longe one
#  element was to resolve the next element

sub valueOf {
    my ($serialized, $handler) = @_;

    # First char says wat kind of data we have (e.g. i:123;)
    my $kind= substr($serialized, 0, 1);

    if ($kind eq 'N') {           # NULL is undef in Perl
        return wantarray ? (undef, 2) : undef;
    } elsif ($kind eq 'b') {
        my $value = Bool::->new( substr ( $serialized , 2, index ($serialized, ';',2) -2 ) );
        return wantarray ? ($value, length $value) : $value;
    } elsif ($kind eq 'i') {
        my $value =  substr ( $serialized , 2, index ($serialized, ';',2) -2 );
        return wantarray ?  (Integer::->new( $value ), length ($value) +3 ) : Integer::->new( $value );
    } elsif ($kind eq 'l') {
        my $value =  substr ( $serialized , 2, index ($serialized, ';',2) -2 );
        return wantarray ?  (Long::->new( $value ), length ($value) +3 ) : Long::->new( $value );
    } elsif ($kind eq 'd') {
        my $value =  Double::->new( substr ( $serialized , 2, index ($serialized, ';',2) -2 ));
        return wantarray ?  ($value , length ($value) +3) : $value;
    } elsif ($kind eq 'f') {
        my $value =  Float::->new( substr ( $serialized , 2, index ($serialized, ';',2) -2 ));
        return wantarray ?  ($value , length ($value)+3) : $value;
    } elsif ($kind eq 's') {
        my $strlen = substr ($serialized, 2, index ($serialized, ':',2) -2 );
        my $length = 2 + length($strlen) + 2 + $strlen + 2;
        my $value =  substr($serialized, 2+ length($strlen) + 2, $strlen);
        return wantarray ? ($value, $length) : $value;
    } elsif ($kind eq 'a') {      # Array aka %hash
        my %result;
        my $len = substr ($serialized, 2, index ($serialized, ':',2) -2 ); # Nr of elements
        my $offset= length($len)+ 2+ 2;
        for (my $i = 0; $i < $len; $i++) {
            # We can only use Strings here...
            # $key can also be an Integer-Object or sth... cause of operator-overloading
            my ($key, $len) = EASC::Protocol::Serializer::valueOf(substr($serialized, $offset));
            $offset+= $len;
            (my $res, $len) = EASC::Protocol::Serializer::valueOf(substr($serialized, $offset));
            $result{$key} = $res;   
            $offset+= $len;
        }
        return wantarray ? (\%result, $offset +1) : \%result;
    } elsif ($kind eq 'A') {    # ARRAYLIST aka @array
        my @result;
        my $len = substr ($serialized, 2, index ($serialized, ':',2) -2 );
        my $offset= length($len)+ 2+ 2;
        for (my $i = 0; $i < $len; $i++) {
           my ($value, $len) = EASC::Protocol::Serializer::valueOf(substr($serialized, $offset));
           push (@result, $value);
           $offset+= $len;
        }
        return wantarray ? (\@result, $offset +1) : \@result;
    } elsif ($kind eq 'E' || $kind eq 'e') {
        # Exception 
        my $len= substr($serialized, 2, index($serialized, ':', 2)- 2);
        my $instance= {};
        $instance->{classname} = substr($serialized, 2+ length($len)+ 2, $len);
        my $offset= 2 + 2 + length($len)+ $len + 2;
        my $size= substr($serialized, $offset, index($serialized, ':', $offset)- $offset);
        $offset+= length($size)+ 2;
        for (my $i= 0; $i < $size; $i++) {
            my ($member, $len) = EASC::Protocol::Serializer::valueOf(substr($serialized, $offset));
            $offset+= $len;
            ($instance->{$member}, $len)= EASC::Protocol::Serializer::valueOf(substr($serialized, $offset));
            $offset+= $len;
        }
        my $length= $offset+ 1;
        my $exception = Exception::->new($instance);
        return wantarray ? ($exception, $length) : $exception;
    } elsif ($kind eq 'I') {
        # Interface. The important thing here is the oid we use to call a method on the looked-up object.
        # We create a fake-class with autoload here
        my $oid = substr ($serialized, 2, index ($serialized, ':',2) -2 );
        my $oidlength = length $oid;
        my $classname = EASC::Protocol::Serializer::valueOf(substr($serialized, $oidlength + 4));
        my $object = ObjInterface::->new($classname, $oid, $handler );
        return wantarray ? ($object, $classname+ $oidlength+ 1) : $object; 
    } elsif ($kind eq 't') {
        # Stack-trace-element ... needed for Exceptions
        my $size= substr($serialized, 2, index($serialized, ':', 2)- 2);
        my $offset= length($size)+ 2+ 2;
        my $details= {};
        for (my $i= 0; $i < $size; $i++) {
            my ($detail, $len) = EASC::Protocol::Serializer::valueOf(substr($serialized, $offset));
            $offset+= $len;
            ($details->{$detail}, $len) = EASC::Protocol::Serializer::valueOf(substr($serialized, $offset));
            $offset+= $len;
        }
        return wantarray ? ($details, $offset+ 1) : $details; 
    } elsif ($kind eq 'O') {
        my $len= substr($serialized, 2, index($serialized, ':', 2)- 2);
        my $instance= {};
        $instance->{classname} = substr($serialized, 2+ length($len)+ 2, $len);
        my $offset= 2 + 2 + length($len)+ $len + 2;
        my $size= substr($serialized, $offset, index($serialized, ':', $offset)- $offset);
        $offset+= length($size)+ 2;
        for (my $i= 1; $i <= $size; $i++) {
            my ($member, $len) = EASC::Protocol::Serializer::valueOf(substr($serialized, $offset));
            $offset+= $len;
            ($instance->{$member}, $len)= EASC::Protocol::Serializer::valueOf(substr($serialized, $offset));
            $offset+= $len;
        }
        my $length= $offset+ 1;
        my $remoteObject = Object::->new($instance);
        return wantarray ? ($remoteObject, $length) : $remoteObject; 
    } else {
        die 'Cannot deserialize "', $serialized, '"';
    }
}

sub DESTROY {}

1;
