using System;
using System.Collections.Generic;
using System.Text;

namespace Net.XpFramework.EASC
{

    /// <summary>
    /// Entry point class for the EASC Remoting API
    /// </summary>
    public class Remote
    {
        XPProtocol proto = null;
        static Dictionary<string, Remote> instances = new Dictionary<string, Remote>();

        #region Constructors
        protected Remote(Uri u)
        {

            this.proto = new XPProtocol();
            this.proto.initialize(u);
        }
        #endregion

        /// <summary>
        /// Entry point method: Gets a remoting instance for a given DSN
        /// </summary>
        /// <param name="dsn"></param>
        /// <returns>A remoting instance</returns>
        public static Remote ForName(string dsn)
        {
            if (!instances.ContainsKey(dsn))
            {
                instances[dsn]= new Remote(new Uri(dsn));
            }
            return instances[dsn];
        }

        /// <summary>
        /// Look up a remote name and return a proxy to it
        /// </summary>
        /// <param name="name">JNDI name</param>
        /// <returns>A proxy object</returns>
        public object Lookup(string name)
        {
            return this.proto.SendPacket(XPProtocol.REMOTE_MSG_LOOKUP, this.proto.ByteCountedStringOf(name));
        }

        /// <summary>
        /// Creates a string representation of this object
        /// </summary>
        /// <returns></returns>
        override public string ToString()
        {
            return this.GetType().ToString() + "@" + this.proto.ToString();
        }
    }
}
