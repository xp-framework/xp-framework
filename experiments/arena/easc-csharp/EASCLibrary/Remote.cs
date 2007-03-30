using System;
using System.Collections.Generic;
using System.Text;

namespace Net.XpFramework.EASC
{
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
        /// <returns></returns>
        public static Remote ForName(string dsn)
        {
            if (!instances.ContainsKey(dsn))
            {
                instances[dsn]= new Remote(new Uri(dsn));
            }
            return instances[dsn];
        }

        public object Lookup(string name)
        {
            return this.proto.SendPacket(XPProtocol.REMOTE_MSG_LOOKUP, this.proto.ByteCountedStringOf(name));
        }

        override public string ToString()
        {
            return this.GetType().ToString() + "@" + this.proto.ToString();
        }
    }
}
