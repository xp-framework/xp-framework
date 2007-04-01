using System;
using System.Collections.Generic;
using System.Text;
using System.Reflection;
using System.Runtime.Remoting;
using System.Runtime.Remoting.Proxies;
using System.Runtime.Remoting.Messaging;

namespace Net.XpFramework.EASC
{
    class Proxy : RealProxy, IRemotingTypeInfo
    {
        private int oid;
        private XPProtocol handler;

        protected Proxy(int oid, XPProtocol handler, Type t) : base(t)
        {
            this.oid = oid;
            this.handler = handler;
        }

        private static Type FindTypeByName(string name)
        {
            foreach (Assembly a in AppDomain.CurrentDomain.GetAssemblies())
            {
                Type t = a.GetType(name);
                if (t != null) return t;
            }
            throw new TypeLoadException("Cannot find type " + name);
        }

        /// <summary>
        /// Entry point method
        /// </summary>
        /// <param name="oid">Object ID</param>
        /// <param name="name">Stub Interface Name</param>
        /// <param name="handler">XP Protocol instance</param>
        /// <returns></returns>
        public static object NewProxyInstance(int oid, string name, XPProtocol handler)
        {
            try
            {
                return new Proxy(oid, handler, FindTypeByName(name)).GetTransparentProxy();
            } 
            catch (TypeLoadException e)
            {
                throw new RemoteException("Cannot find type", e);
            }
        }

        #region IRemotingTypeInfoImplementation
        public bool CanCastTo(System.Type toType, object obj)
        {
            return true;        // We'll allow casting to any type
        }

		public string TypeName {
			get { throw new System.NotSupportedException("TypeName for Proxy isn't supported"); }
			set { throw new System.NotSupportedException("TypeName for Proxy isn't supported"); }
		}
        #endregion

        public override ObjRef CreateObjRef(System.Type type)
        {
            throw new NotSupportedException("ObjRef for Proxy isn't supported");
        }

        public override IMessage Invoke(IMessage msg)
        {
            IMethodCallMessage call = msg as IMethodCallMessage;
            if (call == null)
 			{
 				throw new NotSupportedException("Unsupported call type " + msg.GetType());
 			}

            // Handle ToString specially
            if (call.MethodName == "ToString")
            {
                return new ReturnMessage(this.ToString(), null, 0, null, call);
            }

            // Call method on server
            StringBuilder message= new StringBuilder();
            message.Append("\x00\x00\x00\x00");
            message.Append((char)unchecked((byte)((((this.oid >> 8) >> 8) >> 8) & 0xff)));
            message.Append((char)unchecked((byte)(((this.oid >> 8) >> 8)& 0xff)));
            message.Append((char)unchecked((byte)((this.oid >> 8) & 0xff)));
            message.Append((char)unchecked((byte)((this.oid) & 0xff)));
            message.Append(this.handler.ByteCountedStringOf(call.MethodName));
            message.Append(this.handler.ByteCountedStringOf(this.handler.serializer.representationOf(
                call.Args, 
                this.handler.serializerContext
            )));

            try
            {
                object ret = this.handler.SendPacket(XPProtocol.REMOTE_MSG_CALL, message.ToString());
                return new ReturnMessage(ret, null, 0, null, call);
            }
            catch (Exception e)
            {
                return new ReturnMessage(e, call);
            }
        }

        public override string ToString()
        {
            return this.GetType().ToString() + "(#" + this.oid + ")";
        }
    }
}
