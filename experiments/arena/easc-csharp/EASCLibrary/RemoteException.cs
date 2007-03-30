using System;
using System.Collections.Generic;
using System.Text;
using System.IO;

namespace Net.XpFramework.EASC
{
    public class RemoteException: IOException
    {
        #region Constructors
        public RemoteException(string message, Exception inner) : base(message, inner)
        {
        }
        #endregion
    }
}
