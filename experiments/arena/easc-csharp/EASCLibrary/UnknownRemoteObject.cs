using System;
using System.Collections.Generic;
using System.Text;

namespace Net.XpFramework.EASC
{

    /// <summary>
    /// Represents a remote object
    /// </summary>
    class UnknownRemoteObject
    {
        public string name;
        public Dictionary<string, object> members = new Dictionary<string, object>();

        /// <summary>
        /// Creates a string representation of this object
        /// </summary>
        /// <returns></returns>
        public override string ToString()
        {
            StringBuilder b = new StringBuilder(this.GetType().FullName).Append("(").Append(this.name).Append(") {\n");
            foreach (string member in this.members.Keys)
            {

                b.Append("  ").Append(member).Append(" => ").Append(this.members[member]).Append('\n');
            }
            return b.Append("}").ToString();
        }
    }
}
