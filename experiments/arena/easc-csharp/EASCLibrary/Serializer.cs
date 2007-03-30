using System;
using System.Collections.Generic;
using System.Text;
using System.Reflection;

namespace Net.XpFramework.EASC
{
    class Serializer
    {
        public Dictionary<char, SerializerMapping> mappings = new Dictionary<char, SerializerMapping>();

        #region Deserialization
        /// <summary>
        /// Retrieve value of a serialized string
        /// </summary>
        /// <param name="serialized"></param>
        /// <returns></returns>
        public object valueOf(SerializedData serialized, Dictionary<string, object> context)
        {
            char token = serialized.consumeToken();
            switch (token)
            {
                case 'b': 
                    return !serialized.consumeWord().Equals("0");     // 0 = false, any other value = true

                case 'i': 
                    return int.Parse(serialized.consumeWord());

                case 'd':
                    return double.Parse(serialized.consumeWord());

                case 's': 
                    return serialized.consumeString();

                case 'a':
                    System.Collections.Hashtable array = new System.Collections.Hashtable();
                    int length = serialized.consumeSize();

                    serialized.skipOver(1);     // {
                    for (int i = 0; i < length; i++)
                    {
                        array[this.valueOf(serialized, context)] = this.valueOf(serialized, context);
                    }
                    serialized.skipOver(1);     // }
                    return array;

                case 'O':
                    String classname = serialized.consumeString();
                    int members = serialized.consumeSize();

                    // Check if we can map this type to a class, if we can't, use UnknownRemoteObject
                    Type t= Type.GetType(classname);
                    if (t == null)
                    {
                        UnknownRemoteObject instance= new UnknownRemoteObject();
                        instance.name = classname;
                        serialized.skipOver(1);   // {
                        for (int i = 0; i < members; i++)
                        {
                            instance.members[(string)this.valueOf(serialized, context)] = this.valueOf(serialized, context);
                        }
                        serialized.skipOver(1);     // }
                        return instance;
                    } else {
                        object instance = Activator.CreateInstance(t);
                        serialized.skipOver(1);     // {
                        for (int i = 0; i < members; i++)
                        {
                            string member = (string)this.valueOf(serialized, context);
                            object value = this.valueOf(serialized, context);
                            t.GetMember(member).SetValue(value, 0);
                        }
                        serialized.skipOver(1);     // }
                        return instance;
                    }

                default:

                    // Check the mappings
                    if (this.mappings.ContainsKey(token)) 
                    {
                        return this.mappings[token].valueOf(this, serialized, context);
                    }

                    // No mapping, not builtin
                    throw new FormatException("Unknown token " + token);
            }
        }
        #endregion

        #region Serialization
        /// <summary>
        /// Retrieve serialized representation of a string
        /// </summary>
        /// <param name="s"></param>
        /// <returns></returns>
        public string representationOf(string s, Dictionary<string, object> context)
        {
            return "s:" + s.Length + ":\"" + s + "\";";
        }

        /// <summary>
        /// Retrieve serialized representation of an integer
        /// </summary>
        /// <param name="i"></param>
        /// <returns></returns>
        public string representationOf(int i, Dictionary<string, object> context)
        {
            return "i:" + i;
        }

        /// <summary>
        /// Retrieve serialized representation of a boolean
        /// </summary>
        /// <param name="b"></param>
        /// <returns></returns>
        public string representationOf(bool b, Dictionary<string, object> context)
        {
            return "b:" + (b ? "1" : "0");
        }

        /// <summary>
        /// Retrieve serialized representation of an object
        /// </summary>
        /// <param name="o"></param>
        /// <returns></returns>
        public string representationOf(object o, Dictionary<string, object> context)
        {
            // See if we can find a special method
            Type[] signature= new Type[] { o.GetType(), typeof(Dictionary<string, object>)};
            MethodInfo m= this.GetType().GetMethod("representationOf", signature);
            if (m != null)
            {
                return (string)m.Invoke(this, new object[] { o, context });
            }

            // Fall back to generic object serialization
            StringBuilder result = new StringBuilder("O:");
            result.Append(this.representationOf(o.GetType().FullName, context));
            result.Append(":0:{}");         // FIXME
            return result.ToString();
        }

        /// <summary>
        /// Retrieve serialized representation of an object array
        /// </summary>
        /// <param name="array"></param>
        /// <returns></returns>
        public string representationOf(object[] array, Dictionary<string, object> context)
        {
            StringBuilder result = new StringBuilder("A:");
            result.Append(array.Length);
            result.Append(":{");
            
            foreach (object member in array) {
                result.Append(this.representationOf(member, context));
            }
            result.Append("}");
            return result.ToString();
        }
        #endregion
    }
}
