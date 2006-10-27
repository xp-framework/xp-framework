/* This class is part of the XP framework
 *
 * $Id: json.js 42586 2006-05-04 13:25:22Z kiesel $ 
 */

  /**
   * JSONMessage
   *
   * Base class for all JSONRequests
   *
   * @param   method, The remote method to be called
   * @param   params, Arguments to be passed
   * @param   id, A transaction id to track the request
   */        
  function JSONMessage(method, params, id) {
    this.method= method;
    this.params= params;
    this.id    = id;
  }

  /**
   * JSON encode/decode
   *
   */
  var JSON = function () {
    var m = {
      '\b': '\\b',
      '\t': '\\t',
      '\n': '\\n',
      '\f': '\\f',
      '\r': '\\r',
      '"' : '\\"',
      '\\': '\\\\'
    },

    s = {
      'boolean': function (item) { return String(item); },
      number: function (item) { return isFinite(item) ? String(item) : 'null'; },

      string: function (item) {
        if (/["\\\x00-\x1f]/.test(item)) {
            item = item.replace(/([\x00-\x1f\\"])/g, function(a, b) {
            var c = m[b];
            if (c) { return c; }

            // Get char
            c = b.charCodeAt();
            return '\\u00' +
            Math.floor(c / 16).toString(16) + (c % 16).toString(16);
        });
      }
      return '"' + item + '"';
    },
    
    object: function (item) {
      if (item) {
        var a = [], b, f, i, l, v;
        
          // Its an array, so get keys and values
          if (item instanceof Array) {
          
            // Set identifier for arrays "[ / ]"
            a[0] = '[';
            l = item.length;
            
            // Process all fields
            for (i = 0; i < l; i += 1) {
              v = item[i];
              f = s[typeof v];
          
              if (f) {
                v = f(v);
                if (typeof v == 'string') {
                  if (b) { a[a.length] = ','; }

                  a[a.length] = v;
                  b = true;
                }
              }
            }

            a[a.length] = ']';
            
          // Its an object, so transform it
          } else if (item instanceof Object) {
          
            // Set identifier for objects
            a[0] = '{';
            for (i in item) {
            
              v = item[i];
              f = s[typeof v];
             
              if (f) {
                v = f(v);
                if (typeof v == 'string') {
                  if (b) { a[a.length] = ','; }
                  a.push(s.string(i), ':', v);
                  b = true;
                }
              }
            }
            
            a[a.length] = '}';
          } else {
            return '';
          }
          return a.join('');
        }
        return 'null';
      }
    };
    return {

      /**
       * Iterate over all objects trying to identify
       * xp class objects and turn them into an appropriate
       * Javascript equivalent.
       *
       * @access  protected
       * @param   array arr
       * @return  mixed
       */
      iterateArray: function(arr) {
        if ('object' != typeof arr) return arr;

        try {
          if (
            ('undefined' != typeof arr['__jsonclass__']) && 
            ('undefined' != typeof arr['__xpclass__'])
          ) {
            if ('util.Date' == arr['__xpclass__']) {
              return new Date(arr['_utime'] * 1000);
            }
          }
        } catch (ignore) {
          // Catch "Object has no properties" message
        }

        // Recurse
        for (prop in arr) {
          if ('object' == typeof arr[prop]) {
            arr[prop]= this.iterateArray(arr[prop]);
          }
        }

        return arr;
      },

      /**
       * Encode takes a JavaScript value and produces a JSON text.
       * The value must not be cyclical.
       *
       * @access  public 
       * @param   mixed v, Value
       * @return  json string or NULL
       */
      encode: function (v) {
        var f = s[typeof v];
        if (f) {
          v = f(v);
          if (typeof v == 'string') {
            return v;
          }
        }
        return null;
      },

      /**
       * Decode takes a JSON text and produces a JavaScript value.
       * It will return false if there is an error.
       *
       * @access  public  
       * @param   string text
       * @return  javascript value or FALSE
       */
      decode: function (text) {
        try {
          var data= !(/[^,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t]/.test(
            text.replace(/"(\\.|[^"\\])*"/g, ''))) &&
            eval('(' + text + ')'
          );
        } catch (e) {
          return false;
        }

        return this.iterateArray(data);
      }
    };
  }();
