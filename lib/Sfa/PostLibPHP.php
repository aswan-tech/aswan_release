<?php

include("EncryptionUtil.php");

class PostLibPHP {

    var $epgURL;
    var $sslURL;
    var $motoURL;
    var $mstrKeyDir;
    var $mstrOSType;
    var $verbose;

    function PostLibPHP() {
        PostLibPHP::readPropFile();
    }

    function readPropFile() {
        if ($this->verbose != null && trim($this->verbose) == "true") {
            error_log("PostLibPHP readPropFile Entered", 0);
        }

        $this->motoURL = "https://payseal.icicibank.com/mpi/Moto.jsp";

        $this->sslURL = "https://payseal.icicibank.com/mpi/Ssl.jsp";

        $this->verbose = "true";


       $this->mstrKeyDir = str_replace("/", "//", Mage::getStoreConfig('payment/payseal_standard/key_path'));
       


        if ($this->mstrKeyDir == '')
            Mage::log('Key file pathe is not available');
        //exit;
        $this->mstrOSType = "NT";


        $this->epgURL = "https://payseal.icicibank.com/mpi/PGServSFA";
    }

    ###################################################################
    # postMoto function is used by MOTO Merchants this function takes
    # the php objects and callthe postMoto function of PHP SFA and return
    # the PGResponse object which contains the PGresponse receieved
    ###################################################################

    function postMoto($aoBTA, $aoSTA, $aoMerchant, $aoMPI, $aoCInfo, $aoReserveData) {

        if ($this->verbose != null && trim($this->verbose) == "true") {
            error_log("PostLibPHP postMoto Entered", 0);
        }

        #PostLibPHP::readPropFile();
        ## Creating Instance of PGResponse.
        #
        #
        ##
        $oPGResphp = new PGResponse();

        ## Mandetory checks for Merchant objects
        #  check for Merchant id and the Message type
        #
        ##
        if ($aoMerchant == null || $aoMerchant == "") {
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("PostLibPHP postMoto Error. Merchant object passed is null or Invalid.Transaction cannot proceed.", 0);
            }
            $oPGResphp->setRespCode("2");
            $oPGResphp->setRespMessage(" Merchant object is null or Invalid.Transaction cannot proceed.");
            return $oPGResphp;
        }

        if ($aoCInfo == null || $aoCInfo == "") {
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("PostLibPHP postMoto Error. Card Info object passed is null or Invalid.Transaction cannot proceed.", 0);
            }
            $oPGResphp->setRespCode("2");
            $oPGResphp->setRespMessage(" Card Info object is null or Invalid.Transaction cannot proceed.");
            return $oPGResphp;
        }

        if ($aoMerchant->getMerchantID() == null || $aoMerchant->getMerchantID() == "") {
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("PostLibPHP postMoto Error. Merchant id is Invalid.Transaction cannot proceed.", 0);
            }
            $oPGResphp->setRespCode("2");
            $oPGResphp->setRespMessage(" Merchant id is null or Invalid.Transaction cannot proceed.");
            return $oPGResphp;
        }
        if ($aoMerchant->getMerchantTxnID() == null || $aoMerchant->getMerchantTxnID() == "") {
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("PostLibPHP postMoto Error. Merchant Transaction id is Invalid.Transaction cannot proceed.", 0);
            }
            $oPGResphp->setRespCode("2");
            $oPGResphp->setRespMessage(" Merchant Transaction id is null or Invalid.Transaction cannot proceed.");
            return $oPGResphp;
        }

        if ($aoMerchant->getMessageType() == null || $aoMerchant->getMessageType() == "") {
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("PostLibPHP postMoto Error. Merchant Type is null or Invalid.Transaction cannot proceed.", 0);
            }
            $oPGResphp->setRespCode("2");
            $oPGResphp->setRespMessage(" Message type is null or Invalid.Transaction cannot proceed.");
            return $oPGResphp;
        }

        $strData = $this->buildMerchantBillShip($aoMerchant, $aoBTA, $aoSTA);
        $oEncryptionUtilenc = new EncryptionUtil();
        $strMotoEncryptedData = $aoMerchant->getMerchantID() . trim($aoMerchant->getMerchantTxnID()) . trim($aoMerchant->getCustIPAddress()) . trim($aoMerchant->getAmount()) . trim($aoMerchant->getMessageType()) . trim($aoMerchant->getCurrCode()) . trim($aoMerchant->getInvoiceNo()) . trim($aoCInfo->getCardNum()) . trim($aoCInfo->getExpDtYr()) . trim($aoCInfo->getExpDtMon()) . $aoCInfo->getCVVNum();
        $sDigest = $oEncryptionUtilenc->getHMAC($strMotoEncryptedData, trim($this->mstrKeyDir) . $aoMerchant->getMerchantID() . '.key', $aoMerchant->getMerchantID());


        if ($sDigest == null || $sDigest == "") {
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("Error in Encrypting/Hashing Merchant Data.Transaction cannot proceed.", 0);
            }
            $oPGResphp->setRespCode("2");
            $oPGResphp->setRespMessage(" Error while encrypting/hashing data. Transaction cannot be processed.");
            return $oPGResphp;
        }
        $strData = $strData . '&EncryptedData=' . $sDigest;
        $strData = $strData . '&IntfVer=ASPV2.0';

        $strData = $strData . '&OsType=' . trim($this->mstrOSType);
        $strData = $strData . '&LanguageType=php';

        $strData = $strData . '&CustIPAddress=' . $aoMerchant->getCustIPAddress();

        $strData = $strData . '&InstrType=' . $aoCInfo->getInstrType();
        $strData = $strData . '&CardType=' . $aoCInfo->getCardType();
        $strData = $strData . '&CardNum=' . $aoCInfo->getCardNum();
        $strData = $strData . '&ExpDtYr=' . $aoCInfo->getExpDtYr();
        $strData = $strData . '&ExpDtMon=' . $aoCInfo->getExpDtMon();
        $strData = $strData . '&CVVNum=' . $aoCInfo->getCVVNum();
        $strData = $strData . '&NameOnCard=' . $aoCInfo->getNameOnCard();

        if ($aoMPI == null || $aoMPI == "") {
            $strData = $strData . '&status=""';
            $strData = $strData . '&cavv=""';
            $strData = $strData . '&eci=""';
            $strData = $strData . '&xid=""';
            $strData = $strData . '&purchaseAmount=""';
            $strData = $strData . '&currencyVal=""';
            $strData = $strData . '&shoppingcontext=""';
        } else {
            $strData = $strData . '&status=' . $aoMPI->getVBVStatus();
            $strData = $strData . '&cavv=' . $aoMPI->getCAVV();
            $strData = $strData . '&eci=' . $aoMPI->getECI();
            $strData = $strData . '&xid=' . $aoMPI->getXID();
            $strData = $strData . '&purchaseAmount=' . $aoMPI->getPurchaseAmount();
            $strData = $strData . '&currencyVal=' . $aoMPI->getCurrencyVal();
            $strData = $strData . '&shoppingcontext=' . $aoMPI->getShoppingContext();
        }

        $strData = $strData . '&Reserve1=' . $aoReserveData->getReserveField1();
        $strData = $strData . '&Reserve2=' . $aoReserveData->getReserveField2();
        $strData = $strData . '&Reserve3=' . $aoReserveData->getReserveField3();
        $strData = $strData . '&Reserve4=' . $aoReserveData->getReserveField4();
        $strData = $strData . '&Reserve5=' . $aoReserveData->getReserveField5();
        $strData = $strData . '&Reserve6=' . $aoReserveData->getReserveField6();
        $strData = $strData . '&Reserve7=' . $aoReserveData->getReserveField7();
        $strData = $strData . '&Reserve8=' . $aoReserveData->getReserveField8();
        $strData = $strData . '&Reserve9=' . $aoReserveData->getReserveField9();
        $strData = $strData . '&Reserve10=' . $aoReserveData->getReserveField10();


        # The function return PGResponse object of php Sfa.
        $retData = $this->postData(trim($this->motoURL), trim($strData));

        if ($retData == null || $retData == "") {
            $oPGResphp->setRespCode("2");
            $oPGResphp->setRespMessage(" No response From Payment Gateway or URL not Found");
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("PostLibPHP postMoto Exiting", 0);
            }
            return $oPGResphp;
        } else {

            $oPGResphp = $oPGResphp->getResponse($retData);
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("PostLibPHP postMoto Exiting", 0);
            }
            return $oPGResphp;
        }
    }

    ###################################################################
    # postSSL function is used by SSL Merchants this function
    # takes the php objects and call the postSSL function of PHP SFA
    # and return the PGResponse object.
    ###################################################################

    function postSSL($aoBTA, $aoSTA, $aoMerchant, $aoMPI, $aoReserveData) {

        if ($this->verbose != null && trim($this->verbose) == "true") {

            error_log("PostLibPHP postSSL Entered", 0);
        }
        $oresponse = null;
        $oPGResphp = new PGResponse();

        ## Mandetory checks for Merchant objects
        #  check for Merchant id and the Message type
        #
        ##

        if ($aoMerchant == null || $aoMerchant == "") {
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("PostLibPHP postSSL Error. Merchant object passed is null or Invalid.Transaction cannot proceed.", 0);
            }
            $oPGResphp->setRespCode("2");
            $oPGResphp->setRespMessage(" Merchant object is null or Invalid.Transaction cannot proceed.");
            return $oPGResphp;
        }

        if ($aoMerchant->getMerchantID() == null || $aoMerchant->getMerchantID() == "") {
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("PostLibPHP postSSL Error. Merchant id is Invalid.Transaction cannot proceed.", 0);
            }
            $oPGResphp->setRespCode("2");
            $oPGResphp->setRespMessage(" Merchant id is null or Invalid.Transaction cannot proceed.");
            return $oPGResphp;
        }
        if ($aoMerchant->getMerchantTxnID() == null || $aoMerchant->getMerchantTxnID() == "") {
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("PostLibPHP postSSL Error. Merchant Transaction id is Invalid.Transaction cannot proceed.", 0);
            }
            $oPGResphp->setRespCode("2");
            $oPGResphp->setRespMessage(" Merchant Transaction id is null or Invalid.Transaction cannot proceed.");
            return $oPGResphp;
        }

        if ($aoMerchant->getMessageType() == null || $aoMerchant->getMessageType() == "") {
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("PostLibPHP postSSL Error. Merchant Type is null or Invalid.Transaction cannot proceed.", 0);
            }
            $oPGResphp->setRespCode("2");
            $oPGResphp->setRespMessage(" Message type is null or Invalid.Transaction cannot proceed.");
            return $oPGResphp;
        }

        $strData = $this->buildMerchantBillShip($aoMerchant, $aoBTA, $aoSTA);
        $oEncryptionUtilenc = new EncryptionUtil();
        $strSslEncryptedData = $aoMerchant->getMerchantID() . trim($aoMerchant->getMerchantTxnID()) . trim($aoMerchant->getAmount()) . trim($aoMerchant->getMessageType()) . trim($aoMerchant->getCurrCode()) . trim($aoMerchant->getInvoiceNo());
        $sDigest = $oEncryptionUtilenc->getHMAC($strSslEncryptedData, trim($this->mstrKeyDir) . $aoMerchant->getMerchantID() . '.key', $aoMerchant->getMerchantID());


        if ($sDigest == null || $sDigest == "") {
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("Error in Encrypting/Hashing Merchant Data.Transaction cannot proceed.", 0);
            }
            $oPGResphp->setRespCode("2");
            $oPGResphp->setRespMessage(" Error while encrypting/hashing data. Transaction cannot be processed.");
            return $oPGResphp;
        }

        $strData = $strData . '&EncryptedData=' . $sDigest;
        $strData = $strData . '&IntfVer=ASPV2.0';

        $strData = $strData . '&OsType=' . trim($this->mstrOSType);
        $strData = $strData . '&LanguageType=php';


        #MPI Details
        if ($aoMPI == null || $aoMPI == "") {
            $strData = $strData . '&WhatIUse=""';
            $strData = $strData . '&AcceptHdr=""';
            $strData = $strData . '&UserAgent=""';
            $strData = $strData . '&CurrencyVal=""';
            $strData = $strData . '&Exponent=""';
            $strData = $strData . '&RecurFreq=""';
            $strData = $strData . '&RecurEnd=""';
            $strData = $strData . '&Installment=""';
            $strData = $strData . '&DeviceCategory=""';
            $strData = $strData . '&OrderDesc=""';
            $strData = $strData . '&PurchaseAmount=""';
            $strData = $strData . '&DisplayAmount=""';
        } else {
            $strData = $strData . '&WhatIUse=' . $aoMPI->getWhatIUse();
            $strData = $strData . '&AcceptHdr=' . $aoMPI->getAcceptHdr();
            $strData = $strData . '&UserAgent=' . $aoMPI->getAgentHdr();
            $strData = $strData . '&CurrencyVal=' . $aoMPI->getCurrencyVal();
            $strData = $strData . '&Exponent=' . $aoMPI->getExponent();
            $strData = $strData . '&RecurFreq=' . $aoMPI->getRecurFreq();
            $strData = $strData . '&RecurEnd=' . $aoMPI->getRecurEnd();
            $strData = $strData . '&Installment=' . $aoMPI->getInstallment();
            $strData = $strData . '&DeviceCategory=' . $aoMPI->getDeviceCategory();
            $strData = $strData . '&OrderDesc=' . $aoMPI->getOrderDesc();
            $strData = $strData . '&PurchaseAmount=' . $aoMPI->getPurchaseAmount();
            $strData = $strData . '&DisplayAmount=' . $aoMPI->getDisplayAmount();
        }

        $strData = $strData . '&Reserve1=' . $aoReserveData->getReserveField1();
        $strData = $strData . '&Reserve2=' . $aoReserveData->getReserveField2();
        $strData = $strData . '&Reserve3=' . $aoReserveData->getReserveField3();
        $strData = $strData . '&Reserve4=' . $aoReserveData->getReserveField4();
        $strData = $strData . '&Reserve5=' . $aoReserveData->getReserveField5();
        $strData = $strData . '&Reserve6=' . $aoReserveData->getReserveField6();
        $strData = $strData . '&Reserve7=' . $aoReserveData->getReserveField7();
        $strData = $strData . '&Reserve8=' . $aoReserveData->getReserveField8();
        $strData = $strData . '&Reserve9=' . $aoReserveData->getReserveField9();
        $strData = $strData . '&Reserve10=' . $aoReserveData->getReserveField10();


        # The function return PGResponse object of php Sfa.

        $retData = $this->postData(trim($this->sslURL), trim($strData));

        if ($retData == null || $retData == "") {
            $oPGResphp->setRespCode("2");
            $oPGResphp->setRespMessage(" No response From Payment Gateway or URL not Found");
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("PostLibPHP postSSL Exiting", 0);
            }
            return $oPGResphp;
        } else {
            $oPGResphp = $oPGResphp->getResponse($retData);
            if ($oPGResphp->getRedirectionTxnId() != null || $oPGResphp->getRedirectionTxnId() != "") {
                $oPGResphp->setRedirectionUrl(trim($this->sslURL) . '?txnId=' . trim($oPGResphp->getRedirectionTxnId()));
            }
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("PostLibPHP postSSL Exiting", 0);
            }
            return $oPGResphp;
        }
    }

    ##########################################################################
    # postRelatedTxn function is used for doing Online Related transaction
    # it takes merchant object of php and call the postRelated function of php sfa
    # and this function return PGResponse object which contains the response receieved
    # from php SFA
    ##########################################################################

    function postRelatedTxn($aoMerchant) {

        if ($this->verbose != null && trim($this->verbose) == "true") {
            error_log("PostLibPHP postRelatedTxn Entered", 0);
        }

        $oPGResphp = new PGResponse();

        ## Mandetory checks for Merchant and Merchant object containing
        #  checks for Merchant id , Root system Reference number
        #  Message type  in case of the related transaction.
        ##

        if ($aoMerchant == null || $aoMerchant == "") {
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("PostLibPHP postRelatedTxn Error. Merchant object passed is null or Invalid.Transaction cannot proceed.", 0);
            }
            $oPGResphp->setRespCode("2");
            $oPGResphp->setRespMessage(" Merchant object is null or Invalid.Transaction cannot proceed.");
            return $oPGResphp;
        }

        if ($aoMerchant->getMerchantID() == null || $aoMerchant->getMerchantID() == "") {
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("PostLibPHP postRelatedTxn Error. Merchant id is Invalid.Transaction cannot proceed.", 0);
            }
            $oPGResphp->setRespCode("2");
            $oPGResphp->setRespMessage(" Merchant id is null or Invalid.Transaction cannot proceed.");
            return $oPGResphp;
        }
        if ($aoMerchant->getMerchantTxnID() == null || $aoMerchant->getMerchantTxnID() == "") {
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("PostLibPHP postRelatedTxn Error. Merchant Transaction id is Invalid.Transaction cannot proceed.", 0);
            }
            $oPGResphp->setRespCode("2");
            $oPGResphp->setRespMessage(" Merchant Transaction id is null or Invalid.Transaction cannot proceed.");
            return $oPGResphp;
        }

        if ($aoMerchant->getRootTxnSysRefNum() == null || $aoMerchant->getRootTxnSysRefNum() == "") {
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("PostLibPHP postRelatedTxn Error. Merchant Root System Ref No is Invalid.Transaction cannot proceed.", 0);
            }
            $oPGResphp->setRespCode("2");
            $oPGResphp->setRespMessage(" Merchant Root System Ref No is Invalid.Transaction cannot proceed.");
            return $oPGResphp;
        }

        if ($aoMerchant->getMessageType() == null || $aoMerchant->getMessageType() == "") {
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("PostLibPHP postRelatedTxn Error. Merchant Type is null or Invalid.Transaction cannot proceed.", 0);
            }
            $oPGResphp->setRespCode("2");
            $oPGResphp->setRespMessage(" Message type is null or Invalid.Transaction cannot proceed.");
            return $oPGResphp;
        }

        $strData = $this->buildMerchantRelatedTxn($aoMerchant);
        $oEncryptionUtilenc = new EncryptionUtil();
        $strEncryptedData = trim($aoMerchant->getMerchantID()) . trim($aoMerchant->getMerchantTxnID()) . trim($aoMerchant->getAmount()) . trim($aoMerchant->getMessageType()) . trim($aoMerchant->getRootTxnSysRefNum()) . trim($aoMerchant->getRootPNRefNum()) . trim($aoMerchant->getRootAuthCode());
        $sDigest = $oEncryptionUtilenc->getHMAC($strEncryptedData, trim($this->mstrKeyDir) . $aoMerchant->getMerchantID() . '.key', $aoMerchant->getMerchantID());


        if ($sDigest == null || $sDigest == "") {
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("Error in Encrypting/Hashing Merchant Data.Transaction cannot proceed.", 0);
            }
            $oPGResphp->setRespCode("2");
            $oPGResphp->setRespMessage(" Error while encrypting/hashing data. Transaction cannot be processed.");
            return $oPGResphp;
        }

        $strData = $strData . '&EncryptedData=' . $sDigest;
        $strData = $strData . '&IntfVer=ASPV2.0';

        $strData = $strData . '&OsType=' . trim($this->mstrOSType);
        $strData = $strData . '&LanguageType=php';

        $retData = $this->postData(trim($this->epgURL), trim($strData));

        # The function return PGResponse object of php Sfa.
        if ($retData == null || $retData == "") {
            $oPGResphp->setRespCode("2");
            $oPGResphp->setRespMessage(" No response From Payment Gateway or URL not Found");
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("PostLibPHP postRelatedTxn Exiting", 0);
            }
            return $oPGResphp;
        } else {

            $oPGResphp = $oPGResphp->getResponse($retData);
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("PostLibPHP postRelatedTxn Exiting", 0);
            }
            return $oPGResphp;
        }
    }

    ###########################################################################
    # postTxnSearch function is used for Online search of Transactions
    # it takes merchant object of php and and this function return PGResponse
    # object which contains the response receieved
    ############################################################################

    function postTxnSearch($aoMerchant) {
        if ($this->verbose != null && trim($this->verbose) == "true") {
            error_log("PostLibPHP postTxnSearch Entered", 0);
        }

        $oPGSearchResphp = new PGSearchResponse();


        ## Mandetory checks for Merchant and Merchant object containing
        #  checks for Merchant id , Transaction Start Date and Transaction End Date
        #
        ##


        if ($aoMerchant == null || $aoMerchant == "") {
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("PostLibPHP postTxnSearch Error. Merchant object passed is null or Invalid.Transaction cannot proceed.", 0);
            }
            $oPGSearchResphp->setRespCode("2");
            $oPGSearchResphp->setRespMessage(" Merchant object passed is null or Invalid.Transaction cannot proceed.");
            return $oPGSearchResphp;
        }

        if ($aoMerchant->getMerchantID() == null || $aoMerchant->getMerchantID() == "") {
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("PostLibPHP postTxnSearch Error. Merchant id is Invalid.Transaction cannot proceed.", 0);
            }
            $oPGSearchResphp->setRespCode("2");
            $oPGSearchResphp->setRespMessage(" Merchant id is Invalid.Transaction cannot proceed.");
            return $oPGSearchResphp;
        }

        if ($aoMerchant->getStartDate() == null || $aoMerchant->getStartDate() == "") {
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("PostLibPHP postTxnSearch Error. Transaction Start Date is Invalid.Transaction cannot proceed.", 0);
            }
            $oPGSearchResphp->setRespCode("2");
            $oPGSearchResphp->setRespMessage(" Transaction Start Date is Invalid.Transaction cannot proceed.");
            return $oPGSearchResphp;
        }

        if ($aoMerchant->getEndDate() == null || $aoMerchant->getEndDate() == "") {
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("PostLibPHP postTxnSearch Error. Transaction End Date is Invalid.Transaction cannot proceed.", 0);
            }
            $oPGSearchResphp->setRespCode("2");
            $oPGSearchResphp->setRespMessage(" Transaction End Date is Invalid.Transaction cannot proceed.");
            return $oPGSearchResphp;
        }

        $strData = "";
        # Merchant details
        $strData = $strData . 'MerchantID=' . $aoMerchant->getMerchantID();
        $strData = $strData . '&StartDate=' . $aoMerchant->getStartDate();
        $strData = $strData . '&EndDate=' . $aoMerchant->getEndDate();


        $oEncryptionUtilenc = new EncryptionUtil();
        $strEncryptedData = trim($aoMerchant->getMerchantID()) . trim($aoMerchant->getStartDate()) . trim($aoMerchant->getEndDate());
        $sDigest = $oEncryptionUtilenc->getHMAC($strEncryptedData, trim($this->mstrKeyDir) . $aoMerchant->getMerchantID() . '.key', $aoMerchant->getMerchantID());


        if ($sDigest == null || $sDigest == "") {
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("Error in Encrypting/Hashing Merchant Data.Transaction cannot proceed.", 0);
            }
            $oPGSearchResphp->setRespCode("2");
            $oPGSearchResphp->setRespMessage(" Error while encrypting/hashing data. Transaction cannot be processed.");
            return $oPGSearchResphp;
        }

        $strData = $strData . '&EncryptedData=' . $sDigest;
        $strData = $strData . '&IntfVer=ASPV2.0';

        $strData = $strData . '&OsType=' . trim($this->mstrOSType);
        $strData = $strData . '&LanguageType=php';
        $strData = $strData . '&RequestType=SFATxnSearch';


        $retData = $this->postData(trim($this->epgURL), trim($strData));

        # The function return PGResponse object of php Sfa.
        if ($retData == null || $retData == "") {
            $oPGSearchResphp->setRespCode("2");
            $oPGSearchResphp->setRespMessage(" No response From Payment Gateway or URL not Found");
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("PostLibPHP postTxnSearch Exiting", 0);
            }
            return $oPGSearchResphp;
        } else {
            $oPGSearchResphp = $oPGSearchResphp->getResponse($retData);
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("PostLibPHP postTxnSearch Exiting", 0);
            }
            return $oPGSearchResphp;
        }
    }

    ################################################################################
    # postStatusInq function is used for Online Inquiry of particular Transactions
    # it takes merchant object of php and call the postStatusInquiry function of php sfa
    # and this function return PGResponse object
    ################################################################################

    function postStatusInq($aoMerchant) {

        if ($this->verbose != null && trim($this->verbose) == "true") {
            error_log("PostLibPHP postStatusInq Entered", 0);
        }

        $oPGSearchResphp = new PGSearchResponse();

        # Mandetory checks for Merchant and Merchant object containing
        # checks for Merchant id  and and merchant transaction ID
        #
        #
        if ($aoMerchant == null || $aoMerchant == "") {
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("PostLibPHP postStatusInq Error. Merchant object passed is null or Invalid.Transaction cannot proceed.", 0);
            }
            $oPGSearchResphp->setRespCode("2");
            $oPGSearchResphp->setRespMessage("Merchant object passed is null or Invalid.Transaction cannot proceed.");
            return $oPGSearchResphp;
        }
        if ($aoMerchant->getMerchantID() == null || $aoMerchant->getMerchantID() == "") {
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("PostLibPHP postStatusInq Error. Merchant id is Invalid.Transaction cannot proceed.", 0);
            }
            $oPGSearchResphp->setRespCode("2");
            $oPGSearchResphp->setRespMessage("Merchant id is Invalid.Transaction cannot proceed.");
            return $oPGSearchResphp;
        }

        if ($aoMerchant->getMerchantTxnID() == null || $aoMerchant->getMerchantTxnID() == "") {
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("PostLibPHP postStatusInq Error. Merchant Transaction id is Invalid.Transaction cannot proceed.", 0);
            }
            $oPGSearchResphp->setRespCode("2");
            $oPGSearchResphp->setRespMessage("Merchant Transaction id is Invalid.Transaction cannot proceed.");
            return $oPGSearchResphp;
        }


        $strData = "";
        # Merchant details
        $strData = $strData . 'MerchantID=' . $aoMerchant->getMerchantID();
        $strData = $strData . '&MerchantTxnID=' . $aoMerchant->getMerchantTxnID();


        $oEncryptionUtilenc = new EncryptionUtil();
        $strEncryptedData = trim($aoMerchant->getMerchantID()) . trim($aoMerchant->getMerchantTxnID());
        $sDigest = $oEncryptionUtilenc->getHMAC($strEncryptedData, trim($this->mstrKeyDir) . $aoMerchant->getMerchantID() . '.key', $aoMerchant->getMerchantID());


        if ($sDigest == null || $sDigest == "") {
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("Error in Encrypting/Hashing Merchant Data.Transaction cannot proceed.", 0);
            }
            $oPGSearchResphp->setRespCode("2");
            $oPGSearchResphp->setRespMessage(" Error while encrypting/hashing data. Transaction cannot be processed.");
            return $oPGSearchResphp;
        }

        $strData = $strData . '&EncryptedData=' . $sDigest;
        $strData = $strData . '&IntfVer=ASPV2.0';

        $strData = $strData . '&OsType=' . trim($this->mstrOSType);
        $strData = $strData . '&LanguageType=php';
        $strData = $strData . '&RequestType=SFAStatusInquiry';


        $retData = $this->postData(trim($this->epgURL), trim($strData));

        # The function return PGResponse object of php Sfa.
        if ($retData == null || $retData == "") {
            $oPGSearchResphp->setRespCode("2");
            $oPGSearchResphp->setRespMessage(" No response From Payment Gateway or URL not Found");
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("PostLibPHP postStatusInq Exiting", 0);
            }
            return $oPGSearchResphp;
        } else {
            $oPGSearchResphp = $oPGSearchResphp->getResponse($retData);
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("PostLibPHP postStatusInq Exiting", 0);
            }
            return $oPGSearchResphp;
        }
    }

    Function postData($astrUrlToPostTo, $astrDataToPost) {
        if ($this->verbose != null && trim($this->verbose) == "true") {
            error_log("PostLibPHP postData Entered.", 0);
        }

        if (!extension_loaded('curl')) {
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("PostLibPHP postData curl extension not installed/ not loaded!.", 0);
            }
            print "curl extension not installed/ not loaded!";
            exit;
        }
        // Initialisation
        $ch = curl_init();
        // Set parameters
        curl_setopt($ch, CURLOPT_URL, $astrUrlToPostTo);

        // Request
        curl_setopt($ch, CURLOPT_POSTFIELDS, $astrDataToPost);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_CAINFO, getcwd() . "/lib/Sfa/cacert.pem");
        

        // execute the connexion
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            if ($this->verbose != null && trim($this->verbose) == "true") {
                error_log("PostLibPHP postData " . curl_error($ch) . " ( " . curl_errno($ch) . " )", 0);
            }
        }
        // Close it
        curl_close($ch);
        if ($this->verbose != null && trim($this->verbose) == "true") {
            error_log("PostLibPHP postData Exiting.", 0);
        }

        return $result;
    }

    Function buildMerchantBillShip($aoMerchant, $aoBTA, $aoSTA) {
        $strData = "";
        # Merchant details
        $strData = $strData . 'MerchantID=' . $aoMerchant->getMerchantID();
        $strData = $strData . '&Vendor=' . $aoMerchant->getVendor();
        $strData = $strData . '&Partner=' . $aoMerchant->getPartner();
        $strData = $strData . '&OrdRefNo=' . $aoMerchant->getOrderReferenceNo();
        $strData = $strData . '&MerchantTxnID=' . $aoMerchant->getMerchantTxnID();
        $strData = $strData . '&MessageType=' . $aoMerchant->getMessageType();
        $strData = $strData . '&InvoiceNo=' . $aoMerchant->getInvoiceNo();
        $strData = $strData . '&InvoiceDate=' . $aoMerchant->getInvoiceDate();
        $strData = $strData . '&CurrCode=' . $aoMerchant->getCurrCode();
        $strData = $strData . '&GMTOffset=' . $aoMerchant->getGMTTimeOffset();
        $strData = $strData . '&RespMethod=' . $aoMerchant->getRespMethod();
        $strData = $strData . '&RespURL=' . $aoMerchant->getRespURL();
        $strData = $strData . '&Amount=' . $aoMerchant->getAmount();
        $strData = $strData . '&Ext1=' . $aoMerchant->getExt1();
        $strData = $strData . '&Ext2=' . $aoMerchant->getExt2();
        $strData = $strData . '&Ext3=' . $aoMerchant->getExt3();
        $strData = $strData . '&Ext4=' . $aoMerchant->getExt4();
        $strData = $strData . '&Ext5=' . $aoMerchant->getExt5();
        $strData = $strData . '&MrtIpAddr=' . $aoMerchant->getMrtIPAddress();


        #Bill To Address

        if ($aoBTA != null || $aoBTA != "") {
            $strData = $strData . '&CustomerId=' . $aoBTA->getCustomerId();
            $strData = $strData . '&CustomerName=' . $aoBTA->getName();
            $strData = $strData . '&BillAddrLine1=' . $aoBTA->getAddrLine1();
            $strData = $strData . '&BillAddrLine2=' . $aoBTA->getAddrLine2();
            $strData = $strData . '&BillAddrLine3=' . $aoBTA->getAddrLine3();
            $strData = $strData . '&BillCity=' . $aoBTA->getCity();
            $strData = $strData . '&BillState=' . $aoBTA->getState();
            $strData = $strData . '&BillZip=' . $aoBTA->getZip();
            $strData = $strData . '&BillCountryAlphaCode=' . $aoBTA->getCountryAlphaCode();
            $strData = $strData . '&BillEmail=' . $aoBTA->getEmail();
        } else {
            $strData = $strData . '&CustomerId=""';
            $strData = $strData . '&CustomerName=""';
            $strData = $strData . '&BillAddrLine1=""';
            $strData = $strData . '&BillAddrLine2=""';
            $strData = $strData . '&BillAddrLine3=""';
            $strData = $strData . '&BillCity=""';
            $strData = $strData . '&BillState=""';
            $strData = $strData . '&BillZip=""';
            $strData = $strData . '&BillCountryAlphaCode=""';
            $strData = $strData . '&BillEmail=""';
        }


        # Ship To Address
        if ($aoSTA != null || $aoSTA != "") {
            $strData = $strData . '&ShipAddrLine1=' . $aoSTA->getAddrLine1();
            $strData = $strData . '&ShipAddrLine2=' . $aoSTA->getAddrLine2();
            $strData = $strData . '&ShipAddrLine3=' . $aoSTA->getAddrLine3();
            $strData = $strData . '&ShipCity=' . $aoSTA->getCity();
            $strData = $strData . '&ShipState=' . $aoSTA->getState();
            $strData = $strData . '&ShipZip=' . $aoSTA->getZip();
            $strData = $strData . '&ShipCountryAlphaCode=' . $aoSTA->getCountryAlphaCode();
            $strData = $strData . '&ShipEmail=' . $aoSTA->getEmail();
        } else {
            $strData = $strData . '&ShipAddrLine1=""';
            $strData = $strData . '&ShipAddrLine2=""';
            $strData = $strData . '&ShipAddrLine3=""';
            $strData = $strData . '&ShipCity=""';
            $strData = $strData . '&ShipState=""';
            $strData = $strData . '&ShipZip=""';
            $strData = $strData . '&ShipCountryAlphaCode=""';
            $strData = $strData . '&ShipEmail=""';
        }


        return $strData;
    }

    Function buildMerchantRelatedTxn($aoMerchant) {
        $strData = "";

        #Merchant details
        $strData = $strData . 'MerchantID=' . $aoMerchant->getMerchantID();
        $strData = $strData . '&Vendor=' . $aoMerchant->getVendor();
        $strData = $strData . '&Partner=' . $aoMerchant->getPartner();
        $strData = $strData . '&RespURL=' . $aoMerchant->getRespURL();
        $strData = $strData . '&RespMethod=' . $aoMerchant->getRespMethod();
        $strData = $strData . '&CurrCode=' . $aoMerchant->getCurrCode();
        $strData = $strData . '&RootTxnSysRefNum=' . $aoMerchant->getRootTxnSysRefNum();
        $strData = $strData . '&RootPNRefNum=' . $aoMerchant->getRootPNRefNum();
        $strData = $strData . '&RootAuthCode=' . $aoMerchant->getRootAuthCode();
        $strData = $strData . '&MessageType=' . $aoMerchant->getMessageType();
        $strData = $strData . '&Amount=' . $aoMerchant->getAmount();
        $strData = $strData . '&Ext1=' . $aoMerchant->getExt1();
        $strData = $strData . '&Ext2=' . $aoMerchant->getExt2();
        $strData = $strData . '&Ext3=' . $aoMerchant->getExt3();
        $strData = $strData . '&Ext4=' . $aoMerchant->getExt4();
        $strData = $strData . '&Ext5=' . $aoMerchant->getExt5();
        $strData = $strData . '&MrtIpAddr=' . $aoMerchant->getMrtIPAddress();
        $strData = $strData . '&MerchantTxnID=' . $aoMerchant->getMerchantTxnID();
        $strData = $strData . '&GMTOffset=' . $aoMerchant->getGMTTimeOffset();
        $strData = $strData . '&RequestType=RelatedTxn';

        return $strData;
    }

}
?>


