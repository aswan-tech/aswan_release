#!/usr/bin/php
<?
/**
* cron_import_customers.php
* 
* @copyright  copyright (c) 2009 toniyecla[at]gmail.com
* @license    http://opensource.org/licenses/osl-3.0.php open software license (OSL 3.0)
*/

( !$_SERVER["HTTP_USER_AGENT"] ) or die ( "Nothing to do\n" ); // to run via local browser use ($_SERVER["SERVER_ADDR"] == $_SERVER["REMOTE_ADDR"])
( $argc > 2 ) or die ( "Bad number of parameters, please try $argv[0] PROFILE_ID FILE_TO_IMPORT\n" );

require_once 'app/Mage.php';
umask( 0 );
Mage :: app() -> getRequest() -> setParam( 'files', $argv[2] );
$profile = Mage :: getModel( 'dataflow/profile' );
$userModel = Mage :: getModel( 'admin/user' );
$userModel -> setUserId( 0 );
Mage :: getSingleton( 'admin/session' ) -> setUser( $userModel );

$profile -> load( $argv[1] );
if ( !$profile -> getId() ) {
    Mage :: getSingleton( 'adminhtml/session' ) -> addError( 'ERROR: Incorrect profile id' );
    } 
Mage :: register( 'current_convert_profile', $profile );
$profile -> run();

$recordCount = 0;
$batchModel = Mage :: getSingleton( 'dataflow/batch' );
if ( $batchModel -> getId() ) {
    if ( $batchModel -> getAdapter() ) {
        $batchId = $batchModel -> getId();
        $batchImportModel = $batchModel -> getBatchImportModel();
        $importIds = $batchImportModel -> getIdCollection();
        $batchModel = Mage :: getModel( 'dataflow/batch' ) -> load( $batchId );
        $adapter = Mage :: getModel( $batchModel -> getAdapter() );
        $resource = Mage :: getSingleton( 'core/resource' );
        $customer_group_table = $resource -> getTableName( 'customer_group' );
        $write = $resource -> getConnection( 'catalog_write' );
        foreach ( $profile -> getExceptions() as $e ) {
            printf( $e -> getMessage() . "\n" );
            } 
        $exceptionCount = count( $profile -> getExceptions() );
        foreach ( $importIds as $importId ) {
            $recordCount++;
            try {
                $batchImportModel -> load( $importId );
                if ( !$batchImportModel -> getId() ) {
                    $errors[] = Mage :: helper( 'dataflow' ) -> __( 'WARNING: Skip undefined row' );
                    continue;
                    } 
                $importData = $batchImportModel -> getBatchData();
                try {
                    if ( $importData['group_id'] != '' && !$write -> fetchOne( "select * from $customer_group_table where customer_group_code='" . $importData['group_id'] . "' and tax_class_id=3" ) ) {
                        $write -> query( "insert into $customer_group_table (customer_group_code, tax_class_id) values ('" . $importData['group_id'] . "', 3)" );
                        $write -> commit();
                        } 
                    $adapter -> saveRow( $importData );
                    } 
                catch ( Exception $e ) {
                    printf( "ROW " . $recordCount . ", FIRSTNAME " . $importData['firstname'] . " - " . $e -> getMessage() . "\n" );
                    continue;
                    } 
                if ( $recordCount % 10 == 0 ) {
                    printf( $recordCount . "...\n" );
                    } 
                } 
            catch( Exception $ex ) {
                printf( "ROW " . $recordCount . ", FIRSTNAME " . $importData['firstname'] . " - " . $e -> getMessage() . "\n" );
                } 
            } 
        foreach ( $profile -> getExceptions() as $e ) {
            $exceptionCount > 0 ? $exceptionCount-- : printf( $e -> getMessage() . "\n" );
            } 
        } 
    printf( "Done\n" );
    } 
?>
