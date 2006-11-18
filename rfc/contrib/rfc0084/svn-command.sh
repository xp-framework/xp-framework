svn up ~/devel/xp/trunk/
svn move
svn move util/profiling/unittest/ unittest/
svn mkdir webservices
svn move xml/soap/ webservices/soap/
svn move xml/uddi/ webservices/uddi/
svn move xml/xmlrpc/ webservices/xmlrpc/
svn move org/json/ webservices/json/
svn move xml/wddx/ webservices/wddx/
svn rm xml/xp/
svn rm io/cca/
svn move org/ ../ports/classes/
svn move com/ ../ports/classes/
svn move net/ ../ports/classes/
svn move net/xmethods/ ../ports/classes/net/
svn move net/xp_framework/ ../ports/classes/net/
svn move ch/ ../ports/classes/
svn move us/ ../ports/classes/
svn rm util/adt/
svn rm util/registry/
svn rm text/apidoc/
svn st
svn move peer/cvsclient/*.php ../ports/classes/org/cvshome/
svn rm peer/cvsclient/
svn mkdir ../ports/classes/de/fraunhofer
svn mkdir ../ports/classes/de/fraunhofer/mp3
svn move util/mp3/*.php ../ports/classes/de/fraunhofer/mp3/
svn rm --force ../ports/classes/de/fraunhofer/mp3/
svn move util/mp3/ ../ports/classes/de/fraunhofer/
svn mkdir ../ports/classes/org/gnu/tar/
svn move util/archive/*.php ../ports/classes/org/gnu/tar/
svn move util/archive/TarArchiveEntry.class.php ../ports/classes/org/gnu/tar/
svn move util/archive/TarArchive.class.php ../ports/classes/org/gnu/tar/
svn mkdir ../ports/classes/net/schweikhardt/
svn move text/translator/Swabian.class.php ../ports/classes/net/schweikhardt/
svn move text/translator/Translator.class.php ../ports/classes/net/schweikhardt/
svn mkdir ../ports/classes/org/apache/
svn move peer/ajp/ ../ports/classes/org/apache/
svn st text/apidoc/
grep 'svn' ~/.bash_history > ../rfc/contrib/0084-svncmds.sh
vim ../rfc/contrib/0084-svncmds.sh 
