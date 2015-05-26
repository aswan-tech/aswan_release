
/**
 * Product:     Loyalty Program for Enterprise Edition
 * Package:     Aitoc_Aitloyalty_10.0.10_574534
 * Purchase ID: n/a
 * Generated:   2013-05-13 06:36:55
 * File path:   js/aitoc/aitloyalty/aitloyalty.js
 * Copyright:   (c) 2013 AITOC, Inc.
 */
function aitloyalty_ActionOnChange()
{
    var sValue = document.getElementById('rule_simple_action').value;
    if ('by_percent_surcharge' == sValue || 'by_fixed_surcharge' == sValue)
    {
        
        document.getElementById('rule_discount_step').value = 0;
        document.getElementById('rule_discount_step').disabled = true;
    } else 
    {
        
        document.getElementById('rule_discount_step').disabled = false;
    }
}
Event.observe(document.getElementById('rule_simple_action'), 'change', aitloyalty_ActionOnChange.bindAsEventListener());
Event.observe(window, 'load', aitloyalty_ActionOnChange.bindAsEventListener());


// new script

    function aitloyalty_ActionOnRuleDisplayChange()
    {
        var oTrCoupon   = $('rule_aitloyalty_customer_display_coupon').up(1);
        var oTrTitle    = $('aitloyalty_customer_display_title').up(1);
        var oInpReq     = $('aitloyalty_customer_display_titles_1');
            
        if ($('rule_aitloyalty_customer_display_enable').value == 1)
        {
            oTrCoupon.show();
            oTrTitle.show();
            
            oInpReq.removeClassName('ignore-validate');
        }
        else
        {
            oTrCoupon.hide();
            oTrTitle.hide();
            
            oInpReq.addClassName('ignore-validate');
        }
        return true;
    }