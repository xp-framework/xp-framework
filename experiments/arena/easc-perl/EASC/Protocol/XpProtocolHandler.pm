#!/usr/bin/perl
##
# This file is part of the XP Framework's experiments
#
# $Id$
 
package EASC::Protocol::XpProtocolHandler;

use strict;
use warnings;
use Carp qw(confess);

use EASC::Protocol::Serializer;
use EASC::Protocol::ByteCountedString;

use IO::Socket;
use Socket qw(TCP_NODELAY);

require Exporter;

our $VERSION = 1.0;

our @ISA = qw(Exporter);
our @EXPORT= qw();
our %EXPORT_TAGS = ( );
our @EXPORT_OK = qw();

use vars qw();

# Its maaaaaagic
  sub DEFAULT_PROTOCOL_MAGIC_NUMBER  { 0x3C872747 };
  
# Request messages
  sub REMOTE_MSG_INIT      { 0 };
  sub REMOTE_MSG_LOOKUP    { 1 };
  sub REMOTE_MSG_CALL      { 2 };
  sub REMOTE_MSG_FINALIZE  { 3 };
  sub REMOTE_MSG_TRAN_OP   { 4 };  
# Response messages
  sub REMOTE_MSG_VALUE     { 5 };
  sub REMOTE_MSG_EXCEPTION { 6 };
  sub REMOTE_MSG_ERROR     { 7 };
# Transaction message types
  sub REMOTE_TRAN_BEGIN    { 1 };
  sub REMOTE_TRAN_STATE    { 2 };
  sub REMOTE_TRAN_COMMIT   { 3 };
  sub REMOTE_TRAN_ROLLBACK { 4 };



# Initialize this protocol handler
# @access    public
# @param     \%URL-Data

sub new {
    my ($classname, @args) = @_;
    my $self = {};
    
    $self->{versionMajor} = 0;
    $self->{versionMinor} = 0;
    $self->{_socket} = undef;

    bless($self, $classname);
    return $self->_init(@args);
}


# Initialize this protocol handler
# @access    private
sub _init {
    my ($self, $proxy) = @_;
    $self->{versionMajor} = $proxy->{versionMajor};
    $self->{versionMinor} = $proxy->{versionMinor};
    $self->{host} = $proxy->{host};
    $self->{port} = $proxy->{port};
    
    # Connect to Socket
    my $sock = IO::Socket::INET->new(
        PeerAddr => $self->{host},
        PeerPort => $self->{port},
		Proto    => "tcp",
		Type     => SOCK_STREAM 
    );

    $sock or die "Couldn't connect to $self->{host}:$self->{port}. Error: $@\n";
    $sock->sockopt(Socket::TCP_NODELAY, 1);
    $self->{_sock} = $sock;

    my $response;
    if (defined $proxy->{user}) {     # Authentification 
        my $login= [ 
            EASC::Protocol::ByteCountedString::->new($proxy->{user}), 
            EASC::Protocol::ByteCountedString::->new($proxy->{pass})   
        ];
        $response = $self->sendPacket(REMOTE_MSG_INIT, "\1", $login);
    } else {                          # no Authentification
        $response = $self->sendPacket(REMOTE_MSG_INIT, "\0");   
    }
    return $response ? $self : undef;
}


#
# Returns a string representation of this object
#
# @access public
# @return string
sub toString {
    my ($self) = @_;
    return ref($self) . '(->' . $self->{host} . ':' . $self->{port} . ')';
}


#
# Look up an object by its name
#
# @access  public
# @param   string name
# @return  \Mixed
# 
sub lookup {
    my ($self, $param) = @_;
    my $params = [ EASC::Protocol::ByteCountedString::->new($param) ];
    return $self->sendPacket(REMOTE_MSG_LOOKUP, '',  $params);
}


#
# Begin a transaction
#
# @access  public
# @param   Usertransaction tran
# @return  bool
#
sub begin {
    my ($self) = @_;
    return $self->sendPacket(REMOTE_MSG_TRAN_OP, pack('N', REMOTE_TRAN_BEGIN));
}

#
# Rollback a transaction
#
# @access public
# @param  UserTransaction tran
# @return bool
# 
sub rollback {
    my ($self) = @_;
    return $self->sendPacket(REMOTE_MSG_TRAN_OP, pack('N', REMOTE_TRAN_ROLLBACK));
}

#
# Commit a transaction
#
# @access public
# @param  UserTransaction tran
# @return bool
#
sub commit {
    my ($self) = @_;
    return $self->sendPacket(REMOTE_MSG_TRAN_OP, pack('N', REMOTE_TRAN_COMMIT));
}


#
# Invoke a method on a given object id with given method name and arguments
#
# @access  public
# @param   int oid
# @param   string method
# @param   \@mixed args
#
sub invoke {
    my ($self, $oid, $method, @argz) = @_;
    my $a = 1 if @argz;  # kind of dirty, dunno how to make it better right now
    my $args = \@argz if $a ;
    my $arguments = [];
    push @$arguments, EASC::Protocol::ByteCountedString::->new($method);
    if ($a && ref($args) eq 'ARRAY') {
        push @$arguments, EASC::Protocol::ByteCountedString::->new(EASC::Protocol::Serializer::representationOf($args));
    } elsif ($a) {
        push @$arguments, EASC::Protocol::ByteCountedString::->new(EASC::Protocol::Serializer::representationOf([$args]));
    } else {
        push @$arguments, EASC::Protocol::ByteCountedString::->new(EASC::Protocol::Serializer::representationOf([]));
    }

    return $self->sendPacket(REMOTE_MSG_CALL, pack('NN', 0, $oid), $arguments);
}


#
# Sends a packet, reads and evaluates the response
#
# @access    protected
# @param     int type
# @param     string data default ''
# @param     bytes \@ByteCountedString
# @return    \@mixed
# @throws    Errors and Exceptions as much as it can find
sub sendPacket {
    my ($self, $type, $data,$bytes) = @_;
    
    # Calculate packet length
    my $length = length $data;
    foreach (@$bytes) {
        $length += $_->slength();
    }
    
    # Write packet
    my $packet= pack(
        'Nc4Na*',
        DEFAULT_PROTOCOL_MAGIC_NUMBER,
        $self->{versionMajor},
        $self->{versionMinor},
        $type,
        0,
        $length, 
        $data
    );
    
    # Send it all to socket
    $self->{_sock}->send($packet);
    foreach (@$bytes) {
        $_->writeTo($self->{_sock});
    }
  
    my ($r_magic, $r_major, $r_minor, $r_type, $r_tran, $r_length) = unpack('Nc4N', $self->readBytes(12));
    #print STDERR "NOTICE|Read answer-header: I'll check magic number and msg_type and read $r_length more bytes\n";
    if (DEFAULT_PROTOCOL_MAGIC_NUMBER != $r_magic) {
        close $self->{_sock};
        confess "Magic number mismatch!";
    }

    if ($r_type == REMOTE_MSG_VALUE) {
        my $ret = EASC::Protocol::Serializer::valueOf(EASC::Protocol::ByteCountedString::readFrom($self->{_sock}),$self);
        #print STDERR "NOTICE|Easc done\n";
        return $ret;
    } elsif ($r_type == REMOTE_MSG_EXCEPTION) {
        my $reference = EASC::Protocol::Serializer::valueOf(EASC::Protocol::ByteCountedString::readFrom($self->{_sock}));
        close $self->{_sock};
        $reference->throw();
    } elsif ($r_type == REMOTE_MSG_ERROR) {
        my $message = EASC::Protocol::ByteCountedString::readFrom($self->_sock);
        close $self->{_sock};
        confess $message;
    } else {
        $self->readBytes($r_length);
        close $self->{_sock};
        confess "Unknown message type";
    }
}

sub readBytes {
    my ($self, $num) = @_;
    my $return = '';

    while (length $return < $num) {
        my $buf;
        $self->{_sock}->recv($buf, $num - length $return);
        return if (length $buf == 0);
        $return .= $buf;
    }
    return $return;
}

sub shutdown {
    my ($self) = @_;
    $self->{_socket}->shutdown() if $self->{_socket};
}

sub DESTROY { }

1;

