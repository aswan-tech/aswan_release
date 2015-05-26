<?php
/**
 * Product:     Individual Promotions for Magento Enterpise Edition
 * Package:     Aitoc_Aitindividpromo_10.0.7_574525
 * Purchase ID: UjgdLvjpFE0u1HHQEOk2KNCXazbZ9kQjUnTtO4dMb0
 * Generated:   2013-05-13 06:35:45
 * File path:   app/code/local/Aitoc/Aitindividpromo/Block/Customers.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitindividpromo')){ TUDeZrDeahhUsZri('794d3883d83c739f1c205203d6fc14ba'); ?><?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc.
 */

class Aitoc_Aitindividpromo_Block_Customers extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('customerGrid');
		
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
		
        $this->setDefaultSort('entity_id');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('group_id');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('customer')->__('ID'),
            'width'     => '50px',
            'index'     => 'entity_id',
            'id'     => 'entity_id',
            'type'  => 'number',
        ));

        $this->addColumn('fullname', array(
            'header'    => Mage::helper('customer')->__('Name'),
            'index'     => 'name',
            'escape'    => true,
        ));
        $this->addColumn('email', array(
            'header'    => Mage::helper('customer')->__('Email'),
            'width'     => '200',
            'index'     => 'email'
        ));

        $groups = Mage::getResourceModel('customer/group_collection')
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();

        $this->addColumn('group', array(
            'header'    =>  Mage::helper('customer')->__('Group'),
            'width'     =>  '150',
            'index'     =>  'group_id',
            'type'      =>  'options',
            'options'   =>  $groups,
        ));

        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('customer')->__('Action'),
                'width'     => '150',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('customer')->__('Add'),
                        'url'       => array('base'=> 'norouteexist'),
                        'field'     => 'id',
                        'onclick'   => 'addCustomer(this); return false;',
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

        return parent::_prepareColumns();
    }
	
	protected function _prepareLayout()
	{
		//$this->unsetChild('reset_filter_button');
		//$this->unsetChild('search_button');
		$thisId	=	$this->getRequest()->getParam('id');
		
		$this->setChild('save_button',
			$this->getLayout()->createBlock('adminhtml/widget_button')
				->setData(array(
					'label'     => Mage::helper('adminhtml')->__('Add Customers'),
					'onclick'   => 'massAdd();',
					'class' => 'add',
				))
		);
		/*
		$this->setChild('email_button',
			$this->getLayout()->createBlock('adminhtml/widget_button')
				->setData(array(
					'label'     => Mage::helper('adminhtml')->__('Send Mail to Customers'),
					//'onclick'   => "setLocation('".$this->getUrl('common/index/sendmail/id/'.$thisId, array('_current'=>true))."')",
					'onclick'   => "setLocation('".$this->getUrl('aitindividpromo/index/sendmail/id/'.$thisId, array('_current'=>true))."')",
					'class' => 'save',
				))
		);
		*/
		return parent::_prepareLayout();
	}
	
	public function  getSearchButtonHtml()
    {
        return parent::getSearchButtonHtml() . $this->getChildHtml('save_button') . $this->getChildHtml('email_button');
    }
	
	protected function _prepareMassaction()
    {
        $this->setMassactionIdField('customer_id');
        $this->getMassactionBlock()->setFormFieldName('customer');
		
        $this->getMassactionBlock()->addItem('add', array(
             'label'    => Mage::helper('customer')->__('Add Selected'),
			 'onclick'  => "massAdd();"
             //'url'      => $this->getUrl('*/*/customers'),
             //'confirm'  => Mage::helper('customer')->__('Are you sure?')
        ));
		
        return $this;
    }
	
    public function getCustomers()
    {
        $collection = $this->getCollection();
        $array = array();
        foreach ($collection->load() as $aCustomer)
        {
            $sCustomerFullName = $aCustomer->getName() . ' (' . $aCustomer->getEmail() .  ')';
            $array[$aCustomer->getId()] = $sCustomerFullName;
        }
        return $array;
    }

    protected function _toHtml()
    {
        $sMainHtml = parent::_toHtml();
        //$collection = $this->getCollection();
		
		$collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('group_id');
		
		//$collection = Mage::getModel('customer/customer')->getCollection();
		//print '<br>==='.$collection123->getSelect();
		
        $users = array();
        foreach ($collection->load() as $aCustomer)
        {
            $sCustomerFullName = $aCustomer->getName() . ' (' . $aCustomer->getEmail() .  ')';            
            $users[$aCustomer->getId()] = $sCustomerFullName;
        }
		
		
        $sAppendHtml = '
            <script type="text/javascript">
            aSearchCustomerHash = ' . json_encode($users) . ';
        ';

        $sAppendHtml .= '
			//return customerGrid_massactionJsObject.selectAll()

$("customerGrid_massaction-form").hide();	//Hide MassAction dropdown and button
function massAdd(){
	//alert(customerGrid_massactionJsObject.getCheckboxesValues());
	//alert(customerGrid_massactionJsObject.getGridIds());
	
	var result = [];
	var testCntr = 0;
	
	var rowIDArr = [];
	var rowIDs = customerGrid_massactionJsObject.checkedString;
	rowIDArr = rowIDs.split(",");
	
	//alert(customerGrid_massactionJsObject.checkedString);
	if(customerGrid_massactionJsObject.checkedString != ""){
		rowIDArr.each(function(elem) {
			//alert(aSearchCustomerHash[elem]);
			if (elem && aSearchCustomerHash[elem] && !aCustomerNameHash[elem]){
				aCustomerIdsHash[iCounter] = elem;
				aCustomerNameHash[elem] = aSearchCustomerHash[elem];
				iCounter++;
			}
			populateCustomers();
			//testCntr++;
		});
	}else{
		alert("Please select items.");
	}
	//alert(testCntr);
}

function addCustomer(oLink)
{
    if (!oLink) return false;

    var sHref = oLink.href;

    var aParts = sHref.split("/");

    var sCustomerId = 0;

    if (aParts)
    {
        for (i=0;i<=aParts.length;i++)
        {
            if (aParts[i] == "id")
            {
                sCustomerId = aParts[i + 1];
            }
        }
    }

    if (sCustomerId && aSearchCustomerHash[sCustomerId] && !aCustomerNameHash[sCustomerId])
    {
        aCustomerIdsHash[iCounter] = sCustomerId;
        aCustomerNameHash[sCustomerId] = aSearchCustomerHash[sCustomerId];
        iCounter++;
    }

    populateCustomers();
}';
        $sAppendHtml .= "
customerGridJsObject.reload = function (url)
{
        if (!this.reloadParams) {
            this.reloadParams = {form_key: FORM_KEY};
        }
        else {
            this.reloadParams.form_key = FORM_KEY;
        }
        url = url || this.url;
        if(this.useAjax){
            new Ajax.Request(url + (url.match(new RegExp('\\\\?')) ? '&ajax=true' : '?ajax=true' ), {
                loaderArea: this.containerId,
                parameters: this.reloadParams || {},
                evalScripts: true,
                onFailure: this._processFailure.bind(this),
                onComplete: this.initGrid.bind(this),
                onSuccess: function(transport) {
                    try {
                        if (transport.responseText.isJSON()) {
                        	var response = eval('(' + transport.responseText + ')');
                            $(this.containerId).update(response.html);
                            aSearchCustomerHash = response.aSearchCustomerHash;
                            var response = transport.responseText.evalJSON();
                        	if (response.error) {
                                alert(response.message);
                            }
                            if(response.ajaxExpired && response.ajaxRedirect) {
                                setLocation(response.ajaxRedirect);
                            }
                        } else {
                            $(this.containerId).update(transport.responseText);
                        }
                    }
                    catch (e) {
                        $(this.containerId).update(transport.responseText);
                    }
                }.bind(this)
            });
            return;
        }
        else{
            if(this.reloadParams){
                \$H(this.reloadParams).each(function(pair){
                    url = this.addVarToUrl(pair.key, pair.value);
                }.bind(this));
            }
            location.href = url;
        }
}
</script>";

        return $sMainHtml . $sAppendHtml;
    }

    public function getGridUrl()
    {
        return $this->getUrl('aitindividpromo/index/customers', array('_current'=> true));
    }
	
    public function getRowUrl($row)
    {
        return '';
    }
} } 