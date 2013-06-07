XP Framework ChangeLog
========================================================================

## ?.?.? / ????-??-??

### Bugfixes

* Fixed issue #304: Classpath element not found error not very helpful - (@thekid)
* Fixed issue #305: rdbms.SQLStatementFailedException (errorcode 0: Unexpected 
  token 0x02 (number 0) - (@thekid)

### Features

* Added toString() implementations to unittest.mock.Expectation and 
  ExpectationList classes - (@thekid)


## 5.9.3 / 2013-05-22

### RFCs

* Implemented RFC 273: ChangeLog formatting - (@thekid)

### Bugfixes

* Only add .xar and .php files to class path in XP installer - (@thekid)
* Fixed CompositeProperties::readString(), readFloat() and readRange()'s
  default values being inconsistent with the corresponding methods from
  util.Properties. - (@thekid)
* Fixed issue #302: Exception when not specifying a context class - (@thekid)
* Fixed issue #301: xprt-update fails - (@thekid)

### Features

* Changed ClassLoader::defineClass() and ClassLoader::defineInterface()
  to load parent classes and implemented interfaces if necessary instead
  of raising an exception. See feature request in issue #300 - (@thekid)


## 5.9.2 / 2013-05-16

### Heads up!

* Deprecated scriptlet.xml.workflow.casters.ToFloat in favor of ToDouble
  for the sake of consistency - (@thekid)

### Bugfixes

* Fixed issue #298: Cannot use protected methods as @values provider - (@thekid)
* Fixed issue #297: ArrayType and MapType's isInstance() methods - (@thekid)

### Features

* Added util.Objects utility class simplifying equals(), toString() and
  hashCode() for any type
  http://news.planet-xp.net/article/530/2013/05/14/ - (@thekid)
* Started rewriting tests using foreach with fixtures to use `@values` - (@thekid)


## 5.9.1 / 2013-05-10

### Bugfixes

* Fixed namespaced generic classes not being declared correctly - (@thekid)
* Fixed generic classes not being available by their namespaced versions - (@thekid)
* Fixed issue #294: xpi upgrade just reinstalls existing version - (@thekid)

### RFCs

* Implemented RFC 0267: Unittest parameterization implementation - (@thekid)


## 5.9.0 / 2013-05-05

### Heads up!

* Changed newinstance() to invoke autoloading mechanism, see issue #277 - (@thekid)
* Deprecated xp::$registry in favor of dedicated functionality in the xp
  class, while retaining BC for xp::$registry['details.'.$name] for the
  XP Compiler. See pull request #270 - (@thekid)
* Removed ability to run scripts using require('lang.base.php') as their
  first line in order to boostrap the framework, deprecated since the
  introduction of the XP Runners five years ago. Side effect of #269. - (@thekid)
* Changed reflection to use "var" instead of "void" as return type when
  no api documentation is available. See pull request #264 - (@thekid)
* Changed object identity calculation from microtime() to uniqid in order
  to correctly work with PHP 5.5.0beta1. See issue #260 for details - (@thekid)
* Removed ability to supply multiple classpaths via one "-cp" argument
  in XPCLIs (xp.command.Runner, see issue #259, not portable!) - (@thekid)
* Removed deprecated classes (see pull request #254) - (@kiesel)
* Changed message for missing static methods to include the word "static"
  and made it consistent with the one for missing non-static methods - (@thekid)
* Minimum PHP version requirement is now PHP 5.3.0!
  http://news.planet-xp.net/article/501/2013/02/06/ - (@thekid)
* Removed deprecated package mapping registration via packageMapping() in
  remote.protocol.Serializer - (@thekid)
* Changed repository layout to Maven Standard Directory Layout - see pull
  requests #222 and #224 - (@kiesel, mrosoiu, @thekid)
* Removed old and deprecated REST API - see pull request #223 - (@thekid)
* The finally() function is replaced by ensure($t) - see pull request #187
  This way we're forward-compatible with PHP 5.5 - see issue #186.
  http://news.planet-xp.net/article/481/2012/09/30/ - (@thekid)
* The SAPI feature is now deprecated and will be removed in next major series! - (@thekid)
* Changed newinstance() to declare classes inside the package of the 
  base class (or interface) - (@thekid, @kiesel)
* Changed lang.reflect.Field::getType() to return lang.Type instances.
  Added getTypeName() to restore old behaviour. See issue #34. - (@thekid)
* Merged pull request #93: Access to private constructors, methods and fields - (@thekid)

### RFCs

* Implemented RFC 0208: Coding standards update: PHP 5.3 namespaces - (@thekid)
* Implemented RFC 0270: XPI - Install XP Modules (pull request #287) - (@thekid)
* Implemented RFC 0265: Bump PHP requirement to PHP 5.3.0 minimum - (@thekid)
* Implemented RFC 0210: Separate contrib & framework versions - (@thekid)
* Implemented RFC 0260: XP 5.9 as default branch - (@thekid)
* Implemented first part of RFC 0186 - deprecated SAPI feature. - (@thekid)
* Implemented RFC 0218: Parameter annotations (pull request #116) - (@thekid)
* Implemented RFC 0229: New util.UUID class (pull request #149)
  http://news.planet-xp.net/article/459/2012/04/23/ - (@thekid)
* Implemented RFC 0215: Add accessor for XP Framework version  - (@kiesel, @thekid)
* Implemented RFC 0222: Optional support for PHP 5.3 namespaces
  http://news.planet-xp.net/article/433/2012/01/06/ - (@kiesel, @thekid)

### Bugfixes

* Fixed warnings raised inside AbstractClassLoader::loadClass0() not
  being properly attached to the CNFE raised inside. - (@thekid)
* Fixed issue #293 - RestDeserializerConversionTest fails with fatal 
  error on PHP 5.3.3 - (@thekid, @kiesel)
* Fixed issue #292 - Binford::setPoweredBy() failing when given zero (0) - (@thekid)
* Fixed MimeType::getByFilename() returning incorrect mine type for
  .tar.gz files - (@thekid)
* Ensure AbstractDeferredInvokationHandler::initialize() returns a 
  lang.Generic instance - (@thekid)
* Fixed issue #291 - Namespaced class doesn't support class aliasing - (@thekid)
* Fixed Process::getProcessById() not to yield PHP path for any PID 
  given - move the code to lang.Runtime! - (@thekid)
* Fixed bug with newinstance inside namespaced classes. See bug #276 - (@thekid)
* Fixed Reflection bugs for abstract methods in generic classes. See
  pull request #263 - (@thekid)
* Fixed generic types not allowing argument default values - (@thekid)
* Fixed issue #132: Namespaced classes not usable in generics - (@thekid)
* Fixed issue #130: newinstance() fails to respect namespace - (@kiesel, @thekid)
* Fixed issue #94: Dynamic Classloader does not provide defined classes - (@thekid)

### Features

* Added ability to handle multiple file uploads with ToFileData caster.
  See pull request #289 - (@treuter)
* Added webservices.rest.RestResponse::stream() method - (@thekid)
* Added string casting support to text.regex.Pattern - (@thekid)
* Added pattern Argument Matcher for Mocking Framework - pull request #268 - (@iigorr)
* Made configuration directory passable to scriptlet runners, and change
  xpws command line to support this via "-c". See pull request #266. - (@thekid)
* Greatly improved the performance of the lang.types.String class by using
  the "mbstring" extension when available. See pull request #267. - (@thekid, @pdietz, @kiesel)
* Added ability to subclass REST handlers - see pull request #262 - (@thekid)
* Implemented REST file downloads API - see pull request #261 - (@thekid)
* Added new Timer::measure() method - see pull request #244
  http://news.planet-xp.net/article/506/2013/03/18/ - (@thekid)
* Made the RestFormat class usable within the client - pull request #256 - (@thekid)
* Added Type::isAssignableFrom() - see pull request #249 - (@thekid)
* Changed xp::stringOf() to indent objects - (@thekid)
* Added support for array shorthand as annotation value - see issue #66 - (@thekid)
* Simplified enum declaration - see pull request #229 - (@thekid)
* Implemented rest of pull request #166 and changed all classes depending
  on xml.Node and xml.Tree to use new accessor methods. - (@kiesel)
* Added support for ".php" files inside .pth-files; eases integration
  with foreign libraries. See pull request #218. - (@thekid, @kiesel)
* Added support for looking up configuration through the ClassLoader /
  ResourceProvider infrastructure in pull request #217, #221 - (@mrosoiu, @kiesel)
* Added preliminary support for PHP 5.5
  http://news.planet-xp.net/article/508/2013/03/29/
  http://news.planet-xp.net/article/493/2012/12/31/ - (@thekid, @kiesel)
* Added xp::ENCODING constant and replaced hardcoded values througout
  the framework's source code. See issue #35 for details. - (@thekid, @kiesel)


## 5.8.14 / 2013-05-17

### Heads up!

* This is the last planned release on the xp5_8 branch! - (@thekid, @kiesel)

### Bugfixes

* Added quickfix for #302: Exception when not specifying a context class - (@thekid)

### Features

* Changed REST routing mechanism to allow any character in path segments.
  See pull request #288. - (@thekid)


## 5.8.13 / 2013-04-13

### Heads up!

* Fixed issue #286: PHP 5.4.14 and PHP 5.3.24 support; a workaround for
  https://bugs.php.net/bug.php?id=64370 - (@thekid)

### Bugfixes

* Fixed connect() method for mysql+i driver - (@thekid)
* Fixed issues #280 and #281 - TDS variant, nvarchar and length fixes - (@thekid)
* Fixed issue #278 - Missing TdsV7 T_VARIANT subtype implementations - (@thekid)

### Features

* Added serialization for lang.types wrapper types to REST serializer
  See pull request #285. - (@thekid)
* Added exception mapping / marshaller accessors to RestContext, see 
  pull request #284. - (@iigorr, @thekid)
* Implemented seeking in rdbms.mysqlx.MySqlxBufferedResultSet (for 
  mysql+x driver, motivated by issue #282). - (@thekid)
* Implemented seeking in rdbms.tds.TdsBufferedResultSet (for mssql+x and
  sybase+x drivers, motivated by issue #282). - (@thekid)


## 5.8.12 / 2013-03-18

### Heads up!

* Changed TypeMarshaller::unmarshal() signature - see pull request #255 - (@thekid)
* Removed public one-arg constructor magic from REST deserialization
  See pull request #252 - (@thekid, @iigorr)

### Features

* Added accessors for HTTP timeouts to RestClient - see pull request #250 - (@kiesel)
* Separated REST exception marshalling (payload representation) from the 
  way it is mapped to the response (headers) - see pull request #248 - (@thekid)
* Added ability to inject arbitrary log categories by their name in REST 
  handlers - http://news.planet-xp.net/article/502/2013/03/01/ - (@thekid)
* Added log context implementation - see pull request #239 - (@mrosoiu)
* Added ability to inspect web configuration - see pull request #238 - (@thekid)

### Bugfixes

* Fixed issue #242 - DataSet generator skips indices - (@thekid)
* Fixed issue #251 - PHP 5.3.3 failing missing methods test. Behaviour
  change (only in that PHP version) caused by https://bugs.php.net/51176 - (@thekid)
* Changed FTP integration tests to use in-memory storage and reinstate them
  in the test configuration. See pull request #246. - (@thekid)
* Fixed peer.ftp.server.FtpProtocol port claim strategy when
  opening passive mode sockets - see pull request #245 - (@kiesel, @thekid)
* Fixed class loading inconsistencies with case-insensitive filesystems.
  See pull request #235. - (@thekid)


## 5.8.11 / 2013-01-30

### RFCs

* Implemented RFC 0196: I/O Collections random access - (@thekid)

### Bugfixes

* Fixed issue #230: Tests with closures inside cause abnormal exit - (@thekid)
* Fixed io.FileUtil class to work if File::read() returns less than the
  given chunk size. See pull request #220 - this fixes FileUtil used in
  combination with ResourceProvider, e.g. - (@thekid, @oohinckel)

### Features

* Added iteration support to util.collections.HashTable - pull request #225 - (@thekid)
* Added accessors for attributes and content to xml.Node and new root()
  method to xml.Tree. See pull request #166, partially applied for forward
  compatibility - (@kiesel, @thekid)
* Merged pull request #217 - Resource property source - (@mrosoiu)
* Merged pull request #216 - Image metadata reader - (@thekid)


## 5.8.10 / 2013-01-03


### Heads up!

* Deprecated old REST API in webservices.rest.server, pull request #210 - (@thekid)
* Dropped interpackage dependency util -> rdbms, pull request #204 - (@kiesel)
* Dropped interpackage dependency util.semaphore -> scriptlet, pull request #205 - (@kiesel)

### RFCs

* Implemented RFC 0243 - REST Client & Serverside APIs RFC, pull request #210 - (@thekid)

### Bugfixes

* Fixed Parameter::getType() and getTypeName() for inherited methods / ctors - (@thekid)
* Fixed HttpScriptlet to support "PATCH" verb - (@thekid)
* Fixed xp::extensions() to also work exclusively with type restrictions - (@thekid)
* Fixed Unicode conversion for sybase databases in cases where "utf8" is
  not recognized by iconv library - (@thekid)
* Fixed rdbms.sybase.SybaseDBAdapter to exclude incomplete index records - (@thekid)
* Fixed remote.ByteCountedString's length() method for zero-length strings - (@thekid)
* Fixed TDS protocol's T_DATETIME4 type not being deserialized correctly - (@thekid)
* Fixed parsing of HTTP headers w/o value in peer.http.HttpResponse - (@thekid, @kiesel)
* Fixed OCCURRENCE_OPTIONAL being ignored for missing file upload input 
  fields - see pull request #198 - (@mrosoiu)
* Fixed encoding of associative array with modified pointer position by
  merging pull request #199 - (@andrei21funk, @thekid)
* Fixed $__generic-style generics (BC with < 5.8) breaking. They yielded
  a php.stdClass instance instead of the one to be instantiated, which 
  would result in "Fatal error: Call to undefined method stdClass::X()" 
  when any method X would be called - (@thekid)

### Features

* Added RestResponse::headers() and RestResponse::header() methods - (@thekid, @kiesel)
* Changed command line reflection (xp -r) to accept file and directory
  names referring to classes and packages - (@thekid)
* Changed command line reflection (xp -r) to display all class constants.
  Added forgotten lang.XPClass::getConstants() method to accomplish this. - (@thekid)
* Added @xmlwrapped annotation - see pull request #213  - (@thekid)
* Added xp.unittest.ColoredBarListener - see issue #207 - (@kiesel, iigorr, @oohinckel)
* Added support for unittest listener options - see pull request #201 - (@thekid, @oohinckel)
* Improved generics instance creation by factors between 1.2 and 1.6 - (@thekid)
* Added support for peer.Header instances in REST client - pull #202 - (@thekid)
* Added ArrayType and MapType constructors which accept component types
  in pull request #197 - (@thekid)


## 5.8.9 / 2012-10-19

### Heads up!

* Deprecated finally() in favor of ensure($t) - see issue #186.
  http://news.planet-xp.net/article/481/2012/09/30/ - (@thekid)

### Bugfixes

* Added support for double data type in REST serializer - see pull #192 - (@weiher)
* Fixed JsonDecoder to ignore trailing whitespace - see issue #189. - (@thekid)
* Added forward-compatible version of handling unpack("a...") data. 
  With PHP 5.5.0, this has been changed to include trailing \0s, see
  https://bugs.php.net/bug.php?id=61038 - (@thekid)
* Replaced preg_replace() calls with /e modifier where applicable, it
  is deprecated in PHP 5.5.0 - (@thekid)

### Features

* Added SSL-specific options for sockets - see pull request #193  - (@kiesel)
* Added failover Sybase/MSSQL support via userland driver by merging 
  the incubator/tds branch in pull request #190. 
  http://news.planet-xp.net/article/483/2012/10/11/ - (@thekid, @kiesel) 
* Added DETAIL_TARGET_ANNO for forward compatibility with parameter
  annotations implementation to core and compiler. - (@thekid)


## 5.8.8 / 2012-09-30

### Bugfixes

* Added support for MySQL time column type - see pull request #185 - (@andstefiul)
* Fixed bug in compiler resolving platform-specific constants, e.g.
  DIRECTORY_SEPARATOR, at compile time. - (@thekid)

### RFCs

* Implemented RFC 0254 - Builtin development webserver "xpws"
  http://news.planet-xp.net/article/478/2012/09/29/ - (@thekid)

### Features

* Changed scriptlet API to use $_SERVER instead of getenv() - this is more
  portable to different web server SAPIs. See pull request #184 - (@thekid)


## 5.8.7 / 2012-08-17

### Bugfixes

* Fixed issue #182: URLs with `@` inside fail to parse - (@thekid)
* Allowed _-.: as values in rest paths - see pull request #175 - (@kiesel)
* Fixed optional params for rest server - see pull request #152 - (@mihaighigea, @kiesel, @iigorr)
* Fixed HTTP content length calculation for empty POST bodys. - (@kiesel, @iigorr)

### Features

* Added equals() methods to io.File & io.Folder - pull request #181 - (@thekid)
* Preferred "debug" loglevel over "info" in remote package - less verbose.
  See pull request #180 - (@kiesel)
* Extracted class parsing into own method (testability) - pull request #177 - (@thekid)
* Added support for "self" as type in @return, @param - pull request #176 - (@thekid)


## 5.8.6 / 2012-07-09

### Bugfixes

* Changed drivers for Sybase and MSSQL to also emit seconds of a given 
  util.Date - see pull request #169 - (@oohinckel, @thekid)

### RFCs

* Implemented RFC 0256 - Improve unittest's error messages 
  See also pull request #40. - (@kiesel, @thekid)
* Implemented RFC 0255 - Allow multiple @beforeClass / @afterClass methods.
  See also pull request #100. - (@thekid)

### Features

* Improved unittest output when comparing large strings (pull request #30) - (@thekid, @kiesel)
* Added unittest.TestCase::skip() method - see pull request #173 - (@thekid)
* Added default logger to scriptlet handlers - see pull request #62 - (@oanas, @thekid)
* Added colored output for unittest runner - see issue #25 / pull #172 - (@kiesel, @thekid)
* Added support for supplying a path to the unittest command - will run
  all test case classes inside the given directory and its subdirectories.
  See pull request #170 - (@thekid, @kiesel)
* Added unittest listener to update terminal title - pull request #171 - (@kiesel, @thekid)
* Added support for ! in classpaths - see pull request #168 - (@mrosoiu)


## 5.8.5 / 2012-06-08

### Bugfixes

* Fixed problem with multiple entries in X-Forwarded-Host header. See 
  issue #162, and http://httpd.apache.org/docs/2.2/mod/mod_proxy.html - (@thekid)
* Fixed support for REST Object collections (see pull request #164) - (@thekid, @ghiata)
* Fixed creation of Date from a float value causes PHP fatal error - see
  pull request #156 - (@ghiata)
* Fixed broken ExtensionMethodsIntegrationTest::trimMethod()  - (@thekid, @mrosoiu)
* Merged pull request #145 - Fix stacktrace scope when calling an undefined 
  method using call_user_func_array() - (@mrosoiu, @thekid)
* Merged pull request #141 - Fix when using CURL transports w/ SSL version - (@mihaighigea)
* Fixed issue #137 - Extension methods don't work on lang.Throwable - (@thekid)
* Fix typo in lang.ClassLoader to correct method's argument list,
  see pull request #134 - (@mrosoiu, @kiesel)
* Handle empty Json request gracefully. - (@iigorr)
* Merged pull request #128 - Don't access scriptlet.HttpScriptletRequest
  params public property, use setters and getters instead - (@cconstandachi)

### RFCs

* Implemented RFC 0235 - CsvMapReader, CsvMapWriter (pull request #148) - (@thekid)

### Features

* Changed CsvWriter to be less strict in what it accepts - see issue #123 - (@thekid)
* Added new peer.http.HttpConnection::patch() method - pull request #161 - (@thekid)
* Added new HTTP status codes defined in RFC 6585, see issue #158 - (@thekid)
* Added HttpResponse::getHeaderString() and HttpRequest::getHeaderString()
  see pull request #153 - (@thekid)
* New method lang.reflect.Package::getComment() - pull request #139 - (@thekid)
* Merged pull request #144 - Some improvements of the RestDataCaster - (@Stormwind, @thekid)
* Merged pull request #143 - New peer.URL::getCanonicalURL() method
  http://news.planet-xp.net/article/453/2012/04/06/ - (@mihaighigea)
* Merged pull request #131 - Add finalizeReloaded(), finalizeCancel(),
  finalizeSuccess() - (@cconstandachi, @thekid)
* Changed webservices.rest.server.RestDataCaster methods to be non-static. - (@iigorr)
* Added possibility to handle error in RestHttpScriptlet using instances
  of RestErrorFormatter. - (@iigorr)
* Changed text.parser.generic.ParseException to contain the parse error
  instances, not only their messages. - (@iigorr, @thekid)
* Improved error handling in RestHttpScriptlet - (@iigorr)


## 5.8.4 / 2012-02-26

### Heads up!

* Removed deprecated img.graph package
  http://news.planet-xp.net/article/446/2012/02/21/ - (@thekid)
* Changed scriptlet request parameters and headers to be case-preserving
  See issue #120, http://news.planet-xp.net/article/443/2012/02/08/ - (@thekid, @kiesel)
* Removed "localhost" local sockets magic from MySQL implementations
  http://news.planet-xp.net/article/434/2012/01/08/ - (@thekid, @kiesel)
* Changed sqlite scheme to default to SQLite3 instead of SQLite2.
  http://news.planet-xp.net/article/432/2012/01/06/ - (@kiesel)
* Made util.PropertyManager::getProperties() throw lang.ElementNotFoundException
  in case not a single Properties could be provided by any PropertySource. See
  issue #95 - (@kiesel, @thekid)
* Merged pull request #89: Make JsonDecoder / parser accept target
  encoding; the parser returns native PHP strings instead of
  lang.types.String objects - (@kiesel)

### RFCs

* Implemented RFC 0237 - xp -w and xp -d - (@thekid)
* Implemented RFC 0221 - Allow combining application properties from
  multiple sources - (@kiesel)
* Implemented RFC 0219 - Mocking Library - (@iigorr)

### Bugfixes

* Fixed broken class-external extension method resolution in the XP compiler - (@thekid)
* Fixed bug #127: Exception cause swallowed - (@thekid)
* Fixed default timeout setting in XpProtocolHandler. - (@ledermueller)
* Fixed timeout settings in BSDSocket to not overwrite the general timeout with
  the connect timeout. - (@ledermueller, @kiesel)
* Changed lang.types.Boolean to accept TRUE, FALSE, integers and the strings 
  "true" and "false" and throw exceptions otherwise - (@thekid, @mrosoiu)
* Merged pull request #122: Check for numeric value during creation of numbers - (@ledermueller)
* Merged pull request #119: Json serialization for Generic objects - (@mrosoiu)
* Fixed readBool() in Properties to also accept different notations of true
  values like "True" or "TRUE" - (@robert)
* Fixed issue #112: XmlRpcDecoder does not comply with XML-RPC specifications - (@ppetermann, @thekid)
* Fixed issue #109: Errors in CurlHttpTransport  - (@ppetermann, @thekid)
* Fixed issue #100: XPCLI: -c does not overwrite properties, but adds - (@thekid)
* Fixed issue #97: Fatal error in scriptlet testsuite - (@thekid)
* Fixed peer.server.ForkingServer to continue execution even when forking one
  child fails - (@kiesel, @thekid)
* Fixed util.Date::create() to throw an exception when given invalid
  arguments instead of creating a date with values from the current 
  date and time filled in - pull request #88 (cconstandachi, thekid)
* Fixed money values in rdbms.sqlsrv.SqlSrvConnection (thekid)
* Fixed rdbms.sqlsrv.SqlSrvConnection::query() to cancel previous results
  if necessary, preventing "The connection cannot process this operation 
  because there is a statement with pending results" errors. - (@thekid)
* Fixed rdbms.mysqli.MySQLiConnection::query() to cancel previous results
  if necessary, preventing "Commands out of sync" errors - (@thekid)
* Fixed warnings raised from rdbms.mysql.MySQLConnection::query() when
  unbuffered results have not completely been read - (@thekid)

### Features

* Added support for setting a timeout programmatic or via dsn in the
  HandlerInstancePool to make it possible to overwrite the default timeout of
  60 seconds. - (@ledermueller)
* Added Boolean::$TRUE and Boolean::$FALSE to lang.types.Boolean - (@thekid)
* Added support for multiple configuration paths for web applications
  see RFC 0221 (pull request #118) - (@kusnier)
* Added support for multiple -c arguments for xpcli runner
  see RFC 0221  (pull request #118) - (@kusnier)
* Added lang.reflect.Field::getTypeName() for forward compatibility - see 
  comments in issue #34
  http://news.planet-xp.net/article/440/2012/01/28/ - (@thekid)
* Merged pull request #115: Properties: Support uppercase booleans - (@ledermueller)
* Added tests for rdbms.Statement class - (@ohinckel)
* Merged pull request #103 - Extended Server Protocol, allowing to handle
  accepting sockets and out of resources situations. - (@thekid)
* Merged pull request #105 - MySQL local sockets, adding the possibility
  to use local sockets (Unix sockets on Un*x, Named Pipes on Windows). - (@thekid)
* Merged pull request #104 - Add support for SQLite3 - (@kiesel)
* Merged pull request #99 - Propertymanager paths CRUD - (@thekid)
* Made database drivers implementations selectable - see pull request #79
  http://news.planet-xp.net/article/431/2011/12/30/ - (@thekid)
* Added optional timezone to util.Date::now() - see pull request #88 - (@cconstandachi, @thekid)
* Merged pull request #90: Add webservices.rest - (@kiesel, @thekid)


## 5.8.3 / 2011-12-08

### Heads up!

* Added initial support for PHP 5.4 by a couple of forward-compatible
  adjustments and workarounds. Please note PHP 5.4 is not officially
  released yet and thus PHP 5.4 support is preliminary at best!
  Discussion and details available in issue #76 - (@thekid)
* Changed JSON parser to use a generated parser instead of hand-crafted
  one. Implements functionality suggested in pull request #37  - (@thekid, @Stormwind)
* Deprecated scriptlet.xml.workflow.Handler class. See issue #55 - use 
  the new scriptlet.xml.workflow.AbstractHandler class instead! - (@thekid)

### RFCs

* Implemented RFC 0216 - REST server API implementation - (@ohinckel)

### Bugfixes

* Fixed peer.BSDSocket connect timeout - see pull request #86 - (@ohinckel)
* Fixed rdbms.DSN::equals() method to no longer regard differing observer
  params as equal - (@thekid)
* Fixed timezone issues when runnning complete test suite - see issue #72 - (@thekid)
* Allowed for deprecated multi-value annotations - see issue #69
  http://news.planet-xp.net/article/422/2011/10/19/ - (@thekid)
* Fixed rdbms.mysqlx.AbstractMysqlxResultSet::record() method to return
  only a single field value if its field parameter is set - (@thekid)
* Changed program's exit code to 0xff in case an uncaught exception occurs. 
  http://news.planet-xp.net/article/424/2011/10/14/ - (@kiesel)
* Fixed fatal error in webservices testsuite when running on PHP
  without ext/soap - (@thekid, @kiesel)
* Fixed request value is not unset/disabled when set via setFormValue()
  when using new AbstractHandler class, old behavior retained for BC 
  reasons - see issue #55 - (@thekid, @andstefiul)
* Fixed annotation parsing:
  . Support for @anno(array(...)) 
  . Support for @anno(...) where ... is any scalar value
  . Fix for strings containing a) annotations b) braces c) equal signs
  . Fix for arrays containing strings
  See pull request #56 and comments. - (@mrosoiu, @thekid)
* Fixed timeout for FTP connections - see pull request #58 - (@kiesel, @treuter)

### Features

* Include more information in output produced by XmlTestListener - (@ohinckel)
* Made usage of encoding names consistant to all lowercase,
  see issue #73 - (@ppetermann, @kiesel)
* Added support to DomXSLProcessor to load XML or XSL files by stream
  wrappers, e.g. using res:// to load files with ResourceProvider - (@ohinckel)
* Added Unicode support to webservices.json.JsonDecoder - characters are
  now correctly converted to escape sequences and back. - (@thekid)
* Added lang.types.String::$EMPTY constant - (@thekid)
* Added webservices.json.JsonDecoder::encodeTo() and ::decodeFrom()
  methods to work with io.streams API - (@thekid, @Stormwind)
* Added lang.types support for webservices.soap implementations, as
  proposed in issue #18 - (@kiesel)
* Added util.Properties::removeSection() and ::removeKey() methods, as
  proposed in issue #54 - (@kiesel)
* Added peer.Header::equals() as proposed in pull request #61 - (@andstefiul)


## 5.8.2 / 2011-09-07

### Heads up!

* Added support for PHP 5.3.8 (see issue #52) - (@thekid)
* Added support for APC by supplying dev and ino elements inside class
  loading infrastructure. Implements issue #51 - (@thekid, @Thomas Geiger)
* Added support for PHP 5.3.7 (see issue #49) - includes workaround for
  PHP Bug 55439 (crypt() returns only salt w/ md5 method) - (@thekid)
* Removed lang.Collection deprecation left-overs - see issue #42 - (@thekid)
* Introduced common interface for SOAP implementations:
  webservices.soap.ISoapClient (see issue #19). This re-dedicated
  NativeSoapClient::setEncoding() to change the charset's encoding, not the
  SOAP encoding and thus is a BC break! - (@kiesel, @thekid)
* Made members of SOAP implementations protected - this is a potential
  BC break - (@kiesel, @thekid)
* Changed util.Properties to no longer use parse_ini_file(), fixes the
  behavioral problems documented in issue #15. - (@thekid)
* Deprecated util.Properties::save(), fromString() and fromFile() in favor
  of store(OutputStream $out) and load(InputStream $in). - (@thekid)

### Bugfixes

* Added missing brackets in TestTaglet instantiation in TagletManager - (@ohinckel)
* Fixed fatal error in io.archive.zip.ZipFileEntry::toString() - (@thekid)
* Fixed issue #28: Bug in io.File::readLine() - (@Stormwind, @thekid, @kiesel)
* Fixed issue #32: Primitives in HashSet cause fatal error in toString() - (@thekid)
* Fixed issue #31: Floating point numbers used as keys in util.collections 
  classes HashSet, HashTable and LRUBuffer - (@thekid)
* Fixed issue #23: XMLScriptletURL adds port if not default port for
  given scheme - (@kiesel)
* Fixed issue #16: ScriptletRunner - graceful header handling - (@thekid, @ledermueller)
* Fixed mysqlx protocol implementation to handle timezones, timestamps 
  and numeric data types and correctly - (@thekid)
* Replaced parse_url() inside peer.URL by handcrafted implementation to
  work around PHP bug #54180 - (@thekid)
* Changed util.log.SyslogAppender to work with util.log.Logger as it sets
  custom parameters via public member access - (@treuter)
* Improved exception message in remote.UnknownRemoteObject, so the name
  of the unknown object is visible - (@kiesel, @thekid)
* Fixed unittest API not to allow overwriting TestCase's methods with 
  test methods (e.g. setUp(), tearDown(), getName(), ...) - (@thekid, @kiesel)
* Fixed segmentation fault when using mssql.so to get constraints. - (@thekid, @ledermueller)
* Fixed an fatal error when an "Object does not have any declarative 
  constraints" - (@thekid, @ledermueller)
* Fixed file name interpretation in LIST command in FTP API - (@ohinckel)

### Features

* Added quiet mode to unittest command - see issue #24 - (@thekid)
* Made peer.net.Inet6Address accept packed form - see issue #26 - (@thekid)
* Add toString() implementation to peer.Socket - (@thekid)
* Removed obsolete class webservices.soap.interop.Round2BaseClient which
  actually is a custom soap endpoint implementation - (@kiesel)
* Added peer.net API with support for IPv4/IPv6 address handling - (@kiesel)
* Added support for Unicode in .uni files (UTF-8, UTF-16LE/BE)- issue #20
  http://news.planet-xp.net/article/414/2011/06/12/ - (@thekid)
* Added text.TextTokenizer class which tokenizes off a io.streams.Reader  - (@thekid)
* Added charset() to io.streams.TextReader which returns charset in use - (@thekid)
* Added reset() method to io.streams.Reader which resets it to the start - (@thekid)
* Added PATCH verb to peer.http.HttpConstants - issue #21 - (@thekid)
* Added setParameter() method to peer.http.HttpRequest class - (@thekid)
* Migrated repository to github.com (for the records) - (@thekid, @iigorr, @kiesel, @invadersmustdie)
* Made "xar" command line utility exclude hidden version control systems 
  files other than just SVN and CVS (GIT/Mercurial/Bazaar/...) - (@thekid, @kiesel)
* Implement verbose parameter for xpcli runner - (@ohinckel)


## 5.8.1 / 2011-03-14

### Heads up!

* Changed all exceptions previously inheriting from lang.ChainedException
  to extend lang.XPException instead - (@thekid)
* Changed DriverManager to use userland MySQL in favor of ext/mysqlnd
  http://news.planet-xp.net/article/404/2011/01/16/ - (@thekid)

### RFCs

* Implemented RFC 0205 - All exceptions can be chained optionally - (@thekid)
  
### Bugfixes

* Fixed util.collections classes to work correctly when arrays or maps
  are used for keys - (@thekid)
* Fixed is() core functionality to not raise warnings when given non array 
  values for array and map type tests - (@thekid)
* Implemented extracting ZIP file entries after index iteration for 
  seekable streams - (@thekid)
* Changed FileInputStream and FileOutputStream classes to only open files
  if not already open - (@thekid)
* Implemented static method call verification in the compiler - (@thekid)
* When writing zip files, convert names from iso-8859-1 to CP437 - (@thekid)

### Features

* Added ability to run a single test with "unittest class.Name::method"
  http://news.planet-xp.net/article/409/2011/03/05/ - (@thekid)
* Extended command line reflection to also accept package names
  http://news.planet-xp.net/article/408/2011/02/26/ - (@thekid)
* Improved text.StringTokenizer and text.StreamTokenizer performance - (@thekid)
* Added support for multi-line strings inside .ini files - (@mihaighigea)
* Added userland MySQL implementation in case neither "mysql" nor 
  "mysqli" PHP extensions are loaded - (@thekid)
* Implemented creating and extracting password-protected ZIP files - (@thekid)
* Added new package "math" which contains BigNum, BigInt and BigFloat - (@thekid)
* Made io.archive.zip.ZipEntry implementations' constructors accept
  var args from which to compose a qualified name - (@thekid)
* Added optional level parameter to io.archive.zip.ZipFileEntry's 
  setCompression() method, defaulting to 6 - (@thekid)
* Added io.archive.zip.ZipArchiveWriter::usingUnicodeNames() method - (@thekid)
* Added io.archive.zip.ZipIterator class - (@thekid)
* Implemented reading central directory from zip archives in io.archive.zip - (@kiesel)
* Added function to set persistent headers to web test cases, which are sent with each
  request. - (@iigorr)
* Added function to follow redirects in web test cases and check http status codes and
  url base afterwards. - (@ledermueller, @iigorr)
* Enhanced the followRedirect() method of the unittest.web.WebTestCase class to
  handle the Refresh header too. - (@ledermueller)
* followRedirect() will now throw an exception if no redirect target is found - (@ledermueller)
* navigateTo() from WebTestCase class will now use query parameters as an array 
  instead of a string - (@ledermueller)

## 5.8.0 / 2011-01-11

### Heads up!

* Made cookie handling in unittest.web.WebTestCase non-persistent
  amongst test methods by changing the store from a static to a
  non-static variable - (@thekid, @kiesel)
* Added onBegin(), onError() and onFinish() methods to ParserCallback
  interface in xml.parser - (@thekid)
* Made unittest default and verbose output include the elapsed time
  http://news.planet-xp.net/article/398/2010/12/31/ - (@thekid)
* Changed util.log.ConsoleAppender to use util.cmd.Console class instead
  of directly writing to STDERR - (@thekid)
* Added keys() and values() methods to util.collections.Map - (@thekid)
* Set client charset in rdbms.sybase.SybaseConnection to iso_1 - (@thekid, @kiesel)
* Added check for correct configuration of timezone at bootstrap time
  http://news.planet-xp.net/article/389/2010/10/16/ - (@kiesel)
* Removed deprecated LOGGER_FLAG_* constants - (@thekid)
* Removed deprecated HTTP_* constants - (@thekid)
* Renamed io.dba package to edu.berkeley.dba and moved it to ports - (@thekid)
* Removed deprecated classes:
  * text.String (use lang.types.String instead)
  * lang.Collection (use util.collections.IList implementation instead)
  * text.PHPParser (superseded by text.doclet package)
  * text.PHPTokenizer (-"-)
  * text.PHPSyntaxHighlighter (-"-)
  * util.ChainedException (replaced by lang.ChainedException)
  * util.DateInterval (renamed to TimeInterval to avoid name clash)
  * util.cmd.Runner (moved to xp.command.Runner)
  * xml.XML (functionality moved to xml.Tree) - (@thekid)
* Deprecated ref() and deref() core functionality - (@thekid)

### RFCs

* Implemented RFC 0052 - Make XP its own (compiled) language - (@thekid)
* Implemented RFC 0189 - Extension methods - (@thekid)
* Implemented RFC 0181 - SystemExit - (@thekid)
* Implemented RFC 0194 - Add XPClass::getDeclared*() methods - (@thekid)
* Implemented RFC 0193 - Generics optimization - (@thekid)
* Implemented RFC 0197 - Type literals in tokens - (@thekid)
* Implemented RFC 0185 - ClassFormatException for malformed annotations - (@thekid)  

### Bugfixes

* Added support for array and hash keys (key[], key[name]) in ini files 
  to Properties::fromString() - parse_ini_file() already supports this. - (@thekid)
* Fixed xar command when files are passed with alternative names creating
  archives with all files named after the alternative name. - (@thekid)
* Fixed lang.Process::close() not to raise warnings when called twice - (@thekid)
* Fixed lang.Process::close() to raise a specialized exception if the 
  process was retrieved by getByProcessId() instead of a NullPointer - (@thekid)
* Fixed lang.System::tempDir() to check on TMPDIR and TEMPDIR environment
  variables besides TEMP and TMP and to check several traditional locations
  when none of these are set - (@thekid)
* Fixed bug #43 - peer.mail.store.Pop3Store connection error - (@thekid)
* Fixed bug #45 - Setting Properties on a LogAppender fails - (@thekid)
* Fixed webservices.json.JsonDecoder when decoding overly long strings - (@thekid, @mihaighigea)
* Fixed possible fatal error in lang.ChainedException common stack trace
  elements' calculation - (@thekid)
* Fixed lang.types.String::getBytes() to throw an exception instead of
  cutting off the returned bytes at the position of an encoding error - (@thekid)
* Fixed absolute path calculation in io.Folder - (@thekid)
* Fixed URL parsing of parameters containing square brackets or dots in
  the peer.URL class by replacing parse_str() by a handcrafted parser - (@thekid, @mrosoiu)
* Changed img.fonts.TrueTypeFont's constructor to throw an exception if
  the font cannot be found - (@thekid)
* Fixed Runtime::memoryUsage() and Runtime::peakMemoryUsage() - (@thekid)
* Fixed generics declaration parsing in create() and is() - (@thekid)

### Features

* Added support to write Strings and Characters to io.stream.TextWriter - (@thekid)
* Added withBom() method to io.stream.TextWriter to write the respective 
  BOM depending on the character set (utf-16be, utf-16le, utf-8) - (@thekid)
* Added casting support to xml.meta.Marshaller and xml.meta.Unmarshaller - (@thekid)
* Added injection support to xml.meta.Marshaller and xml.meta.Unmarshaller - (@thekid)
* Added ability to select unmarshalling target class based on root
  tag to xml.meta.Unmarshaller - (@thekid)
* Added fluent interface peer.http.FormRequestData::withPart()  - (@thekid)
* Added unittest.TestResult::elapsed() method - (@thekid)
* Added setProcessor(int, CellProcessor) and withProcessor() equivalent
  to text.csv.CsvWriter and text.csv.CsvReader - (@thekid)
* Added lang.RuntimeOptions::withClassPath() method - (@thekid)
* Ports: Added support to Google custom search for Spelling suggestions  - 
  ("Did you mean"), Synonyms ("You could also try") and Key matches  - (~ Adwords) - (@thekid)
* Ports: Allowed passing arbitrary parameters in Google custom search
  implementatiom, com.google.search.custom.GoogleSearchClient::searchFor() - (@thekid)
* Added setXSLDoc(), setXMLDoc() to xml.DomXSLProcessor - (@kiesel)
* Implemented equals() method in xml.Node - (@thekid)
* Changed InputStream, OutputStream, Reader and StreamTransfer in 
  io.streams as well as their implementations to be closeable - (@thekid)
* Changed rdbms.ResultSet and all its implementations to be closeable - (@thekid)
* Implemented ARM blocks in XP language, supported by lang.Closeable 
  interface in framework.
  http://news.planet-xp.net/article/397/2010/12/31/ - (@thekid)
* Added text.regex.Pattern::replaceWith() method - (@thekid)
* Changed text.regex.Pattern not to throw exceptions on malformed 
  patterns like Pattern::compile(), and instead defer pattern validation 
  until actually matching the pattern - performance improvement - (@thekid)
* Made io.File::copy() and io.File::move() accept io.File and io.Folder 
  instances in addition to strings. - (@thekid)
* Added values() method to util.collections.HashTable - (@thekid)
* Implemented automatic encoding detection in io.stream.TextReader when
  NULL was given as encoding, based on BOM of stream - (@thekid, @kiesel)
* Added support for multiple header values to peer.http.HttpRequest - (@thekid)
* Implemented fluent interface for rdbms.finder API - (@thekid)
* Added ability to define regular expressions in @expect(withMessage)
  by using a forward slash as its first character.
  http://news.xp-framework.net/article/378/2010/09/18/ - (@thekid)
* Added lang.ArrayType and lang.MapType classes to represent zero-based
  indexed lists of elements and key/value-pairs, respectively
  http://news.xp-framework.net/article/376/2010/09/18/ - (@thekid)
* Added indexer access functionality, this(): this(array(1, 2, 3), 1)= 2
  http://news.xp-framework.net/article/379/2010/09/22/ - (@thekid)
* Enabled protected member access via reflection
  http://news.xp-framework.net/article/351/2010/04/04/ - (@thekid)
* Changed command line reflection ("xp -r") to display generic definitions - (@thekid)
* Extended is() core functionality to accept primitive and array types - (@thekid)
* Added isInstance() method to lang.Type and lang.Primitive classes - (@thekid)
* Added typeof() core functionality - (@thekid)


## 5.7.13 / 2011-01-11

### Heads up!

* Changed util.log.ConsoleAppender to use util.cmd.Console class instead
  of directly writing to STDERR - (@thekid)

### Bugfixes

* Fixed xar command when files are passed with alternative names creating
  archives with all files named after the alternative name. - (@thekid)
* Fixed lang.Process::close() not to raise warnings when called twice - (@thekid)
* Fixed lang.Process::close() to raise a specialized exception if the 
  process was retrieved by getByProcessId() instead of a NullPointer - (@thekid)
* Fixed bug #43 - peer.mail.store.Pop3Store connection error - (@thekid)
* Fixed bug #45 - Setting Properties on a LogAppender fails - (@thekid)
* Fixed webservices.json.JsonDecoder when decoding overly long strings - (@thekid, @mihaighigea)

### Features

* Added xml.parser.TreeInputSource class - (@thekid)
* Added casting support to xml.meta.Marshaller and xml.meta.Unmarshaller - (@thekid)
* Added injection support to xml.meta.Marshaller and xml.meta.Unmarshaller - (@thekid)
* Added ability to select unmarshalling target class based on root
  tag to xml.meta.Unmarshaller - (@thekid)
* Added fluent interface peer.http.FormRequestData::withPart()  - (@thekid)
* Added lang.RuntimeOptions::withClassPath() method - (@thekid)
* Ports: Added support to Google custom search for Spelling suggestions  - ("Did 
  you mean"), Synonyms ("You could also try") and Key matches  - (~ Adwords) - (@thekid)
* Ports: Allowed passing arbitrary parameters in Google custom search
  implementatiom, com.google.search.custom.GoogleSearchClient::searchFor() - (@thekid)


## 5.7.12 / 2010-11-17

### Heads up!

* Set client charset in rdbms.sybase.SybaseConnection to iso_1 - (@thekid, @kiesel)

### Bugfixes

* Fixed lang.types.String::getBytes() to throw an exception instead of
  cutting off the returned bytes at the position of an encoding error - (@thekid)
* Fixed absolute path calculation in io.Folder - (@thekid)
* Fixed possible fatal error in lang.ChainedException common stack trace
  elements' calculation - (@thekid)
* Changed img.fonts.TrueTypeFont's constructor to throw an exception if
  the font cannot be found - (@thekid)
* Fixed Runtime::memoryUsage() and Runtime::peakMemoryUsage() - (@thekid)

### Features

* Made io.File::copy() and io.File::move() accept io.File and io.Folder 
  instances in addition to strings. - (@thekid)
* Changed text.regex.Pattern not to throw exceptions on malformed 
  patterns like Pattern::compile(), and instead defer pattern validation 
  until actually matching the pattern - performance improvement - (@thekid)
* Added text.regex.Pattern::replaceWith() method - (@thekid)
* Added values() method to util.collections.HashTable - (@thekid)
* Implemented equals() method in xml.Node - (@thekid)
* Added support for multiple cookies in unittest.web.WebTestCase - (@kiesel, stahlberg)


## 5.7.11 / 2010-09-14

### Heads up!

* Deprecated peer.mail.Message::getRecipient() - the iterative use of
  fetching the recipients with "while" in favor of getRecipients() - (@thekid)
* Deprecated getMessage() and getStatusCode() in peer.http.HttpResponse 
  in favor of message() and statusCode(). - (@thekid, @Gabriel Mihai Ghigea)
* Deprecated getHeaders() and getHeader() in peer.http.HttpResponse and
  replaced by headers() and header(), respectively. The new methods have
  changed return types to be able to support multiple headers - (@thekid, @mihaighigea)

### RFCs

* Implemented RFC 0093 - xml.meta - (@thekid)

### Bugfixes

* Fixed errors when raising ClassLinkageExceptions in the classloader - (@thekid)
* Fixed peer.Socket::canRead() on Windows when using PHP's VC9 binaries 
  http://news.xp-framework.net/article/368/2010/08/23/ - (@thekid)
* Fixed command line parsing on Windows when parsing triple quotes - (@thekid)
* Fixed peer.mail.SmtpTransport not sending an "RCPT TO:" after the 
  first message passed to send() - (@thekid)
* Fixed util.collections classes' equals() implementations for generic
  versions (a Vector of Strings should never equal a Vector of Objects,
  although a String is an Object). - (@thekid)
* Fixed modifiers to text.regex.Pattern::compile() not being recognized - (@thekid)
* Fixed unbuffered queries using datasets using rdbms.Statement instances
  as criteria not actually being unbuffered - (@thekid)
* Fixed peer.http.HttpInputStream to no longer read beyond EOF in certain
  situations - (@thekid)
* Fixed peer.SocketInputStream to return 0 from available() on EOF - (@thekid)
* Fixed string quotation and comment handling in Properties::fromString() - (@ohinckel)

### Features

* Changed ClassNotFoundException's message to contain the cause's message - (@thekid, @kiesel)
* Ports: Added com.google.search.custom package (Google Custom Search
  implementation, for use e.g. with the GSA) - (@thekid)
* Added possibility to explicitely pass SSL version to peer.http APIs
  by using "https+v2://" or "https+v3://" in the URL's scheme - (@thekid, @mihaighigea)
* Added XSLStringCallback::wordwrap() to wrap long texts at given column - (@ohinckel, toennishoff)


## 5.7.10 / 2010-07-04

### Heads up!

* Changed unittests with @expect to fail if the expected exception is 
  caught but a warning is raised - (@thekid, @kiesel, @iigorr)
* Changed URL to service mapping in web runners - a mapping on the URL 
  /service will no longer catch the URL /services (note the trailing "s"),
  while it will catch any of /service, /service/json or /service/ws/soap.
  http://news.xp-framework.net/article/355/2010/06/10/ - (@thekid)

### RFCs

* Implemented RFC 0203 - Unbuffered queries - (@thekid)

### Bugfixes

* Fixed webservices.json.JsonDecoder not to convert integers on stream
  larger than LONG_MAX to LONG_MAX (or LONG_MIN, respectively) - (@thekid, @treuter, @ledermeller)
* Fixed util.Date's constructor not to throw exceptions if a previous 
  call to it lead to an error in PHP versions before 5.2.6. - (@thekid, @mihaighigea)
* Changed xml.XPath to throw an exception inside the constructor instead
  of later on when given invalid XML - (@thekid, @iigorr)
* Fixed bug to always use forward slashes as directory separator in
  cgen's wsdl subcommand - (@kiesel)
* Changed HttpSession to regenerate session ID before starting a session
  in order to prevent duplicate session creation - (@thekid)
* Ports: Fixed protocol messup when reading messages without content length 
  from STOMP endpoints. - (@thekid)
* Removed unused member field xml.Tree::$dataSource - (@ohinckel)

### Features

* webservices.rpc.AbstractRpcRouter now logs all exceptions transmitted
  to the client (for SOAP, XMLRPC, JSON) - (@kiesel)
* Created string representations for xml.Tree, xml.Node, xml.PCData and 
  xml.CData instances - (@thekid)
* Added xml.Tree::withRoot() method for use in fluent interfaces
  http://news.xp-framework.net/article/359/2010/06/22/ - (@thekid)
* Added support for MySQL connectivity through PHP's mysqli extension if 
  the mysql extension is not available - (@thekid)
* Added support for Sybase connectivity through PHP's mssql extension if 
  the sybase_ct extension is not available - (@thekid)
* Added support for alternative syntax in web.ini's [app] section - (@kiesel)
* Ports: Added support for selectors to StompConnection::subscribe() - (@thekid)
* Ports: Added optional headers parameter to StompConnection::send() - (@thekid)
* Added support for rewriting {PROFILE} literals in web.ini - (@kiesel)


## 5.7.9 / 2010-05-05

### Heads up!

* Ports: Deprecated com.google.soap.search package, it's been discontinued 
  by Google - (@thekid)

### RFCs

* Implemented RFC 0201 - XP Overlays - (@thekid)

### Bugfixes

* Fixed lang.reflect.Proxy class to not produce fatal errors when passed
  an empty array of interfaces to implement - (@thekid)
* Fixed common stack trace element calculation in lang.ChainedException
  if chained exception is a chained exception itself. - (@thekid)
* Fixed peer.ftp.FtpConnection to be able to cope with FTP servers
  sending an empty directory listing (without "." and "..") for 
  nonexistant directories - (@thekid)
* Fixed format string BIT_AND operator to only use one placeholder - (@ohinckel)

### Features

* Allowed changing contenttype to use others than multipart/mixed in
  peer.mail.MimeMessage - (@gelli)
* Refactored class loader implementations and extracted common code in
  all three implementations into an abstract base class - (@thekid)
* Ports: Added package org.codehaus.stomp - (@kiesel)
* Introduced new class loading exceptions, lang.ClassDependencyException 
  and lang.ClassLinkageException which inherit the existing 
  lang.ClassNotFoundException class. - (@kiesel)
* Made exception messages raised from injection more verbose in XPCLIs - (@thekid)
* Added support to peer.Socket::canRead() to block forever when NULL
  is passed as value for the timeout parameter. - (@thekid)
* Changed unittest.web.WebTestCase to load HTML from a stream instead
  of buffering it all into memory first - (@thekid)
* Log exceptions caught by web runner to scriptlet log - (@ohinckel)
  

## 5.7.8 / 2010-03-24

### Heads up!

* Changed xpcli runner to display exceptions raised in run() block of  
  the run command class including stacktrace - (@thekid)
* Deprecated lang.Primitive::$INTEGER in favor of lang.Primitive::$INT - (@thekid)
* Deprecated lang.Type::$ANY in favor of lang.Type::$VAR - (@thekid)
* Changed util.collections.Map interface to ensure array access works.
  Any implementation must now adhere to http://de3.php.net/arrayaccess - (@thekid)

### RFCs

* Implemented RFC 0178 - XP installations (runner part) - (@thekid)
* Implemented RFC 0183 - lang.ResourceProvider - (@kiesel)

### Bugfixes

* Fixed rdbms.StatementFormatter handling of booleans in %d token - (@thekid)
* Fixed org.nagios.NscaClient's handling of CRC32 checksums
  and thus fixes passive nagios checks since security.checksum
  refactoring - (@kiesel)
* Cleaned up various apidoc spelling issues in @param / @return, and
  exchanged "mixed" with "var" throughout the entire repository. - (@thekid)
* Fixed lang.XPClass raising fatals when methods inherited / implemented
  from builtin classes - (@thekid)
* Fixed reflection on lang.Throwable class in certain cases when builtin
  Exception class is queried for non-existant details - (@thekid)
* Fixed Wrapper::load() not executing casters and checkers when array
  values were submitted and first value is empty - (@ohinckel, @kusnier)
* Fixed peer.URL::getQuery() to build query string correctly when using
  multi-dimensional arrays in query string - (@ohinckel)

### Features

* Implemented HTTPOnly support in scriptlet.Cookie - (@thekid)
* Made unittest.TestCase::assertInstanceOf() accept lang.Type instances
  as first parameter as well as strings with type names - (@thekid)
* Added new scriptlet.DirectOutputHttpScriptletResponse class - (@kiesel)
* Added "-r" command line switch to xp runner
  http://news.xp-framework.net/article/345/2010/02/16/ - (@thekid)
* Enabled discovery of port used in bind() when setting to port to 0 
  which indicates to use any free port - in peer.ServerSocket
  http://news.xp-framework.net/article/343/2010/02/01/  - (@thekid)


## 5.7.7 / 2010-01-26

### Heads up!

* Deprecated unittest.TestCase::assertClass() and assertSubclass() 
  replaced by new method assertInstanceOf(). - (@thekid)
* Changed FTP_ASCII / FTP_BINARY constants in peer.ftp package to class
  constants FtpTransfer::ASCII and FtpTransfer::BINARY.
  http://news.xp-framework.net/article/334/2009/12/28/ - (@thekid)

### Features

* Added getOptions() and getSelectedOptions() to unittest.web.SelectField  - (@thekid)
* Added support for cookies to web test API - (@thekid)
* Added new security.checksum.MessageDigest API supporting incrementally
  creating checksums (md5, sha1, crc32, whirlpool, adler32 and more) - (@thekid)
* Added new lang.Runtime::newInstance() method - (@thekid)
* Added member "in" to command line classes to read from standard input - (@thekid, srogin)
* Added compressing input and decompressing output streams to io.streams:
  deflate / inflate, gzcompress / gzdecompress, bz2compress / bz2decompress
  http://news.xp-framework.net/article/335/2010/01/06/ - (@thekid)
* Added io.streams.StreamTransfer class to copy one stream to another - (@thekid)
* Improved performance of reflection on class members (fields, methods) - (@thekid)
* Changed util.log.Logger::configure() to refrain from using global
  and deprecated LOGGER_FLAG_* constants (soon to be removed). - (@thekid)
* Added util.log.LogCategory::getAppenders() to make adding appenders
  more testable - (@thekid)
* Always include current URL in web testcase failure messages - (@thekid)
* Added new methods to unittest.web.WebTestCase: assertStatusIn(),
  assertTextPatternNotPresent() and assertTextPatternPresent() - (@thekid)
* Added "-a" commandline option to unittest command - (@thekid)
* Added text.regex.MatchResult::$EMPTY static member holding an empty
  match - can be used for MatchResult::$EMPTY.equals(...) e.g. - (@thekid)
* Added withCallback() and withEncoding() methods to XMLParser class
  http://news.xp-framework.net/article/331/2009/12/17/ - (@thekid) 

### RFCs

* Implemented RFC 0175 - ZIP File support - (@thekid)

### Bugfixes

* Fixed core unittests to correctly recognize "Darwin" (Mac OS X) not
  as Windows but as a Un*x system
  http://news.xp-framework.net/article/340/2010/01/24/ - (@thekid, @gelli)
* Fixed incorrect EOF detection in peer.Socket class - (@thekid)
* Fixed web test API always returning UTF-8 encoded strings - (@thekid)
* Fixed peer.http API not to choke on consecutive requests if data isn't
  completely read - (@thekid)
* Fixed XP runners not terminating PHP runtime on Windows
  http://news.xp-framework.net/article/338/2010/01/15 - (@thekid)
* Fixed XP runners on BSD systems not being able to parse command line
  when procfs is not mounted
  http://news.xp-framework.net/article/337/2010/01/10 - (@thekid)
* Fixed adding a testcase class twice (or a test twice) to a TestSuite
  would result in the tests (or the test, respectively) only being run 
  once - especially makes sense with tests with different arguments. - (@thekid)
* Fixed lang.reflect.Routine methods dealing with class details when
  working with inherited interface methods - (@thekid)
* Fixed unittest.web.WebTestCase::navigateTo() not handling the request
  method but instead always using HTTP GET - (@thekid)
* Fixed unittest.web.WebTestCase::assertTextPresent() method to check
  for substring matches instead of for text nodes with exact string - (@thekid)
* Reset EOF status in peer.Socket::close() to get correct EOF status
  after reconnecting - (@ohinckel, @Christine Karch, @kiesel)


## 5.7.6 / 2009-12-16

### Heads up!

* Changed io.File to no longer to accept filenames containing a NUL byte - (@thekid)
* Changed io.File to accept only scheme URLs beginning with xar:// as well 
  as php://stderr, php://stdout and php://stdin. - (@thekid)
* Made scriptlet.HttpScriptlet use a merge of $_GET and $_POST,
  instead of $_REQUEST, effectively not taking cookie values
  into account when using HttpScriptletRequest::getParam(). - (@thekid, @kiesel)

### Features

* Added lang.types.ArrayList::contains() method  - (@thekid)
* Made WorkflowScriptletRequest give a less information-leaking error message - (@thekid, @kiesel)
* Made ClassNotFoundException constructor take an optional, second argument
  to separate information on available loaders from actual error message - (@kiesel, @thekid)
* Added util.Currency and util.Money classes - (@thekid)
* Made scriptlet.HttpScriplet check for existance of X-Forwarded-Host
  HTTP-header when creating Location:-headers - (@thekid, @kiesel)
* Made scriptlet.HttpScriptlet support all HTTP methods ("verbs") by
  default - (@kiesel)
* Added new text.DateFormat class - (@thekid)
* Added with*() methods complementing the setter to img.util.ExifData 
  and img.util.IptcData to allow for fluent usage - (@thekid)
* XPCLI: Check for injection type not only in `@inject(type= ...)` and in
  in the type restriction but also in the documented type (in this order) - (@thekid, @kiesel)

### RFCs

* Implemented RFC 0192 - Logging API enhancements - (@thekid)
* Implemented RFC 0191 - New CSV API - (@thekid)

### Bugfixes

* Fixed fatals in peer.server.PreforkingServer if logging was disabled - (@gelli)
* Fixed exception message for @args methods in xp.command.Runner - (@thekid, patzer)
* Fixed memory leak caused by assigning $this as member of $this - (@thekid, @kiesel, roth)
* Fixed bug on uncaught exceptions in @beforeClass-method in
  unittest-API - (@thekid)
* Ensured that injection occurs before argument passing in
  util.cmd.Command classes - (@thekid)
* Made scriptlet.xml.workflow.casters.ToDateCaster explicitely check for
  errors and warnings when parsing a date and reject that value in case - (@kiesel, manta)
* Fixed WSDL code generation for functions with no arguments - (@ohinckel, karch)


## 5.7.5 / 2009-10-22

### Heads up!

* Refactored command line parsing and composition from lang.Process to
  new lang.CommandLine class - (@thekid)
* Made peer.URL throw a lang.FormatException when passed malformed URLs - (@thekid)
* Changed peer.URL::addParams() and peer.URL::addParam() to throw a
  lang.IllegalArgumentException if the parameter to be added already
  exists - (@thekid)
* Refactored scriptlet API classes to make them testable - (@thekid)

### Features

* Added new rdbms.finder.GenericFinder class - (@thekid)
* Added support for custom error pages to the web runner - (@kiesel)
* Added setLocation() method to webservices.soap.native.NativeSoapClient
  to overwrite endpoint location if needed. - (@gelli)
* Added peer.http.FormRequestData and peer.http.FormData as an implementation
  of multipart/form-data http requests - (@kiesel)
* Added unittest.XmlTestListener class which reports results in an XML 
  format adequate for continuous integration systems.
  http://news.xp-framework.net/article/320/2009/10/02/ - (@thekid)
* Added peer.URL::removeParam() method - (@thekid)
* Added support for array parameters (num[]=one&num[]=two) and hash
  parameters (data[size]=S&data[color]=green) to peer.URL  - (@thekid, @Patrick Kohlmann)
* Added peer.URL::hasParam() method to test for existance of a single
  parameter - it complements hasParams() - (@thekid)
* Added peer.URL::setParams() and peer.URL::setParam() methods - (@thekid)
* Added xml.Stylesheet::addTemplate() (and withTemplate()) which work 
  with xml.XslTemplate instances. - (@thekid)
* Added xml.Node::withChild() method to complete addChild() - but instead
  of returning the child returns the node itself.
  http://news.xp-framework.net/article/318/2009/09/21/ - (@thekid)
* Added various with*() methods to xml.Stylesheet to complement their
  add*() counterparts - but instead return the stylesheet instance to
  allow for a fluent API. - (@thekid)
* Changed scriptlet error pages to use data protocol instead of relying
  on an image named "err.gif" in the document root's /image subdirectory - (@thekid)
* Added rdbms.finder.FinderMethod::getFinder() method - (@thekid)
* Added getInputStream() and getOutputStream() methods to io.File - (@thekid)
* Added getInputStream() and getOutputStream() methods to peer.Socket - (@thekid)
* Added possibility to pass an associative array to HTTP request params - (@thekid)

### RFCs

* Implemented RFC 0190 - Readers and Writers - (@thekid)
* Implemented RFC 0188 - Test outcome - (@thekid)
* Implemented RFC 0187 - @expect withMessage - (@thekid)

### Bugfixes

* Fixed remote.protocol.Serializer to apply mappings for interfaces - (@srogin, @gelli)
* Fix util.TimeZoneTransition::previous() implementation.
  Fixes bug #41 - (@kiesel, wulsch)
* Fixed lang.Enum::membersOf() to return correct list of members for
  abstract enums - (@thekid)
* Updated org.bugzilla.gateway.BugMessage to use "=" instead of ":" as
  assignment character for tokens (required for Bugzilla V3) - (@ohinckel)
* Fixed content type header in peer.mail.MimeMessage with multiple parts - (@ohinckel)
* Fixed extraneous trailing whitespace in rdbms.mysql.MySQLDBAdapter - (@Tobias Roth)
* Fixed peer.URL::getQuery() and peer.URL::hashCode() not being returning
  the correct values after the URL object has been modified - (@thekid)
* Fixed newline detection in StreamReader::readLine() - (@ohinckel)
* Fixed XPath constructor to include XML declaration when creating XPath
  object from xml.Tree - (@ohinckel, @Christine Karch)


## 5.7.4 / 2009-09-05

### Heads up!

* Deprecated peer.ftp.server.FtpConnectionListener class
  http://news.xp-framework.net/article/309/2009/08/19/ - (@thekid)
* Refactored rdbms.pgsql.PostgreSQLConnection class so it behaves in 
  line with other rdbms.DBConnection classes - this includes 
  behavorial changes visible to outside - (@kiesel)
* Made util.Locale fully qualified, to make XP forward compatible
  with PHP 5.3.0 with ext/intl enabled - (@kiesel)
* Changed "unittest" command to exit with non-zero exitcode if either no
  tests are found or any test fails - (@thekid)
* Changed ClassDoc::sourceFile() and PackageDoc::sourceFile() in 
  text.doclet to return io.File objects instead of strings - (@thekid)
* Refactored scriptlet.workflow.casters.ToValidXMLString into the
  checker scriptlet.workflow.checkers.WellformedXMLChecker - (@kiesel)

### Features

* Added initial support for web runner
  http://news.xp-framework.net/article/296/2009/08/11/ - (@kiesel, @thekid)
* Refactored DBConnection classes; streamline code and remove leftovers
  from PHP4 times - (@kiesel)
* Added optional parameter "file" to xp::gc() which will only clean up
  errors caused inside the given file (versus all errors) - (@thekid, @kiesel)
* Added memory reporting methods to lang.Runtime class - (@thekid)
* Made timeouts during socket reads detectable by specialized exception
  peer.SocketTimeoutException
  http://news.xp-framework.net/article/308/2009/08/11 - (@thekid, @Rene Patzer)
* Added Console input stream (Console::$in) - (@thekid)
* Added lang.Runtime::bootstrapScript() method which returns the bootstrap
  script (e.g. "tools/class.php" or "tools/xar.php") - (@thekid)
* Added lang.Runtime::mainClass() method which returns the entry point
  class (e.g. "tools.unittest.Runner") - (@thekid)
* Added lang.Runtime::startupOptions() method which returns the options
  given to the runtime on the command line (e.g. "-dinclude_path=...", 
  "-q" or "-denable_dl=Off") - (@thekid)
* Added lang.Process::getArguments() which returns command line arguments
  passed to the process as an array of strings. - (@thekid)
* Support data type "varbinary" in MySQLDBAdapter class - (@ohinckel)
* Made XML_ILLEGAL_CHARS a xml.Node class constant - (@kiesel)
* Added accessors for underlying output stream to io.streams.StringWriter
  http://news.xp-framework.net/article/306/2009/07/12/ - (@thekid)
* Made rdbms.ConnectionManager initialize rdbms.DBConnection lazily, so
  that configuring with unsupported protocols only ends in exceptions
  when that connection is actually requested. - (@kiesel, @thekid)
* Added Microsoft SQL server support to rdbms API (depending either on 
  "mssql" or "sqlsrv" PHP extensions). - (@thekid)
* Added InterBase/Firebird support to rdbms API (depending on "ibase"
  PHP extension). - (@thekid)
* Extended error handling mechanism to provide class and method information
  for E_NOTICE and E_WARNING stacktrace elements. - (@kiesel)
* Made only those access implementations available in rdbms.DriverManager 
  where the correct driver is loaded 
  http://news.xp-framework.net/article/303/2009/06/09 - (@thekid)

### RFCs

* Implemented RFC 0159 - Deadlock handling
  http://news.xp-framework.net/article/304/2009/06/16/ - (@thekid)

### Bugfixes

* Fixed util.TimeSpan to handle "%%" correctly in its format() method - (@thekid)
* Prevented creation of negative timespans in util.TimeSpan::subtract() - (@thekid)
* Fixed valuesOf(), membersOf() and valueOf() methods in lang.Enum not
  to return static members other than those of the enum declaration. - (@thekid)
* Fixed bug #38 - Annotation parsing fails for strings containing "="  - (@thekid)
* Removed superfluous newline from message string in compoundMessage()
  of HttpScriptletException class - (@ohinckel)
* Fixed xml.dom.Document to work as advertised (getElementById() and
  getElementsByName() methods). - (@Rene Patzer)
* Fixed xp.codegen.esdl.Generator so it does work now - (wagner)
* Fixed util.log.SyslogAppender which never logged anything due to a 
  broken syslog() call. - (@thekid)
* Added missing dependency lang.Primitive to lang.Type - (@ohinckel)
* Fixed lang.archive.ArchiveClassLoader::packageContents() to work for 
  the base package - (wagner)
* Fixed lang.reflect.Package::getPackageNames() to work for the base 
  package - (wagner)
* Fixed lang.Process to ignore directories when looking up the executable - (@ohinckel)
* Fixed rdbms.mysql.MySQLDBAdapter class to recognize longtext and 
  tinyblob fields - (@Tobias Roth)
* Fixed img.util.ExifData class to no longer set date to "1999-12-31" 
  when EXIF data does not contain a date - (@thekid)
  

## 5.7.3 / 2009-06-08

### Heads up!

* Changed text.doclet.Doclet::start() to be abstract - (@thekid)
* Removed webservices.soap.transport.SOAPHTTPSTransport - the transport
  class SOAPHTTPTransport can handle this already - (@ohinckel)
* Replaced all base RPC webservice transport classes with newly created
  scriptlet.rpc.transport.AbstractRpcTransport class - (@ohinckel)
* Rewrote FTP API to no longer depend on ext/ftp
  http://news.xp-framework.net/article/298/2009/05/17/ - (@thekid)
* Made scriptlet.xml.workflow.checkers.ParamChecker and 
  scriptlet.xml.workflow.casters.ParamCaster abstract. - (@kiesel)
* Deprecated global constants in scriptlet.xml.XMLScriptletResponse
  in favor of class constants - (@kiesel)
* Deprecated HTTP_* constants, use peer.http.HttpConstants class 
  constants instead. - (@kiesel)
* Removed net.xp_framework.website from ports (now resides in xpadmin
  repository, web/src) - (@thekid)
* Modified package mapping to also work from client to server in 
  remote.protocol.Serializer - (@gelli)

### Bugfixes

* Fixed JSONDecoder unserialization for objects having __wakeup() method
  declared (e.g. util.Date) - (@ohinckel)
* Made JSONClient work again by using JsonRequestMessage::create() instead
  of JsonMessage::createCall() function when invoking RPC - (@ohinckel)
* Fixed GenericHttpTransport (which is not JsonRpcHttpTransport) to work
  with "new" HttpConnection and HttpRequest object (see RFC 0162) - (@ohinckel)
* Fixed image tags parsing in text.doclet.markup API for multiple 
  adjacent images - (@thekid)
* Fixed lang.reflect.Constructor::newInstance() check on instantiating
  an abstract class - (@kiesel, @thekid)
* Fixed webservices.soap.xp.XPSoapNode class to not fatal when
  serializing stdClass objects. - (@kiesel)
* Fixed com.microsoft.com.COMObject class to work with PHP5 COM - (@thekid)
* Fixed lang.XPClass::getSimpleName() cutting off first character when 
  class name is unqualified - (@thekid)
* Fixed class names for soap generator - (wagner)
* Fixed endless recursion in text.util.RandomCodeGenerator class - (@kiesel, @Tom Geiger)
* Fixed text.util.RandomCodeGenerator not being able to generate codes
  longer than 44 characters - (@thekid)
* Added fix to catch submission of malicious request data in 
  scriptlet.xml.XMLScriptletResponse. - (@kiesel)
* Fixed typo in security.crypto.PrivateKey class - (@Thomas Geiger, @Tobias Roth, @kiesel)

### Features

* Extended doclet command to be able to work with .xar files
  http://news.xp-framework.net/article/300/2009/06/07/ - (@thekid)
* Added usage to the doclet command. - (@thekid)
* Added support for package names as arguments to doclets, where lang.*
  will expand to all classes inside the lang package, and lang.** to all
  classes inside lang and all subpackages - (@thekid)
* Changed text.doclet.TagletManager to process test tags via specialized 
  taglet - (@thekid)
* Added support for attributes to custom markup processors in 
  text.doclet.markup API - (@thekid)
* Added text.spelling package and SpellChecker class based on ext/pspell
  http://news.xp-framework.net/article/297/2009/05/17/ - (@thekid)
* Changed io.Folder constructor signature to (string|Folder [, string*]) - (@thekid)
* Added a new soap type caster webservices.soap.types.SOAPDecimal - (wagner)
* Added a new Caster that removes illegal control characters from input 
  strings scriptlet.workflow.casters.ToValidXMLString - (toennishoff)
* made lang.XPClass support reflection for class constants - (@kiesel)
* org.bugzilla.gateway.BugMessage class now inhertits methods from
  peer.mail.MimeMessage class instead of peer.mail.Message class to
  support file attachments - (toennishoff)
* Added a setBody() method to peer.mail.MimeMessage class to add more 
  compatibility to peer.mail.Message and to support older bedavior of
  org.bugzilla.gateway.BugMessage class - (toennishoff)


## 5.7.2 / 2009-03-27

### Heads up!

* Removed xp-net.xp_framework-{VERSION}.xar from boot classpath. It is
  no longer required for runners, that's all in xp-tools-${VERSION}.xar.
  See http://news.xp-framework.net/article/290/2009/03/27/ - (@thekid)

### RFCs

* Implemented RFC 0087 - Move class details information to ClassLoader - (@thekid)
* Implemented RFC 0174 - io.collections interface additions / io.streams 
  integration - (@thekid)
* Implemented RFC 0167 - RDBMS and Timezones - (@gelli)

### Features

* Added lang.Primitive::wrapperClass() method - (@thekid)
* Included type information in unittest.AssertionFailedError's message
  See http://news.xp-framework.net/article/291/2009/03/28/ - (@thekid)
* Included prerequisites in unittest.PrerequisitesNotMetError's message - (@thekid)
* Added lang.reflect.Package::getSimpleName() which complements 
  lang.XPClass::getSimpleName() - (@thekid)
* Added util.log.StreamAppender to use streams as log appender - (@ohinckel)
* Added io.streams.ChannelInputStream and io.streams.ChannelOutputStream 
  classes that read from / write to stdin, stdout and stderr - (@thekid)
* Made peer.ftp.FtpConnection constructor accept not only strings but also
  peer.URL objects - same as in peer.http.HttpConnection - (@thekid)
* Implemented feature suggested in bug #31 - Allow Numbers in Scriptlet
  States - (@thekid)
* Added io.collections.ArchiveCollection class to work with XARs - (@thekid)
* Added support for enums to the Remote API (EASC)
  See http://news.xp-framework.net/article/287/2009/01/28/ - (@ohinckel, @thekid)
* Made lang.XPClass::isSubclassOf() accept not only a string with the 
  classname but also XPClass objects - (@thekid)

### Bugfixes

* Fixed XMLScriptletResponse::forwardTo() function to clear query and
  fraction value when not specified - (@kiesel)
* Fixed xp::stringOf() to not incorrectly detect recursion with objects 
  returning hashcodes which are very large numbers - (@thekid)
* Fixed dataset generator to use prefixed XML file names when accessing
  referenced tables for foreign keys - (@gelli, @ohinckel)
* Fixed WSDL code generator: replace "." with "_" and upper case first
  letter in generated class names - (wagner, @ohinckel)
* Fixed org.fpdf.FPDF::writeCells() when writing justified cells - (@thekid, Tobias Roth, @Thomas Geiger)
* Use "etc" directory as default configuration path for xpcli command - (@ohinckel)
* Fixed rdbms.mysql.MySQLDBAdapter did not handle '"' quotes (bug #34) - (wagner)
* Fixed util.Date::hashCode() returns timestamp now instead of object id. - (wagner)
* Made /proc lookup inside lang.Process::getProcessById() work on 
  FreeBSD 7 - the link is /proc/$PID/file instead of /proc/$PID/exe - (@thekid, wagner)
* Fixed bug #33 - Parse error for comments having a line ending with "]"  - (@thekid)
* Fixed io.File::write() and io.File::writeLine() to throw an IOException
  if the underlying file handle has been closed - (@thekid)
* Fixed io.streams.FileInputStream and io.streams.FileOutputStream not 
  to close underlying file when they get garbage-collected. - (@thekid)
* Fixed remote interface mapping when creating the remote interface in the
  remote API - (@ohinckel)
* Fixed util.Locale::setDefault() fatalling when the given locale does
  not exist (and while trying to throw an exception). - (@thekid)
* Fixed scriptlet.xml.XMLScriptletRequest not handling URLs handled
  by rewrite rules such as RewriteRule ^/category/([0-9]+)/.*$ /index.php 
  [E=PRODUCT:news,E=LANGUAGE:en_US,E=STATE:bycategory,E=CATID:$1,PT] - (@thekid)
* Fixed lang.Process thinking it would be able to execute directories 
  which would simply fail later on. - (@thekid)
* Fixed leftover of refactoring of peer.mail.MessagingAddress 
  interface in peer.mail.transport.SmtpTransport - (@gelli, @reuter, @kiesel)


## 5.7.1 / 2009-01-23

### Heads up!

* Deprecated SOAP_ACTION_* global constants in 
  webservices.soap.transport.SOAPHTTPTransport class in favor of
  class constants in same class. - (@kiesel)

### RFCs

* Implemented RFC 0176 - Scriptlet URL handler classes - (@ohinckel)

### Bugfixes

* Fixed net.xp_framework.unittest.bootstrap package to work with paths
  with spaces inside - (@thekid)
* Fixed peer.ftp.DefaultFtpListParser to return correct dates for entries
  listed with compact format ("Apr 4 20:16" instead of "Apr 4 2009") - (@thekid, @ohinckel)
* Fixed io.File's members filename, dirname and extension being set from
  original URI instead of resolved one - (@thekid)
* Fixed bug #30 - recursive class loading in static initializer  - (@thekid)
* Fixed error in rdbms.sybase.SybaseDialect, the cast SQL function had 
  flipped arguments. - (wagner)
* Set default encoding in JsonMessage to UTF-8 since JsonEncoder/
  JsonDecoder always encodes stuff in UTF-8 - (@ohinckel)
* Fixed WebdavClient::put() method to explicitely pass a RequestData 
  object to HttpConnection's put() method - (@ohinckel)

### Features

* Added support to unittest command to run all non-abstract TestCase
  classes inside a package (e.g. unittest my.package.name.*') - (@thekid)
* Added -e option to unittest command to evaluate assertions directly 
  from the command line (e.g. unittest -e '$this->assertTrue(TRUE);') - (@thekid)
* Increased verbosity in unittest.TestCase's methods assertClass() and 
  assertSubclass() in case given variable is not instanceof Generic - (@thekid)
* Changed lang.Thread to not forget process id of forked children after
  it has been stopped or exited - (@kiesel, @thekid)


## 5.7.0 / 2008-11-24

### Heads up!

* Deprecated util.cmd.Runner class, this is now in xp.command in the 
  tools-xar (part of RFC 0173 implementation) - (@thekid)
* Removed class util.profiling.ClassProfiler - (@kiesel)
* Deprecated LOGGER_* global constants in util.log.LogCategory in favor
  of class constants in util.log.LogLevel - (@thekid)
* Deprecated HTTP_* global constants in peer.http.HttpConstants in favor
  of class constants - (@thekid)
* Removed package util.io and class util.io.VirtualFileManager and
  io.EmbeddedFile - (@kiesel)
  
### RFCs

* Implemented RFC 0173 - xp-tools.xar - (@thekid)
* Implemented RFC 0164 - Exceptions for XPClass::get*() - (@thekid)

### Bugfixes

* Fixed img.util.ExifData::flashUsed() method - (@thekid)
* Fixed unittest.AssertionFailedError not to hide the actual location
  in the stacktrace. - (@thekid)
* Made message part optional in HTTP status response when parsing it in
  peer.http.HttpResponse::readHeader() - (@ohinckel)
* Fixed functionality of peer.ldap.LDAPQuery::getAttrsOnly() - (wagner)

### Features

* Switched from is_callable() to method_exists() in classloading parts 
  of the core for performance reasons. - (@thekid)
* Added support for io.streams API to StreamReader and StreamWriter
  from the img.io package (keeping backwards compatibility)
  http://news.xp-framework.net/article/279/2008/10/21 - (@thekid)
* Added peer.ftp.collections package - bridge between peer.ftp and 
  io.collections - (@thekid)
* Added lang.XPClass::getSimpleName()  - (@thekid)
* Made multiple forward-compatibility related adjustments - (@thekid, @kiesel)
* Added peer.ldap.LDAPQuery::setAttrsOnly() - (wagner)


## 5.6.9 / 2008-09-03

### Heads up!

* Removed disfunctional unittest.coverage package - (@thekid)
* Deprecated lang.Collection class - Use lang.types.ArrayList or the 
  classes from util.collections instead - (@thekid)
* Deprecated util.DateInterval in favor util.TimeInterval - (@thekid)

### RFCs

* Implemented RFC 0171 - Rename util.DateInterval class - (@thekid)
* Implemented RFC 0169 - New package unittest.web - (@thekid)

### Bugfixes

* Fixed text.encode.QuotedPrintable to encode question marks - (@treuter)
* Fixed lang.types.Number::equals() to return false for different types
  with same values (new Integer(1)->equals(new Long(1)) no longer yields
  TRUE now). - (@thekid)
* Made util.DateMath::diff() produce correct results (fixed segfault
  for PHP 5.2.6). - (@kiesel)

### Features

* Added util.collections.Arrays class: extension methods and bridging
  between lang.types and util.collections. - (@thekid)
* Added annotation accessors to lang.reflect.Field: hasAnnotation(),
  getAnnotation(), hasAnnotations(), getAnnotations() - (@thekid)
* Added XML declaration to debug output at sapi scriptlet.development - (@kiesel)
* Moved xml.wsdl to net.xp_framework.wsdl; this package only contained
  wsdl.xsl - so no code affected. - (@kiesel)


## 5.6.8 / 2008-08-03

### Heads up!

* Completely refactored the peer.http API to be able to nicely 
  implement RFC 0162. - (@thekid)
* HttpConnection::put() method explicitely required now a RequestData
  object as argument to upload a file, instead of just passing the data
  string - (@ohinckel)

### RFCs

* Implemented RFC 0162: HTTP proxy - (@thekid)

### Bugfixes

* Fixed peer.ftp API to be able to cope with numerous FTP servers - (@thekid)
* Fixed fatal error when XPClass::isEnum() had been used before
  lang.Enum has actually been loaded - (@kiesel)
* Fixed off-by-one error when reading chunked http response bodies and
  added a unittest for this - (@kiesel, @ohinckel)
* Added missing function rdbms.Peer::getTable() which is used in
  rdbms.DataSet class - (@ohinckel)
* Made lang.ClassLoader::loadClass() raise a ClassFormatException in
  case the included file does not declare the class. - (@thekid)

### Features

* Added lang.reflect.Field::set() method - (@thekid)
* Made peer.Socket instances cloneable - (@thekid)
* Made peer.URL constructor parameter optional - (@thekid)
* Extended util.MimeType class to include types for various programming
  languages. - (@thekid)
* Added 503 error page to scriptlet SAPI - (@thekid)
* Added pushBack() method to text.Tokenizer class and its implementations - (@thekid)


## 5.6.7 / 2008-06-09

### Heads up!

* Deprecated xml.XML class - (@thekid)

### RFCs

* Implemented RFC 0166: XP Runners - (@thekid)
* Implemented RFC 0165: New text.regex package - (@thekid)
* Implemented RFC 0163: Respect ANSI SQL in rdbms package - (@kiesel)
* Implemented RFC 0157: Make product, language and session optional 
  URL parts - (@kiesel)
  
### Bugfixes

* Fixed FtpConnection::getDir() function by passing FTP connection object
  to FtpDir constructor - (@ohinckel)
* Fixed _recurseData() in XPSoapMessage to handle arrays vs. objects
  correctly - (@ohinckel)
* Made HttpScriptlet::process() rethrow HttpScriptletExceptions as-is,
  while any other exception is wrapped into a HttpScriptletException. - (@thekid).
* Fixed bug when scriptlet.xml.workflow.casters.ToDate accepted an
  abbreviated date, interpreted it as a time and used the
  current date with given time instead the given date - (@kiesel)
* Fixed session id initialization in XMLScriptletRequest::initialize() - (@ohinckel)
* Fixed rdbms.Record::set() method - (bayer, @kiesel)

### Features

* Added lang.Process::newInstance() method
  http://news.xp-framework.net/article/258/2008/05/28/New_Process - (@thekid)
* Added " @ {host}" to rdbms.SQLConnectionClosedException's message - (@thekid)
* Changed XAR file format (BC-break-free) to be less memory-consuming,
  platform-independent and get rid of original filename junk - (@thekid)
* Added a parameter "bool append" (defaulting to FALSE) to 
  io.streams.FileOutputStream's constructor - (@thekid)
* Made io.streams.FileInputStream and io.streams.FileOutputStream's
  constructors accept either an io.File or a string with the filename - (@thekid)
* Added syntactical support in text.doclet.markup API for ordered and
  unordered lists. - (@thekid)


## 5.6.6 / 2008-04-23

### Heads up!

* Changed xpcli / util.cmd.Runner argument passing - argv[0] should not be
  passed to Runner::main() - (@thekid)
* Deprecated addMinutes(), addHours(), addDays() methods in util.TimeSpan - (@gelli)
* Backwards incompatible changes in lang.reflect package
  http://news.xp-framework.net/article/240/2008/03/08/BC - (@thekid)
* Removed deprecated lang.reflect.Argument class and corresponding 
  methods in lang.reflect.Routine - (@thekid)

### RFCs

* Implemented RFC 0160: xml.IXSLProcessor::setXMLTree() / setXSLTree() - (@thekid)
* Implemented RFC 0156: Constructor::newInstance() - (@thekid)
* Implemented RFC 0155: lang.reflect.Routine::getReturnType(): Type 
  instead of string - (@thekid)
* Implemented RFC 0148: Bytes type - (@thekid)

### Bugfixes

* Made xarloader::acquire() throw a lang.FormatException in case the
  given file is not a valid XAR archive - (@thekid)
* Fixed bug #28 - MySQLConnection does not honor Port given in DSN - (mcontento, @thekid)
* Fixed util.Date's constructor with timestamps - (@SirCasm, @kiesel)
* Fixed wrong date creation in util.Message which created wrong sending
  dates - (@ohinckel)
* Fixed newinstance() and create() for fully qualified classes - (@thekid)
* Fixed xml.DomXSLProcessor to produce meaningful error messages in
  case a transformation failed - (@kiesel)
* Fixed text.doclet.markup API to handle sourcecode, preformatted text
  ordered and unordered lists correctly - (@thekid)
* Added mapping for field type double to rdbms.mysql.MySQLDBAdapter - (@ohinckel)
* Fixed problems with percent sign (%) substitution in RDBMS API when
  using Criteria or Statement class - (@ohinckel)
* Fixed unittest.TestSuite to not report errors if a previously run
  test's setUp() routine failed. - (@thekid)
* Fixed util.Properties::readBool() to return TRUE if any of "1", "yes",
  "true" or "on" is given (see bug #26) - (@thekid)
* Fixed lang.Process to not throw exceptions if non-qualified command
  is given (e.g. new Process('ssh')) by resolving PATH environment - (@thekid)
* Fixed problem with xpcli -c / -cp option conflicting with the run 
  Command class' -c / -cp options - (@thekid)
* Fixed HttpScriptletRequest::getData never read from php://input - (wagner)

### Features

* Added lang.ClassLoader::getLoaders() - (@thekid)
* Made lang.ClassLoader::registerPath() throw an exception in case
  the given path does not exist - (@thekid)
* Made lang.base.php use PHP_INT_MAX for LONG_MIN / LONG_MAX values 
  instead of calculating it manually. Saves on function call. - (@thekid)
* Added security.password package - (@thekid)
* Added ENUM class type to text.doclet API - ClassDoc::isEnum() will return
  true and ClassDoc::classType() will yield ENUM_CLASS if this classdoc 
  object represents a subclass of lang.Enum. - (@thekid)
* Added containingPackage() and contains() methods to PackageDoc class - (@thekid)
* Made io.File constructor accept (string, string) and (Folder, string)
  See http://news.xp-framework.net/article/241/2008/03/24/io.File - (@thekid)
* QA work: Completed / updated apidocs in multiple places - (@thekid)
* Added util.TimeSpan::add(), util.TimeSpan::subtract() methods - (@gelli, @thekid)
* Added static seconds(), minutes(), hours(), days() and weeks() to
  util.TimeSpan - (@gelli, @thekid)
* Added rdbms.DBTableAttribute::getLength() accessor - add this 
  information to XML generated from databases via DBXmlGenerator - (mcontento, @thekid)
* Added SQL function to week() to extrakt the "week of year" from a
  date field - (wagner)


## 5.6.5 / 2007-02-04

### Heads up!

* Removed deprecated lang.reflect.Argument::isPassedByReference() method - (@thekid)
* Changed cast() core functionality to no longer change the object given,
  throw a lang.ClassCastException in case of errors, throw a 
  lang.ClassNotFoundException in case the given type does not exist,
  no longer support primitives (will result in a lang.IllegalArgumentException)
  and to return xp::null() if NULL is given - (@thekid)

### RFCs

* Implemented RFC 0152: Arguments vs. Parameters - (@thekid)
* Implemented RFC 0153: HTTP protocol versions - (@thekid)
* Implemented RFC 0151: Runtime class - (@thekid)
* Implemented RFC 0150: Before and after methods for testcases - (@thekid)
* Implemented RFC 0010: Type casts - (@thekid)

### Bugfixes

* Fix double utf8 decode in SOAPHTTPTransport::retrieve() by ignoring the
  Content-Type header (XMLParser already determines the encoding and uses
  ISO-8859-1 as default output encoding) - (@ohinckel, @kiesel)
* Added "static" keyword to System::putEnv() und System::getEnv() - (@ohinckel)
* Ensured arguments passed to XSL callbacks are utf8_decode()d and return 
  values are utf8_encode()d - (@thekid)
* Fixed peer.ftp.FtpFile not reloading file details (size, date, permissions,
  ...) after a successfull upload - (@thekid)
* Fixed peer.ftp.FtpDir::findEntry()  - (@gelli, @kiesel)
* Fixed FTP server API to parse parameters to NLST correctly and report
  directories as single entities if "-d" is given in NLST and LIST - (@gelli, @thekid)
* Fixed lang.reflect.Proxy to add type hints to generated methods - (@thekid)

### Features

* Added "-cp" command line option to xpcli which will add XAR files and
  directories given to it to the classpath - (@thekid)
* Added lang.ClassLoader::registerPath() method - (@thekid)
* Added util.collections.Vector::addAll() method - (@thekid)
* Added optional parameter "hinted" to lang.reflect.Argument::getType()
  with which one can retrieve the type hint's type name. - (@thekid)
* Added lang.reflect.Argument::getDefaultValue() method. In contrast to 
  getDefault() it returns the value as-is (not as a string) - (@thekid)


## 5.6.4 / 2007-12-25

### Heads up!

* Removed deprecated rdbms.Criteria::newInstance() method - (@thekid)
* Removed deprecated lang.reflect.Routine::returnsReference() method - (@thekid)
* Refactored unittest.TestSuite/unittest.TestCase for added listener API.
  With these changes, test cases cannot be run on their own but must always
  be added to a suite before - it can run single tests via runTest() - (@thekid)
* An implementation of scriptlet.rpc.AbstractRpcRequest::getMessage()
  must set the message's encoding on it's own now, the router does
  not do this anymore - (@kiesel)
* Removed constant SKELETON_PATH - (@thekid)
* Changed stack trace elements generated from PHP error message to no 
  longer contain the class. The filename cannot really be translated to 
  the fully qualified class name in a consistent *and* fast-enough manner - (@thekid)
* Made util.Properties::fromFile() throw an IOException when the given
  file cannot be read - (@thekid)
* Deprecated lang.archive.ArchiveReader in favor of lang.archive.Archive
  http://news.xp-framework.net/article/226/2007/12/11/ArchiveReader_deprecation - (@thekid)

### RFCs

* Implemented RFC 0145: Make unittests strict - (@thekid, @kiesel)

### Bugfixes

* Fixed util.collections.HashSet::addAll() to not change the list if one
  of the array elements is of invalid type - (@thekid)
* Fixed lang.reflect.Routine::getExceptionTypes() always throwing exceptions. - (@thekid)
* Fixed lang.Primitive::boxed() for NULL values - (@thekid)
* Fixed a bug in peer.ldap which occurred when the size-limit for the results
  was exceeded..  - (@ohinckel, @SirCasm)
* Fixed webservices.soap API to fix problem with SOAP clients that send 
  UTF-8 encoded values - (@kiesel)
* Fixed webservices.soap API to properly read timezone from serialized
  SOAP dateTime values and also pass timezone information - (@kiesel)
* Fixed util.Properties class to throw IOExceptions in case the ini file
  cannot be read - (@thekid)
* Fixed lang.archive.ArchiveClassLoader::providesPackage() method - (@thekid)
* Fixed rdbms.util.DBXmlGenerator::getSource() - (bug #25) - (@thekid)
* Fixed security.cert.X509Certificate::getSubjectDN() and
  security.cert.X509Certificate::getIssuerDN() to decode DNs correctly - (@thekid)
* Fixed notices and warnings in various places (E_ALL conformance). - (@thekid)

### Features

* Changed raise() functionality to support more than one argument to be
  passed to exception's constructor via varargs - (@kiesel, @thekid)
* Made rdbms.DriverManager::register() throw an IllegalArgumentException
  when an incorrect driver class was given - (@thekid)
* Improved performance of lang.types.ArrayList::equals() method - (@thekid)
* Improved performance of XPClass::getFields() and XPClass::getField() - (@thekid)
* Added API to listen to events during a unit test run - (@thekid)
* Made it possible to run multiple unittests at once with the
  unittest cli runner - see "unittest -?" for examples. - (@thekid)
* Added option to choose target encoding in xml.parser.XMLParser with
  the setEncoding() method. Default encoding still is ISO-8859-1. - (@kiesel)
* Added 'd' (diff) instruction to xar command - (@kiesel)
* Added setters for all elements of an URL to peer.URL class - (@kiesel)
* Added support for XARs inside XARs - (@thekid)
* Added lang.ClassLoader::removeLoader() method to complement its
  registerLoader() method. - (@thekid)
* Made lang.archive.ArchiveClassLoader use xar loader provided by
  lang.base.php instead of having to use an extra ArchiveReader class - (@thekid, @kiesel)
* Improved performance of xml.XSLCallback class - (@thekid)
* Improved performance of XPClass::getMethods() / XPClass::getMethod() / 
  XPClass::hasMethod() and XPClass::getConstructor() / 
  XPClass::hasConstructor() - (@thekid)


## 5.6.3 / 2007-11-9

### Heads up!


### RFCs


### Bugfixes

* Fixed pre-fetching mode in peer.ldap-API - (@thekid, @kiesel)
* Fixed scriptlet.xml.workflow.casters.ToDate which was accepting 
  when it encountered empty input (unittest said it shouldn't) - (@thekid)
* Fixed DomXSLProcessor::determineOutputEncoding() to handle absolute
  URIs in xsl:include/xsl:import hrefs correctly - (@thekid)
* Fixed DomXSLProcessor::determineOutputEncoding() to return the output
  encoding declared in imported XSL files using <xsl:import/> - (@thekid, @ohinckel)
* Fixed lang.ClassLoader::defineInterface() and defineClass() methods - (@kiesel)
* Fixed use of undefined variables in lang.Primitive,
  lang.types.ArrayList and lang.types.String - (@kiesel)
* Fixed LDAPClient::read() method - (@kiesel)
* Ports: Fixed Nagios Heartbeat adding two dots between unqualified host
  name and domain (e.g. example..1and1.com where example is the host name) - (@ohinckel, @SirCasm)

### Features



## 5.6.2 / 2007-11-21

### Heads up!

* Removed deprecated xml.XSLProcessor class - (@thekid)
* Changed img.util.IptcData::setDateCreated() to accept and 
  img.util.IptcData::getDateCreated() to return util.Date instances - (@thekid)
* The directories "experiments", "ports/dist" and several project  specific classes from ports/classes have been moved to the XPForge
  repository - http://xp-framework.info/xml/xp.en_US/news/view?217 - (@thekid)

### RFCs

* Implemented RFC 0143: Image metadata API enhancements - (@thekid)
* Implemented RFC 0139: XPForge SVN - (@thekid, @kiesel)
* Implemented RFC 0141: FTP Transfer listening - (@thekid)
* Implemented RFC 0142: I/O Streams to PHP streams wrapper - (@thekid)
* Implemented RFC 0140: FTP API enhancements - (@thekid)
* Implemented RFC 0138: Additional interceptor callbacks in 
  peer.ftp.server - (@gelli)

### Bugfixes

* Fixed peer.mail.Message which set the Date of the message via
  Date::now()->toString(). Since the default behaviour of toString
  was changed with the new date object this led to a non rfc-conform
  date in the mail header. - (@ohinckel, @SirCasm)
* Work around PHP bug #43206 by using date_time_set / date_date_set
  in util.DateUtil instead of Date::getTimeZone(). - (clangg)
* Fix XPSoapMessage::_recurseData() to return array keys without ":" in
  case a namespace is included in node name - (@ohinckel)
* Fixed xp::sapi() when using XAR-releases on Windows - (@thekid)
* Added support for field type tinytext, mediumblob and bit to
  rdbms.mysql.MySQLDBAdapter - (wagner, @ohinckel)
* Ensured file is only then closed in io.streams.FileOutputStream's 
  destructor if it's still open - (@thekid)
* scriptlet.xml.workflow.casters.ToDate now uses the constructor of 
  the util.Date object for parsing the date string instead of 
  text.parser.DateParser. The DateParser led to erroneous results. - (@SirCasm)
* Fixed a bug which prevented dataset objects from joins to 
  build correctly - (@SirCasm)
* Fixed bug in parsing of keywords and supplementalCategories in 
  img.util.IptcData which would return only the first element - (@thekid)
* Fixed bug in peer.mail.store.ImapStore where one could not disable
  checking the validity of a certificate - (@SirCasm, @kiesel)
* Fixed assumption of port numbers in peer.http.HttpRequest when no 
  port number is given. - (@thekid, @gelli)
 
### Features

* Added tell() method to io.streams.Seekable interface and its
  implementations to complement seek() - (@thekid)
* Made XMLScriptlets use the XSL processor's output encoding in their
  Content-Type response header - (@thekid)
* text.parser.DateParser makes use of parser capabilities of php.DateTime - (@kiesel)
* util.Date objects preserve GMT offset after deserialization - (@kiesel, thekid, @ohinckel)
* added getOffset(), getOffsetInSeconds() methods to util.Date - (@kiesel)
* Extract FocalLength from EXIF data in img.util.ExifData class - (@thekid)
* Implemented peer.ldap.LDAPSearchResult in a way that will read
  incrementally instead of fetching everything when constructed - (@gelli)
* Ensured dateinterval type argument to DateMath::diff() is passed in 
  uppercase in XSL date callback - (@gelli)


## 5.6.1 / 2007-09-25

### Heads up!

* The serialization format of util.Date has changed along with
  RFC 0115 (see below).

### RFCs

* Implemented RFC 0115 - Make Date objects represent an instance in time - (@kiesel, @thekid)
* Implemented RFC 0135 - Default callbacks for XSLT (2nd part) - (@kiesel)

### Bugfixes

* Fixed util.collections.HashSet iteration via foreach() - (@thekid)
* Reverted bugfix in text.CSVGenerator - (@kiesel)

### Features

* Made forward-compatible changes to core classes to ease migration to
  XP6 (which makes use of PHP namespaces, see RFC 0136) - (@thekid)
* Ports: new package org.cyrus - Cyrus IMAP admin functionality - (@kiesel)


## 5.6.0 / 2007-08-29

### Heads up!

* Generics create() syntax changes
  http://xp-framework.info/xml/xp.en_US/news/view?208 - (@thekid)
* util.ChainedException is now lang.ChainedException
  http://xp-framework.info/xml/xp.en_US/news/view?209 - (@thekid)

### RFCs

* Implemented RFC 0097 - TargetInvocationException - (@thekid)
* Implemented RFC 0132 - Enum support - (@thekid)
* Implemented RFC 0133 - Add support for filenames as argument for XPCLI - (@thekid)
* Implemented RFC 0135 - Default callbacks for XSLT - (@kiesel)
  
### Bugfixes

* Fixed duplicate redirect in scriptlet API when used with sessions - (@thekid)
* Fix remote.protocol.Serializer bug in best mapping finding
  algorithm - (@Frank Kleine, @kiesel)
* Fix deserialization of objects of XP types - (@kiesel)
* Fixed util.collections.HashTable::get(), ::remove() and ::containsKey()
  if used with generics / primitives - (@gelli, @thekid)
* Fixed "Record is not compatible with DataSet" when using rdbms.Statement
  as criteria with doSelect() / iteratorFor() - (wagner)

### Features

* Added new packaged remote.mappings for specialized Java classes - (strobel)


## 5.5.5 / 2007-07-19

* Fixed Dataset::insert() if object values contain % signs. - (wagner)
* Made rdbms.DBConnection::addObserver() return the observer added - (@thekid)
* Added methods to LDAPClient to perform entry modifications at the 
  attribute level. - (@gelli)
* Implemented RFC 131 - SetOperation and Query - (wagner)

## 5.5.4 / 2007-07-09

* Unbreak peer.irc-API with PHP5 - (@kiesel)
* Fix fatal error in XPClass::newInstance() when class represented is
  abstract or an interface - now throws lang.IllegalAccessExceptions - (@thekid)
* Changed EASC protocol representation of Enums (BC-break-free) - (@thekid)
* Added GIF support to org.fpdf.FPDF class' putImage() method - (@thekid)
* make util.cmd.Runner output errors and usage to STDERR - (@kiesel)

## 5.5.3 / 2007-06-26

* Protect Base57 encoding and decoding against possibly inccorrect results 
  if EG(precision) is too small - (@thekid)
* Make socket_select() work properly when interrupted by signals - (sperber)
* Fixed exception handler in SAPI soap.service - (@kiesel)
* Implemented sql function and projections for postgreSQL and sqllite - (wagner)

## 5.5.2 / 2007-06-15

* Fixed DomXSLProcessor's error handling only to throw exceptions for
  LIBXML_ERR_FATAL level - (@thekid, @gelli)
* Improved performance of img.filter.ConvolveFilter - use builtin
  imageconvolution() function if available
* Fixed bug in io.sys.IPCQueue::removeQueue() which lead to the queue 
  not being removed - (@thekid)
* Implemented RFC 0130 - util.HashmapIterator returns values instead of keys - (@thekid)
* Made TransformerException's message more verbose in DomXSLProcessor  - (@thekid, gelli, @clang)
* Implemented RFC 0096 - DataSet classes will be enhanced to know about 
  foreign keys (wagner)
* Implemented RFC 0123 - aggregate or cut down the query's resultset - (wagner)
* Implemented RFC 0124 - SQL standard functions - (wagner)
* Implemented RFC 0129 - join over multiple tables - (wagner)

## 5.5.1 / 2007-06-01

* Fixed bug #9 - peer.mail.InternetAddress not correctly encoding addresses - (@thekid)
* Fixed bug #8 - HttpRequest ignores QUERY_STRING - (@thekid)
* Fixed bug #14 - create empty Folder returns TRUE - (@thekid)
* Fixed bug #20 - util.collections.DJBX33AHashImplementation broken on 
  64 bit systems (resolved by using MD5HashImplementation) - (@thekid)
* Implemented RFC 0063: Unit test API cleanup - (@thekid)
* Implemented RFC 0031: Replace IntegerRangeChecker with NumberRangeChecker - (@thekid)
* Implemented RFC 0060: LDAPEntry::isA() - (@thekid)

## 5.5.0 / 2007-05-21

* Implemented RFC 0128: webservices.xmlrpc handling of NULL - (@gelli)
* Fix fatal error when ext/soap is enabled (name clash for class SoapFault) - (@kiesel)
* Prevent error leaks from libxml in DomXslProcessor class - (@kiesel)
* Fix possible endless loop in util.HashmapIterator class - (wagner)
* Implemented RFC 0126: rdbms.Column API - (wagner)
* Added support for foreach() iteration to I/O collection iterator classes - (@thekid)
* Fix bug when class could not be found in bootstrapping - (@kiesel)
* Implemented RFC 0127: rdbms.Peer::getConnection() - (@thekid)
* Added new text.StreamTokenizer class - (@thekid)
* Implemented RFC 0121: Class loading revamp - (@thekid)
* Implemented RFC 0117: New Package class - (@thekid)
* Implemented RFC 0037: Ability to fully qualify class names - (@thekid)
* Implemented RFC 0125: Fluent interface for util.log.LogCategory - (@thekid)
* Add SQLiteDBAdapter class - (@kiesel)
* Fix several minor issues in SQLiteConnection class - (@kiesel)

## 5.4.1 / 2007-05-04

* Fix bug with recursive uses() calls fatalling - (@thekid)
* Implemented RFC 0122 - Make handlers cancelable - (@kiesel)
* Implemented RFC 0106 - Array access / iteration / type boxing / generics  - (@thekid)
* Fix output of stacktraces in util.ChainedException - (@kiesel)
* Fix DomXSLProcessor's error handling by clearing the error stack when
  error was handled - (@ohinckel)
* Implemented RFC 0120 - Console::$out and Console::$err - (@thekid)
* Properly detect identity columns in rdbms.sybase.SybaseDBAdapter - (@kiesel)
* Fixed bug with io.File when reading from zero-length files - (@kiesel)
* add @args annotation for util.cmd.Command scripts (fixes bug #23) - (@thekid, @kiesel)
* fix extraction of .xar files on Windows (fixes bug #22) - (@thekid)

## 5.4.0 / 2007-04-18

* Implemented RFC 0113 - Include PHP5 SOAP extension - (@kiesel, @rene)
* Implemented RFC 0118 - Exceptions for missing token values in 
  StatementFormatter - (@thekid)
* Implemented RFC 0081 - new lang.Runnable interface - (@thekid)
* Fixed rdbms.StatementFormatter class when given an array of objects
  to format, e.g. array(Date::now(), DateUtil::addDays(Date::now(), 1)).
  In certain situations, this would even cause segmentation faults! - (@thekid)

## 5.3.2 / 2007-04-01

* Implemented RFC 0111 - Remove "util" and "ext" - (@thekid)
* Implemented RFC 0116 - Ports infrastructure: Technologies and packages - (@thekid, @kiesel)
* Added support for XML namespaces to xml.meta Marshaller / Unmarshaller - (@thekid)
* Fixed peer.mail.store.CclientStore (ext/imap wrapper) implementation 
  which was broken due to PHP5 BC breaks - (@thekid)
* Implemented RFC 0112 - HttpScriptletException inherits ChainedException - (@kiesel)
* Implemented RFC 0110 - RemoteStackTraceElement - (@thekid)
* Fix error "Call to protected method SOAPNode::_recurse()" in class
  SOAPHashMap - (clangg, ohinckel)

## 5.3.1 / 2007-02-27

* Fixed header handling for multiple headers with same name in scriptlet API  - (@gelli)
* Changed various classes to use type hints instead of manual instanceof/
  IllegalArgumentException code - (@thekid)
* Improve DomXSLProcessor's error handling - (@kiesel)
* Fix FTP directory listing in FtpConnectionListener to display centure
  correctly - (@ohinckel)
* Adapt xml.meta.Unmarshaller::contentOf() to new DOM API of PHP5 to handle
  "pass" attribute in "xmlmapping" annotation correctly - (@ohinckel, @strobel)
* Fix unbuffered queries on MySQLConnections - (@gelli)
* Fix copy and unset of duplicate subjects in MailTransport - (clang)
* FTP server can intercept CWD calls - (@kiesel, @ohinckel)

## 5.3.0 / 2007-02-01

* Make xpcli show self-usage if invoked without any arguments - (@thekid)
* Implemented RFC 0100 - IllegalArgumentException for Type hints - (@thekid)
* Made VCalendar API in the org.imc-package work with Microsoft Outlook - (@SirCasm)
* Add ability to use log=<category> in the Remote DSN for logging
  in the same it is done in database connection DSNs - (@ohinckel)
* Changed lang.reflect.Argument::isOptional() to also work when no 
  apidoc comment exists. - (@thekid)
* Added support for ENUM type to MySQLDBAdapter - (@thekid, @SirCasm)
* Implemented RFC 0069 - EASC server in php - (@gelli)
* Implemented RFC 0076 - Transfer exception cause, on the PHP server 
  side (previously only implemented in PHP client and Java server) - (@thekid)
* Fixed peer.mail.store.ImapStore::_supports() method - (@kiesel)
* Fixed peer.mail.transport.SmtpTransport class - (@SirCasm)
* Implemented RFC 0099 - New rdbms.finder API - (@thekid)

## 5.2.0 / 2007-01-17

* Replace deprecated is_a() with instanceof operator in core - (@kiesel)
* Made rdbms.Criteria usable in a fluent interface way - (e.g. 
  $c= Criteria::newInstance()->add('bz_id', 500, EQUAL)->add(...); - (@thekid)
* Implemented RFC 0107 (Generic acccess to rdbms.Peer/rdbms.DataSet) - (@thekid)
* Use stream wrapper loading for loadClass() in class
  lang.archive.ArchiveClassLoader - (@kiesel)
* Implemented RFC 0083: Define classes through stream wrappers - (@kiesel)
* Deprecated xml.XSLProcessor - use xml.DomXSLProcessor instead
  Sablotron was removed from ext in PHP5 (in Version 5.0.0 Beta 1
  on 29-Jun-2003, ChangeLog states: "Completely Overhauled XML 
  support ... Moved the old DOM-XML and XSLT extensions to PECL")
  Neither PECL nor PECL4Win show any trace of ext/xslt extension 
  anymore, though. - (@thekid)
* Changed xml.XMLScriptlet to use xml.DomXSLProcessor - (@thekid)

## 5.1.2 / 2007-01-10

* Made rdbms.DataSet class abstract - (@thekid)
* Fix static initializer not being called for classes loaded via 
  uses wrapper  - (@thekid)
* Implemented RFC 0105: Package-Info - (@thekid)
* Fix setBase() when working with in-memory stylesheets - (clang)
* Fix "Static function DataSet::getPeer() should not be abstract"
  in rdbms.DataSet - (@thekid)

## 5.1.1 / 2007-01-04

* Make arbitrary request headers available in CGI sapi - (@kiesel)
* Implement more stream wrapper functions in XpXarLoader, providing a
  more complete stream wrapper support (file_exists now works) - (@kiesel)
* Fix addRecipient() method in peer.mail.Message - (@kiesel)
* Make xml.DomXSLProcessor::setBase() work again - (@kiesel)
* Show class' apidoc comment in xpcli --help|-? - (@thekid)
* Added lang.XPClass::getComment() - (@thekid)
* Added lang.reflect.Field::getModifiers() - (@thekid)
* Added lang.reflect.Modifiers utility class - (@thekid)
* Made CLI sapi print uncaught exceptions to STDERR - (@thekid)
* Made Doclet API parse XP5 syntax - (@thekid)
* Exclude replication tables from SybaseDBAdapter::getTables() - (@thekid)
* Implemented RFC 0104: Add XSLCallback class and provide support 
  for calling PHP functions from within XSL - (@kiesel)

## 5.1.0 / 2006-12-28

* Implemented RFC 0103: Coding standards adjustements for XP5 - (@thekid)
* Implemented RFC 0102: XP Class Runner - (@thekid)
* Implemented RFC 0088: Streams API - (@thekid)
* Updated SQLite class to use StatementFormatter - (@kiesel)
* Added webservices.soap.types.SOAPDouble class - (strobel)
* Fixed util.Properties to return an empty array from readArray()
  for an empty string such as values="" - (@thekid)
* Fixed Java Webstart example to work with URLs containing query string
  arguments - (@thekid)

## 5.0.0 / 2006-12-21

* fix reflection for classes from .xars - (@kiesel)
* implemented RFC 0092 (migrated to PHP5) - (@kiesel)
  
## 4.1.3 / 2006-12-18

* Fix (De-)Serializer bug in remote package - (@kiesel)
* Add SQLConnectionClosedException in rdbms package for mysql - (@gelli)
* Fix several bugs in DBAdapter API - (@rene)
* Add method hasProperties() to util.PropertyManager - (@thekid)
  
## 4.1.2 / 2006-12-05

* Implemented RFC 0090 - "Refactor remote.protocol.Serializer" - (@thekid)
* Added setConnectTimeout() to peer.http.HttpConnection - (@kiesel)


## 4.1.1 / 2006-11-28

* Fixed xp::sapi() to correctly load sapis from XARs. - (@kiesel)

* Build stacktrace elements in toString() output in a more
  defensive way. In cases where exceptions are thrown, arguments 
  of a function call in the debug_backtrace() array might already
  be destructed and their types are no longer known. The builtin
  gettype() function can handle those.
  Just casting those arguments to string will result in memory overflow
  fatal errors or segfaults like these:
  FATAL:  emalloc():  Unable to allocate 3238256257 bytes - (@kiesel)


## 4.1.0 / 2006-11-27

* Implemented RFC 0080 - "Anonymous class creation" - (@thekid)
  

## 4.0.0 / 2006-11-20

* Implemented RFC 0084 - "Packages cleanup" - (@thekid)
