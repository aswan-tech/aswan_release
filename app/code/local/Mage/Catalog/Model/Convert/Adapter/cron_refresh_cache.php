#!/usr/bin/php
<?
/**
* cron_refresh_cache.php
* 
* @copyright  copyright (c) 2009 toniyecla[at]gmail.com
* @license    http://opensource.org/licenses/osl-3.0.php open software license (OSL 3.0)
*/

( !$_SERVER["HTTP_USER_AGENT"] ) or die ( "Nothing to do\n" ); // to run via local browser use ($_SERVER["SERVER_ADDR"] == $_SERVER["REMOTE_ADDR"])

require_once 'app/Mage.php';
umask( 0 );
Mage :: app( "default" );
$ver = Mage :: getVersion();
$userModel = Mage :: getModel( 'admin/user' );
$userModel -> setUserId( 0 );
Mage :: getSingleton( 'admin/session' ) -> setUser( $userModel );

echo "Refreshing cache...\n";
Mage :: app() -> cleanCache();
$enable = array();
foreach ( Mage :: helper( 'core' ) -> getCacheTypes() as $type => $label ) {
    $enable[$type] = 1;
    } 
Mage :: app() -> saveUseCache( $enable );

if ( $ver >= 1.3 ) {
    try {
        Mage :: getResourceModel( 'catalog/category_flat' ) -> rebuild();
        echo "  Flat Catalog Category was rebuilt successfully\n";
        } 
    catch ( Exception $e ) {
        echo $e -> getMessage() . "\n";
        } 

    try {
        Mage :: getResourceModel( 'catalog/product_flat_indexer' ) -> rebuild();
        echo "  Flat Catalog Product was rebuilt successfully\n";
        } 
    catch ( Exception $e ) {
        echo $e -> getMessage() . "\n";
        } 
    }
else {
    try {
        Mage :: getSingleton( 'catalog/url' ) -> refreshRewrites();
        echo "  Catalog Rewrites were refreshed successfully\n";
        }
    catch ( Exception $e ) {
        echo $e -> getMessage() . "\n";
        }
    }

try {
    Mage :: getModel( 'catalog/product_image' ) -> clearCache();
    echo "  Image cache was cleared successfully\n";
    } 
catch ( Exception $e ) {
    echo $e -> getMessage() . "\n";
    } 

try {
    $flag = Mage :: getModel( 'catalogindex/catalog_index_flag' ) -> loadSelf();
    if ( $flag -> getState() == Mage_CatalogIndex_Model_Catalog_Index_Flag :: STATE_RUNNING ) {
        $kill = Mage :: getModel( 'catalogindex/catalog_index_kill_flag' ) -> loadSelf();
        $kill -> setFlagData( $flag -> getFlagData() ) -> save();
        } 
    $flag -> setState( Mage_CatalogIndex_Model_Catalog_Index_Flag :: STATE_QUEUED ) -> save();
    Mage :: getSingleton( 'catalogindex/indexer' ) -> plainReindex();
    echo "  Layered Navigation Indices were refreshed successfully\n";
    } 
catch ( Exception $e ) {
    echo $e -> getMessage() . "\n";
    } 

try {
    Mage :: getSingleton( 'catalogsearch/fulltext' ) -> rebuildIndex();
    echo "  Search Index was rebuilded successfully\n";
    } 
catch ( Exception $e ) {
    echo $e -> getMessage() . "\n";
    } 

try {
    Mage :: getSingleton( 'cataloginventory/stock_status' ) -> rebuild();
    echo "  CatalogInventory Stock Status was rebuilded successfully\n";
    } 
catch ( Exception $e ) {
    echo $e -> getMessage() . "\n";
    } 

echo "Done\n";

?>
