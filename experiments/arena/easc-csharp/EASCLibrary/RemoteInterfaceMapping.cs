using System;
using System.Collections.Generic;
using System.Text;

namespace Net.XpFramework.EASC
{
    class RemoteInterfaceMapping : SerializerMapping
    {
        public Object valueOf(Serializer serializer, SerializedData serialized, Dictionary<string, object> context)
        {
            int oid = serialized.consumeSize();
            serialized.skipOver(1);     // "{"
            string name = (string)serializer.valueOf(serialized, context);
            serialized.skipOver(1);     // "}";

            return Proxy.NewProxyInstance(oid, name, (XPProtocol)context["handler"]);
        }

        public string representationOf(Serializer serializer, Object value, Dictionary<string, object> context)
        {
            throw new NotImplementedException();
        }
    }
}
