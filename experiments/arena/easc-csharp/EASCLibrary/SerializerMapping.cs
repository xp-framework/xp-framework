using System;
using System.Collections.Generic;
using System.Text;

namespace Net.XpFramework.EASC
{
    interface SerializerMapping
    {
        object valueOf(Serializer serializer, SerializedData serialized, Dictionary<string, object> context);

        string representationOf(Serializer serializer, object value, Dictionary<string, object> context);
    }
}
