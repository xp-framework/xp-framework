using System;
using System.Collections.Generic;
using System.Text;
using System.Net.Sockets;
using System.IO;

namespace Net.XpFramework.EASC
{
    /// <summary>
    /// Implements the XP EASC protocol. Used by the Remote class.
    /// </summary>
    class XPProtocol : IDisposable
    {
        // Request messages
        public const byte REMOTE_MSG_INIT = 0;
        public const byte REMOTE_MSG_LOOKUP = 1;
        public const byte REMOTE_MSG_CALL = 2;
        public const byte REMOTE_MSG_FINALIZE = 3;
        public const byte REMOTE_MSG_TRAN_OP = 4;

        // Response messages
        public const byte REMOTE_MSG_VALUE = 5;
        public const byte REMOTE_MSG_EXCEPTION = 6;
        public const byte REMOTE_MSG_ERROR = 7;

        const UInt32 EASC_MAGIC_NUMBER = 0x3c872747;

        protected TcpClient sock = null;
        protected BinaryWriter writer= null;
        protected BinaryReader reader= null;

        public Dictionary<string, object> serializerContext = new Dictionary<string, object>();
        public Serializer serializer = null;

        /// <summary>
        /// Initialize client/server communication
        /// </summary>
        /// <param name="proxy">The Uri object specifying the remote endpoint</param>
        public void initialize(Uri proxy)
        {
            this.sock = new TcpClient();

            try
            {
                this.sock.Connect(proxy.Host, proxy.Port);
                this.sock.NoDelay = true;
            }
            catch (SocketException e)
            {
                throw new RemoteException("Failed connecting to " + proxy, e);
            }

            // Set up serializer
            this.serializer = new Serializer();
            this.serializer.mappings['I'] = new RemoteInterfaceMapping();
            this.serializerContext["handler"] = this;

            // Start up socket communication
            this.writer = new BinaryWriter(this.sock.GetStream());
            this.reader = new BinaryReader(this.sock.GetStream());
            this.SendPacket(REMOTE_MSG_INIT, "\x01"); // FIXME: Doesn't support user/password yet
        }

        /// <summary>
        /// Encode a string into BCS format. FIXME: Probably should be in its own class
        /// </summary>
        /// <param name="data"></param>
        /// <returns></returns>
        public string ByteCountedStringOf(string data)
        {
            int length = data.Length;
            StringBuilder result = new StringBuilder();

            result.Append((char)unchecked((byte)((length >> 8) & 0xff)));
            result.Append((char)unchecked((byte)((length) & 0xff)));
            result.Append("\x00");      // FIXME: Only one chunk supported!
            result.Append(data);

            return result.ToString();
        }

        #region WireFormat
        protected void Write(UInt32 value)
        {
            byte[] buffer= new byte[4];
            for (int i = 0; i < 4; i++)
            {
                buffer[3 - i] = unchecked((byte)(value & 0xff));
                value = value >> 8;
            }
            this.writer.Write(buffer);
        }

        protected UInt32 ReadUInt32()
        {
            UInt32 ret = 0;
            byte[] buffer= this.reader.ReadBytes(4);
            for (int i = 0; i < 4; i++)
            {
                ret = unchecked((ret << 8) | buffer[i]);
            }
			return ret;
        }

        protected UInt16 ReadUInt16()
        {
            UInt32 ret = 0;
            byte[] buffer = this.reader.ReadBytes(2);
            for (int i = 0; i < 2; i++)
            {
                ret = unchecked((ret << 8) | buffer[i]);
            }
            return (UInt16)ret;
        }

        protected string ReadByteCountedString()
        {
            StringBuilder b= new StringBuilder();
            byte next = 0;
            Encoding encode = Encoding.ASCII;
            do
            {
                UInt16 length = this.ReadUInt16();
                next = this.reader.ReadByte();

                byte[] buffer = new byte[length];
                this.reader.Read(buffer, 0, length);
                b.Append(encode.GetString(buffer));
            } while (next != 0);

            return b.ToString();
        }
        #endregion

        /// <summary>
        /// Send a packet of a given type and return the server's answer in unserialized representation
        /// </summary>
        /// <param name="type">One of the REMOTE_MSG_* constants</param>
        /// <param name="data">The data packet, already serialized</param>
        /// <returns></returns>
        public object SendPacket(byte type, string data) {

            // Write
            this.Write(EASC_MAGIC_NUMBER);
            this.writer.Write((byte)1);           // Major version
            this.writer.Write((byte)0);           // Minor version
            this.writer.Write(type);
            this.writer.Write(false);             // Whether a transaction is active
            this.Write((uint)data.Length);
            byte[] payload = Encoding.ASCII.GetBytes(data);
            this.writer.Write(payload, 0, payload.Length);
            this.writer.Flush();

            // Read answer
            UInt32 magic = this.ReadUInt32();
            byte vmajor = this.reader.ReadByte();
            byte vminor = this.reader.ReadByte();
            byte rtype = this.reader.ReadByte();
            bool tran = this.reader.ReadBoolean();
            UInt32 length = this.ReadUInt32();

            if (magic != EASC_MAGIC_NUMBER)
            {
                this.sock.Close();
                throw new RemoteException("Protocol error", new FormatException("Expected " + EASC_MAGIC_NUMBER + ", have " + magic));
            }

            // Switch on type
            string response;
            switch (rtype)
            {
                case REMOTE_MSG_VALUE:
                    response = this.ReadByteCountedString();
                    object value = this.serializer.valueOf(new SerializedData(response), this.serializerContext);
                    return value;

                case REMOTE_MSG_EXCEPTION:
                    response = this.ReadByteCountedString();
                    object exception = this.serializer.valueOf(new SerializedData(response), this.serializerContext);
                    throw new RemoteException("Exception", (Exception)exception);

                case REMOTE_MSG_ERROR:
                    byte[] buffer = new byte[length];
                    this.reader.Read(buffer, 0, (int)length);
                    throw new RemoteException(Encoding.ASCII.GetString(buffer), new IOException());

                default:
                    this.sock.Close();
                    throw new RemoteException("Protocol error", new IOException());

            }
        }

        #region IDisposableImplementation
        /// <summary>
        /// Closes socket if connected
        /// </summary>
        public void Dispose()
        {
            if (this.sock.Connected)
            {
                this.sock.Close();
            }
        }
        #endregion

        public override string ToString()
        {
            return this.GetType() + "<" + this.sock.ToString() + ">";
        }
    }
}
