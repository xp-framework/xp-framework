using System;
using System.Collections.Generic;
using System.Text;

namespace Net.XpFramework.EASC
{
    /// <summary>
    /// Represents serialized data, see PHP's serialize() function
    /// </summary>
    class SerializedData
    {
        protected string buffer;
        protected int offset;

        #region Constructors
        public SerializedData(string serialized)
        {
            this.buffer = serialized;
            this.offset = 0;
        }
        #endregion

        public void skipOver(int length)
        {
            this.offset += length;
        }

        public char consumeToken()
        {
            char token = this.buffer[this.offset];
            this.offset += 2;
            return token;
        }

        public string consumeString()
        {
            string length = this.buffer.Substring(this.offset, this.buffer.IndexOf(':', this.offset)- this.offset);
            int b = length.Length + 2;
            int l = int.Parse(length);
            string value = this.buffer.Substring(this.offset + b, l);
            this.offset += b + l + 2;
            return value;
        }

        public string consumeWord()
        {
            string word = this.buffer.Substring(this.offset, this.buffer.IndexOf(';', this.offset) - this.offset);
            this.offset += word.Length + 1;
            return word;
        }

        public int consumeSize()
        {
            string size = this.buffer.Substring(this.offset, this.buffer.IndexOf(':', this.offset) - this.offset);
            this.offset += size.Length + 1;
            return int.Parse(size);
        }
    }
}
