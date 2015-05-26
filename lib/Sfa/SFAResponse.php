<?php

include("Sfa/EncryptionUtil.php");

$mconfig = Mage::getSingleton('payseal/config');
$sMerchantId = trim($mconfig->getMerchantId());
$SkeyPath = str_replace("/", "//", $mconfig->getKeyPath()) . $sMerchantId . ".key";
$strMerchantId = $sMerchantId;
$astrFileName = $SkeyPath;
$astrClearData;
$ResponseCode = "";
$Message = "";
$TxnID = "";
$ePGTxnID = "";
$AuthIdCode = "";
$RRN = "";
$CVRespCode = "";
$Reserve1 = "";
$Reserve2 = "";
$Reserve3 = "";
$Reserve4 = "";
$Reserve5 = "";
$Reserve6 = "";
$Reserve7 = "";
$Reserve8 = "";
$Reserve9 = "";
$Reserve10 = "";


if ($_POST) {

    if ($_POST['DATA'] == null) {
        print "null is the value";
    }
    $astrResponseData = $_POST['DATA'];
    $astrDigest = $_POST['EncryptedData'];
    $oEncryptionUtilenc = new EncryptionUtil();
    $astrsfaDigest = $oEncryptionUtilenc->getHMAC($astrResponseData, $astrFileName, $strMerchantId);

    if (strcasecmp($astrDigest, $astrsfaDigest) == 0) {
        parse_str($astrResponseData, $output);
        if (array_key_exists('RespCode', $output) == 1) {
            $ResponseCode = $output['RespCode'];
        }
        if (array_key_exists('Message', $output) == 1) {
            $Message = $output['Message'];
        }
        if (array_key_exists('TxnID', $output) == 1) {
            $TxnID = $output['TxnID'];
        }
        if (array_key_exists('ePGTxnID', $output) == 1) {
            $ePGTxnID = $output['ePGTxnID'];
        }
        if (array_key_exists('AuthIdCode', $output) == 1) {
            $AuthIdCode = $output['AuthIdCode'];
        }
        if (array_key_exists('RRN', $output) == 1) {
            $RRN = $output['RRN'];
        }
        if (array_key_exists('CVRespCode', $output) == 1) {
            $CVRespCode = $output['CVRespCode'];
        }
    }
}
print "<h6>Response Code:: $ResponseCode <br>";
print "<h6>Response Message:: $Message <br>";
print "<h6>Auth ID Code:: $AuthIdCode <br>";
print "<h6>RRN:: $RRN<br>";
print "<h6>Transaction id:: $TxnID<br>";
print "<h6>Epg Transaction ID:: $ePGTxnID<br>";
print "<h6>CV Response Code:: $CVRespCode<br>";
?>