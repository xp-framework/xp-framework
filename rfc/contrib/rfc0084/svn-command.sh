# These commands use directories relative to skeleton
cd %SKELETON%

# Unittests
svn move util/profiling/unittest/ unittest/

# Webservices
svn mkdir webservices
svn move xml/soap/ webservices/soap/
svn move xml/uddi/ webservices/uddi/
svn move xml/xmlrpc/ webservices/xmlrpc/
svn move org/json/ webservices/json/
svn move xml/wddx/ webservices/wddx/

# Deprecation
svn rm xml/xp/
svn rm io/cca/
svn rm util/adt/
svn rm util/registry/
svn rm text/apidoc/

# Domain-specific -> ports
svn move org/ ../ports/classes/
svn move com/ ../ports/classes/
svn move ch/ ../ports/classes/
svn move us/ ../ports/classes/

# ...ports/classes/net already exists:)
svn move net/xmethods/ ../ports/classes/net/
svn move net/xp_framework/ ../ports/classes/net/

# peer.cvs -> ports/org.cvshome
svn move peer/cvsclient/PServerClient.class.php ../ports/classes/org/cvshome/
svn rm peer/cvsclient/

# util.mp3 -> ports/de.fraunhofer.mp3
svn mkdir ../ports/classes/de/fraunhofer
svn move util/mp3/ ../ports/classes/de/fraunhofer/

# util.archive -> ports/org.gnu.tar
svn mkdir ../ports/classes/org/gnu/tar/
svn move util/archive/TarArchiveEntry.class.php ../ports/classes/org/gnu/tar/
svn move util/archive/TarArchive.class.php ../ports/classes/org/gnu/tar/

# text.translator -> ports/net.schweikhardt
svn mkdir ../ports/classes/net/schweikhardt/
svn move text/translator/Swabian.class.php ../ports/classes/net/schweikhardt/
svn move text/translator/Translator.class.php ../ports/classes/net/schweikhardt/
svn rm text/translator

# peer.ajp -> ports/org.apache.ajp
svn mkdir ../ports/classes/org/apache/
svn move peer/ajp/ ../ports/classes/org/apache/
