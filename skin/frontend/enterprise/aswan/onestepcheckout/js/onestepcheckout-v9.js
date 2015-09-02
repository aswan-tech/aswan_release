

/* Login popup class */
var OneStepCheckoutLoginPopup = Class.create({
    initialize: function(options) {
        this.options = options;
        this.popup_container = $('onestepcheckout-login-popup');
        this.popup_link = $('onestepcheckout-login-link');
        this.popup = null;
        this.createPopup();
        this.mode = 'login';

        this.forgot_password_link = $('onestepcheckout-forgot-password-link');
        this.forgot_password_container = $('onestepcheckout-login-popup-contents-forgot');
        this.forgot_password_loading = $('onestepcheckout-forgot-loading');
        this.forgot_password_error = $('onestepcheckout-forgot-error');
        this.forgot_password_success = $('onestepcheckout-forgot-success');
        this.forgot_password_button = $('onestepcheckout-forgot-button');
        this.forgot_password_table = $('onestepcheckout-forgot-table');

        this.login_link = $('onestepcheckout-return-login-link');
        this.login_container = $('onestepcheckout-login-popup-contents-login');
        this.login_table = $('onestepcheckout-login-table');
        this.login_error = $('onestepcheckout-login-error');
        this.login_loading = $('onestepcheckout-login-loading');
        this.login_button = $('onestepcheckout-login-button');
        this.login_form = $('onestepcheckout-login-form');
        this.login_username = $('id_onestepcheckout_username');

        /* Bindings for the enter button */
        this.keypress_handler = function(e) {
            if(e.keyCode == Event.KEY_RETURN) {
                e.preventDefault();

                if(this.mode == 'login') {
                    this.login_handler();
                } else if(this.mode == 'forgot') {
                    this.forgot_password_handler();
                }
            }
        }.bind(this);

        this.login_handler = function(e) {

            var parameters = this.login_form.serialize(true);
            var url = this.options.login_url;
            this.showLoginLoading();

            new Ajax.Request(url, {
                method: 'post',
                parameters: parameters,
                onSuccess: function(transport) {
                    var result = transport.responseText.evalJSON();
                    if(result.success) {
                        window.location = window.location;
                    } else {
                        this.showLoginError(result.error);
                    }
                }.bind(this)
            });
        };

        this.forgot_password_handler = function(e) {
            var email = $('id_onestepcheckout_email').getValue();

            if(email == '') {
                alert(this.options.translations.invalid_email);
                return;
            }

            this.showForgotPasswordLoading();

            /* Prepare AJAX call */
            var url = this.options.forgot_password_url;

            new Ajax.Request(url, {
                method: 'post',
                parameters: { email: email },
                onSuccess: function(transport) {
                    var result = transport.responseText.evalJSON();

                    if(result.success) {
                        /* Show success message */
                        this.showForgotPasswordSuccess();

                        /* Pre-set username to simplify login */
                        this.login_username.setValue(email);
                    } else {
                        /* Show error message */
                        this.showForgotPasswordError();
                    }

                }.bind(this)
            });
        };

        this.bindEventHandlers();
    },

    bindEventHandlers: function() {
        /* First bind the link for opening the popup */
        if(this.popup_link){
            this.popup_link.observe('click', function(e) {
                e.preventDefault();
                this.popup.open();
            }.bind(this));
        }

        /* Link for closing the popup */
        if(this.popup_container){
            this.popup_container.select('p.close a').invoke(
                'observe', 'click', function(e) {
                this.popup.close();
            }.bind(this));
        }

        /* Link to switch between states */
        if(this.login_link){
            this.login_link.observe('click', function(e) {
                e.preventDefault();
                this.forgot_password_container.hide();
                this.login_container.show();
                this.mode = 'login';
            }.bind(this));
        }

        /* Link to switch between states */
        if(this.forgot_password_link){
            this.forgot_password_link.observe('click', function(e) {
                e.preventDefault();
                this.login_container.hide();
                this.forgot_password_container.show();
                this.mode = 'forgot';
            }.bind(this));
        }

        /* Now bind the submit button for logging in */
        if(this.login_button){
            this.login_button.observe(
                'click', this.login_handler.bind(this));
        }

        /* Now bind the submit button for forgotten password */
        if(this.forgot_password_button){
            this.forgot_password_button.observe('click',
                this.forgot_password_handler.bind(this));
        }

        /* Handle return keypress when open */
        if(this.popup){
            this.popup.observe('afterOpen', function(e) {
                document.observe('keypress', this.keypress_handler);
            }.bind(this));

            this.popup.observe('afterClose', function(e) {
                this.resetPopup();
                document.stopObserving('keypress', this.keypress_handler);
            }.bind(this));
        }

    },

    resetPopup: function() {
        this.login_table.show();
        this.forgot_password_table.show();

        this.login_loading.hide();
        this.forgot_password_loading.hide();

        this.login_error.hide();
        this.forgot_password_error.hide();

        this.login_container.show();
        this.forgot_password_container.hide();
    },

    showLoginError: function(error) {
        this.login_table.show();
        this.login_error.show();
        this.login_loading.hide();

        if(error) {
            this.login_error.update(error);
        }
    },

    showLoginLoading: function() {
        this.login_table.hide();
        this.login_loading.show();
        this.login_error.hide();
    },

    showForgotPasswordSuccess: function() {
        this.forgot_password_error.hide();
        this.forgot_password_loading.hide();
        this.forgot_password_table.hide();
        this.forgot_password_success.show();
    },

    showForgotPasswordError: function() {
        this.forgot_password_error.show();
        this.forgot_password_error.update(
            this.options.translations.email_not_found),

        this.forgot_password_table.show();
        this.forgot_password_loading.hide();
    },

    showForgotPasswordLoading: function() {
        this.forgot_password_loading.show();
        this.forgot_password_error.hide();
        this.forgot_password_table.hide();
    },

    show: function(){
        this.popup.open();
    },

    createPopup: function() {
        this.popup = new Control.Modal(this.popup_container, {
            overlayOpacity: 0.65,
            fade: true,
            fadeDuration: 0.3
        });
    }
});

function $RF(el, radioGroup) {
    if($(el).type && $(el).type.toLowerCase() == 'radio') {
        var radioGroup = $(el).name;
        var el = $(el).form;
    } else if ($(el).tagName.toLowerCase() != 'form') {
        return false;
    }

    var checked = $(el).getInputs('radio', radioGroup).find(
            function(re) {return re.checked;}
    );
    return (checked) ? $F(checked) : null;
}

function $RFF(el, radioGroup) {
    if($(el).type && $(el).type.toLowerCase() == 'radio') {
        var radioGroup = $(el).name;
        var el = $(el).form;
    } else if ($(el).tagName.toLowerCase() != 'form') {
        return false;
    }
    return $(el).getInputs('radio', radioGroup).first();
}

function get_totals_element()
{
    // Search for OSC summary element
    var search_osc = $$('div.onestepcheckout-summary');

    if(search_osc.length > 0)    {
        return search_osc[0];
    }

    var search_cart = $$('div.shopping-cart-totals');

    if(search_cart.length > 0)    {
        return search_cart[0];
    }

    if($('shopping-cart-totals-table'))    {
        return $('shopping-cart-totals-table');
    }

}

function get_save_methods_function(url, update_payments)
{

    if(typeof update_payments == 'undefined')    {
        var update_payments = false;
    }
    return function(e)    {
        if(typeof e != 'undefined')    {
            var element = e.element();

            if(element.name != 'shipping_method')    {
                update_payments = false;
            }
        }

        var form = $('onestepcheckout-form');
        var shipping_method = $RF(form, 'shipping_method');
        var payment_method = $RF(form, 'payment[method]');

        var totals = get_totals_element();
        totals.update('<div class="loading-ajax">&nbsp;</div>');

        if(update_payments)    {
            var payment_methods = $$('div.payment-methods')[0];
            payment_methods.update('<div class="loading-ajax">&nbsp;</div>');
        }

        var parameters = {
                shipping_method: shipping_method,
                payment_method: payment_method
        }
        /* Find payment parameters and include */
        var items = $$('input[name^=payment]').concat($$('select[name^=payment]'));
        var names = items.pluck('name');
        var values = items.pluck('value');

        for(var x=0; x < names.length; x++)    {
            if(names[x] != 'payment[method]')    {
                parameters[names[x]] = values[x];
            }
        }
        new Ajax.Request(url, {
            method: 'post',
            onSuccess: function(transport)    {
            if(transport.status == 200)    {
                var data = transport.responseText.evalJSON();
				
				if(typeof(data.session_error) != 'undefined'){
					alert(data.session_error);
					window.location.reload();
				}

                totals.update(data.summary);

                if(update_payments)    {
                    payment_methods.replace(data.payment_method);

                    $$('div.payment-methods input[name^=payment\[method\]]').invoke('observe', 'click', get_separate_save_methods_function(url));

                    $$('div.payment-methods input[name^=payment\[method\]]').invoke('observe', 'click', function() {
                        $$('div.onestepcheckout-payment-method-error').each(function(item) {
                            new Effect.Fade(item);
                        });
                    });

                    if($RF(form, 'payment[method]') != null)    {
                        try    {
                            var payment_method = $RF(form, 'payment[method]');
                            $('container_payment_method_' + payment_method).show();
                            $('payment_form_' + payment_method).show();
                        } catch(err)    {

                        }
                    }

                }
            }
        },
        parameters: parameters
        });
    }
}

function exclude_unchecked_checkboxes(data)
{
    var items = [];
    for(var x=0; x < data.length; x++)    {
        var item = data[x];
        if(item.type == 'checkbox')    {
            if(item.checked)    {
                items.push(item);
            }
        }
        else    {
            items.push(item);
        }
    }

    return items;
}

function get_save_billing_function(url, set_methods_url, update_payments, triggered, obj)
{
    if(typeof update_payments == 'undefined')    {
        var update_payments = false;
    }
    if(typeof triggered == 'undefined')    {
        var triggered = true;
    }

    if(!triggered){
        return function(){return;};
    }

    return function()    {
        var form = $('onestepcheckout-form');
        var items = exclude_unchecked_checkboxes($$('input[name^=shipping]').concat($$('select[name^=shipping]')));
        var shipping_names = items.pluck('name');
        var shipping_values = items.pluck('value');
        var parameters = {
                shipping_method: $RF(form, 'shipping_method')
        };


        var street_count = 0;
        for(var x=0; x < shipping_names.length; x++)    {
            if(shipping_names[x] != 'shipping_method')    {

                var current_name = shipping_names[x];

                if(shipping_names[x] == 'shipping[street][]')    {
                    current_name = 'shipping[street][' + street_count + ']';
                    street_count = street_count + 1;
                }

                parameters[current_name] = shipping_values[x];
				
            }
        }
        var use_for_billing = $('shipping:use_for_billing_yes');


        if(use_for_billing && use_for_billing.getValue() != '1')    {
            var items = $$('input[name^=billing]').concat($$('select[name^=billing]'));
            var names = items.pluck('name');
            var values = items.pluck('value');
            var street_count = 0;

            for(var x=0; x < names.length; x++)    {
                if(names[x] != 'payment[method]')    {
                    var current_name = names[x];
                    if(names[x] == 'billing[street][]')    {
                        current_name = 'billing[street][' + street_count + ']';
                        street_count = street_count + 1;
                    }

                    parameters[current_name] = values[x];
                }
            }
        }

        var shipment_methods = $$('div.onestepcheckout-shipping-method-block')[0];
        var shipment_methods_found = false;

        if(typeof shipment_methods != 'undefined') {
            shipment_methods_found = true;
        }
		
        if(shipment_methods_found)  {
            shipment_methods.update('<div class="loading-ajax-shipment-methods">&nbsp;</div>');
        }

        var payment_method = $RF(form, 'payment[method]');
        parameters['payment_method'] = payment_method;
        parameters['payment[method]'] = payment_method;
	

        var payment_methods = $$('div.payment-methods')[0];
        payment_methods.update('<div class="clear"></div><div class="amsloader ta-c pt30 pb30 fs42"></div>');

        var totals = get_totals_element();
        totals.update('<div class="loading-ajax">&nbsp;</div>');
        
        new Ajax.Request(url, {
            method: 'post',
            options: { asynchronous: false },
			onLoading: function(){
				jQuery('#delivery-loader').show();
			},
            onSuccess: function(transport)    {
			
            if(transport.status == 200)    {
                var data = transport.responseText.evalJSON();
				if(typeof(data.session_error) != 'undefined'){
					alert(data.session_error);
					window.location.reload();
				}
				
				jQuery('#delivery-loader').hide();
				
				/* Custom code custom JS call to populate city and state */
				if(typeof(data.billing) != 'undefined'){
					if(data.billing.statetext == ''){
						data.billing.statetext = "-- Select state --";
					}
					
					if(jQuery("select[name='billing[country_id]'] option:selected").val() == 'IN'){
						jQuery('#billing_address_list div.input-city input').val(data.billing.city);
						jQuery('#billing_address_list div.input-region input').val(data.billing.statetext);
					}
					
					jQuery("#billing_address_list div.input-region select option").each(function() {
						if(jQuery(this).attr('selected')){
							jQuery(this).attr('selected', false);
						}
					});
					jQuery("#billing_address_list div.input-region select option").filter(function() {
						return jQuery(this).val() == data.billing.state;
					}).attr('selected', true);
					
					if(jQuery("select[name='billing[country_id]'] option:selected").val() == 'IN'){
						jQuery('#billing_address_list div.input-region select').attr('defaultvalue',data.billing.state);
						jQuery('#billing_address_list div.input-region .selector > span').text(data.billing.statetext);
					}
				}
				
				if(typeof(data.shipping) != 'undefined'){
					if(data.shipping.statetext == ''){
						data.shipping.statetext = "-- Select state --";
					}
					
					if(jQuery("select[name='shipping[country_id]'] option:selected").val() == 'IN'){
						jQuery('#shipping_address_list div.input-city input').val(data.shipping.city);
						jQuery('#shipping_address_list div.input-region input').val(data.shipping.statetext);
					}
					
					jQuery("#shipping_address_list div.input-region select option").each(function() {
						if(jQuery(this).attr('selected')){
							jQuery(this).attr('selected', false);
						}
					});
					
					jQuery("#shipping_address_list div.input-region select option").filter(function() {
						return jQuery(this).val() == data.shipping.state;
					}).attr('selected', true);
					
					if(jQuery("select[name='shipping[country_id]'] option:selected").val() == 'IN'){
						jQuery('#shipping_address_list div.input-region select').attr('defaultvalue',data.shipping.state);
						jQuery('#shipping_address_list div.input-region .selector > span').text(data.shipping.statetext);
					}
				}
				
				if(typeof(data.shipping) != 'undefined'){
					if(data.shipping.postcode != '' && data.shipping.postcode != '-' && data.shipping.cod != '1'){
						if(jQuery("#shipping_address_list .input-postcode .post-code-error").length == 0){
								jQuery('#shipping_address_list .input-postcode input').after('<p class="post-code-error">Cash On Delivery not valid for this pincode. Please choose other payment option in PAYMENT tab.</p>');
						}
					}
					if(data.shipping.postcode != '' && data.shipping.postcode != '-' && data.shipping.cod == '1'){
						if(jQuery("#shipping_address_list .input-postcode .post-code-error").length > 0){
								jQuery('#shipping_address_list .input-postcode .post-code-error').remove();
						}
					}
				}else{
					if(jQuery("#shipping_address_list .input-postcode .post-code-error").length > 0){
						jQuery('#shipping_address_list .input-postcode .post-code-error').remove();
					}
				}
				
				if(typeof(data.shipping) != 'undefined'){
					if(typeof(data.shipping.country) != 'undefined' && data.shipping.country != 'IN'){
						if(jQuery('#shipping_address_list .input-telephone input').hasClass('validate-mobile')){
							jQuery('#shipping_address_list .input-telephone input').removeClass('validate-mobile');
						}
						if(jQuery('#shipping_address_list .input-telephone div.validation-advice').length >0 ){
							jQuery('#shipping_address_list .input-telephone div.validation-advice').remove();
						}
					}else{
						if(!jQuery('#shipping_address_list .input-telephone input').hasClass('validate-mobile')){
							jQuery('#shipping_address_list .input-telephone input').addClass('validate-mobile');
						}
					}
				}else{
					if(jQuery('#shipping_address_list .input-telephone input').hasClass('validate-mobile')){
						jQuery('#shipping_address_list .input-telephone input').removeClass('validate-mobile');
					}
					if(jQuery('#shipping_address_list .input-telephone div.validation-advice').length >0 ){
						jQuery('#shipping_address_list .input-telephone div.validation-advice').remove();
					}
				}
				
				if(typeof(data.billing) != 'undefined'){
					if(typeof(data.billing.country) != 'undefined' && data.billing.country != 'IN'){
						if(jQuery('#billing_address_list .input-telephone input').hasClass('validate-mobile')){
							jQuery('#billing_address_list .input-telephone input').removeClass('validate-mobile');
						}
						if(jQuery('#billing_address_list .input-telephone div.validation-advice').length >0 ){
							jQuery('#billing_address_list .input-telephone div.validation-advice').remove();
						}
					}else{
						if(!jQuery('#billing_address_list .input-telephone input').hasClass('validate-mobile')){
							jQuery('#billing_address_list .input-telephone input').addClass('validate-mobile');
						}
					}
				}else{
					if(jQuery('#billing_address_list .input-telephone input').hasClass('validate-mobile')){
						jQuery('#billing_address_list .input-telephone input').removeClass('validate-mobile');
					}
					if(jQuery('#billing_address_list .input-telephone div.validation-advice').length >0 ){
							jQuery('#billing_address_list .input-telephone div.validation-advice').remove();
						}
				}
			
				/* Custom js call ends */
				
                // Update shipment methods
                if(shipment_methods_found)  {
                    shipment_methods.update(data.shipping_method);
                }

                payment_methods.update(data.payment_method);
                totals.update(data.summary);

                // Add new event handlers

                if(shipment_methods_found)  {
                    $$('dl.shipment-methods input').invoke('observe', 'click', get_separate_save_methods_function(set_methods_url, update_payments));
                }

                $$('div.payment-methods input[name^=payment\[method\]]').invoke('observe', 'click', get_separate_save_methods_function(set_methods_url));

                $$('div.payment-methods input[name^=payment\[method\]]').invoke('observe', 'click', function() {
                    $$('div.onestepcheckout-payment-method-error').each(function(item) {
                        new Effect.Fade(item);
                    });
                });

                if(shipment_methods_found)  {
                    $$('dl.shipment-methods input').invoke('observe', 'click', function() {
                        $$('div.onestepcheckout-shipment-method-error').each(function(item) {
                            new Effect.Fade(item);
                        });
                    });
                }

                if($RF(form, 'payment[method]') != null)    {
                    try    {
                        var payment_method = $RF(form, 'payment[method]');
                        $('container_payment_method_' + payment_method).show();
                        $('payment_form_' + payment_method).show();
                    } catch(err)    {

                    }
                }
				
				//code to focus the current element in concern (not to let the form jump up) starts
				var thiscntrl = jQuery(obj).attr('id');
				//$(thiscntrl).focus();
				//code to focus the current element in concern (not to let the form jump up) ends
				
			/* hide show handeled in case of multiple javascript calls */
			}
        },
        parameters: parameters
        });
	
    }
}

function get_separate_save_methods_function(url, update_payments)
{
    if(typeof update_payments == 'undefined')    {
        var update_payments = false;
    }

    return function(e)    {
        if(typeof e != 'undefined')    {
            var element = e.element();

            if(element.name != 'shipping_method')    {
                update_payments = false;
            }
        }

        var form = $('onestepcheckout-form');
        var shipping_method = $RF(form, 'shipping_method');
        //var payment_method = $RF(form, 'payment[method]');
		var payment_method = jQuery("#p_method_sel").val();

        var payment_type = '';
        var p_pid = '';
        var payment_Pg = '';
        var bankcode = '';
        
        var ccnum = '';
        var ccname = '';
        var ccvv = '';
        var ccexpmon = '';
        var ccexpyr = '';

        if(payment_method != "cashondelivery"){
            payment_type = $RF(form, 'payment[type]');
            payment_Pg = $RF(form, 'payment[Pg]');
            bankcode = $RF(form, 'payment[Bankcode]');            
            ccnum = jQuery("#ccnum").val();
            ccname = jQuery("#ccname").val();
            ccvv = jQuery("#ccvv").val();
            ccexpmon = jQuery("#ccexpmon").val();
            ccexpyr = jQuery("#ccexpyr").val();
            p_pid =  jQuery("#p_pid").val();
        }
        
        var totals = get_totals_element();
        var freeMethod = $('p_method_free');
        if(freeMethod){
            payment.reloadcallback = true;
            payment.countreload = 1;
        }
		
		totals.update('<div class="loading-ajax">&nbsp;</div>');

        if(update_payments)    {
            var payment_methods = $$('div.payment-methods')[0];
            payment_methods.update('<div class="loading-ajax">&nbsp;</div>');
        }

        var parameters = {
                shipping_method: shipping_method,
                payment_method: payment_method,
                payment_type: payment_type,
                payment_Pg: payment_Pg,
                bankcode: bankcode,
                ccnum: ccnum,
                ccname: ccname,
                ccvv: ccvv,
                ccexpmon: ccexpmon,
                ccexpyr: ccexpyr,
                p_pid: p_pid
        }

        /* Find payment parameters and include */
        var items = $$('input[name^=payment]').concat($$('select[name^=payment]'));
        var names = items.pluck('name');
        var values = items.pluck('value');

        for(var x=0; x < names.length; x++)    {
            if(names[x] != 'payment[method]')    {
                parameters[names[x]] = values[x];
            }
        }
        new Ajax.Request(url, {
            method: 'post',
			onLoading: function(){
				jQuery('#delivery-loader').show();
			},
            onSuccess: function(transport)    {
            if(transport.status == 200)    {
                var data = transport.responseText.evalJSON();
				if(typeof(data.session_error) != 'undefined'){
					alert(data.session_error);
					window.location.reload();
				}
				jQuery('#delivery-loader').hide();
                var form = $('onestepcheckout-form');

                totals.update(data.summary);

                payment.currentType = data.payment_type;
                payment.currentMethod = data.payment_method_val;
                payment.currentProviderId = data.p_pid;
                               

                if(update_payments)    {

                    payment_methods.replace(data.payment_method);

                    $$('div.payment-methods input[name^=payment\[method\]]').invoke('observe', 'click', get_separate_save_methods_function(url));
                    $$('div.payment-methods input[name^=payment\[method\]]').invoke('observe', 'click', function() {
                        $$('div.onestepcheckout-payment-method-error').each(function(item) {
                            new Effect.Fade(item);
                        });
                    });

                    if($RF($('onestepcheckout-form'), 'payment[method]') != null)    {
                        try    {
                            var payment_method = $RF(form, 'payment[method]');
                            $('container_payment_method_' + payment_method).show();
                            $('payment_form_' + payment_method).show();
                        } catch(err)    {

                        }
                    }
                }
            }
        },
        parameters: parameters
        });
    }
}

function paymentrefresh(url) {
    var payment_methods = $$('div.payment-methods')[0];
    payment_methods.update('<div class="loading-ajax">&nbsp;</div>');
    new Ajax.Request(url, {
        method: 'get',
        onSuccess: function(transport){
            if(transport.status == 200)    {
                    var data = transport.responseText.evalJSON();
					if(typeof(data.session_error) != 'undefined'){
						alert(data.session_error);
						window.location.reload();
					}
                    payment_methods.replace(data.payment_method);

                    $$('div.payment-methods input[name^=payment\[method\]]', 'div.payment-methods input[name^=payment[useProfile]]').invoke('observe', 'click', get_separate_save_methods_function(url));
                    $$('div.payment-methods input[name^=payment\[method\]]', 'div.payment-methods input[name^=payment[useProfile]]').invoke('observe', 'click', function() {
                        $$('div.onestepcheckout-payment-method-error').each(function(item) {
                            new Effect.Fade(item);
                        });
                    });

                    if($RF(form, 'payment[method]') != null)    {
                        try    {
                            var payment_method = $RF(form, 'payment[method]');
                            $('container_payment_method_' + payment_method).show();
                            $('payment_form_' + payment_method).show();
                        } catch(err){}
                    }

            }
        }
    });
}

function addressPreview(templates, target) {
    var bparams = {};
    var sparams = {};
    var savedBillingItems = $('billing-address-select');
    if(savedBillingItems && savedBillingItems.getValue()){
        index = savedBillingItems.selectedIndex;
        bparams = customerBAddresses[index];
    } else {
        var items = $$('input[name^=billing]').concat($$('select[name^=billing]'));
        items.each(function(s) {
          if(s.getStyle('display') != 'none'){
              selectText = s.options
              if(selectText){
                  value = selectText[s.selectedIndex].text;
              } else {
                  value = s.getValue();
              }
              value = '<span class="' + s.id.replace(':','-') + '">' + value + '</span>';

              if(s.id == 'billing:region_id'){
                  bparams['billing:region'] = value;
              } else {
                  bparams[s.id] = value;
              }
          }
        });
    }



    var savedShippingItems = $('shipping-address-select');
    if(savedShippingItems && savedShippingItems.getValue()){
        index = savedShippingItems.selectedIndex;
        sparams = customerSAddresses[index];
    } else {
        var items = $$('input[name^=shipping]').concat($$('select[name^=shipping]'));
        items.each(function(s) {
            if(s.getStyle('display') != 'none'){
                selectText = s.options
                if(selectText){
                    value = selectText[s.selectedIndex].text;
                } else {
                    value = s.getValue();
                }

                value = '<span class="' + s.id.replace(':','-') + '">' + value + '</span>';

                if(s.id == 'shipping:region_id'){
                    sparams['shipping:region'] = value;
                } else {
                    sparams[s.id] = value;
                }
            }
        });
    }


    var form = $('onestepcheckout-form');

    var shipping_method = $RF(form, 'shipping_method');
    if(shipping_method){
        var shipping_label = $('s_method_' + shipping_method).up('dt').down('label').innerHTML;
        var shipping_title = $('s_method_' + shipping_method).up('dt').previous().innerHTML;
        var shipping_row = shipping_title + ' - ' + shipping_label
    }

    var useOnlyBilling = $('billing:use_for_shipping_yes').getValue();
    billinga_template = new Template(templates.billing);

    if(useOnlyBilling){
        shippinga_template = new Template(templates.billing);
    }else{
        shippinga_template = new Template(templates.shipping);
    }

    var payment_method = payment.currentMethod;

    if(payment_method){
        var payment_label = $('p_method_'+payment_method).up('dt').down('label').innerHTML;
    }

    var targetelem = $(target + '_billinga').childElements()[1];
    if(targetelem){
        targetelem.update(billinga_template.evaluate(bparams));
    }

    var targetelem = $(target + '_shippinga').childElements()[1];
    if(targetelem){
        if(useOnlyBilling){
            targetelem.update(shippinga_template.evaluate(bparams));
        }else{
            targetelem.update(shippinga_template.evaluate(sparams));
        }
    }

    var targetelem = $(target + '_shipping').childElements()[1];
    if(targetelem){
        targetelem.update(shipping_row);
    }

    var targetelem = $(target + '_payment').childElements()[1];
    if(targetelem){
        targetelem.update(payment_label);
    }

    var targetelem = $(target + '_summary').childElements()[1];
    if(targetelem){
        targetelem.update('');
        targetelem.insert($$('table.onestepcheckout-summary')[0].cloneNode(true));
        targetelem.insert($$('table.onestepcheckout-totals')[0].cloneNode(true));
    }
}


var Checkout = Class.create();
    Checkout.prototype = {
        initialize: function(){
        this.accordion = '';
        this.progressUrl = '';
        this.reviewUrl = '';
        this.saveMethodUrl = '';
        this.failureUrl = '';
        this.billingForm = false;
        this.shippingForm= false;
        this.syncBillingShipping = false;
        this.method = '';
        this.payment = '';
        this.loadWaiting = false;
    },

    ajaxFailure: function(){
        location.href = this.failureUrl;
    },

    setLoadWaiting: function(step, keepDisabled) {
        return true
    }
};

//billing
var Billing = Class.create();
Billing.prototype = {
            initialize: function(form, addressUrl, saveUrl){
        this.form = form;
    },

    setAddress: function(addressId){

    },

    newAddress: function(isNew){
        if (isNew) {
            this.resetSelectedAddress();
            Element.show('billing_address_list');
			jQuery("#billing_address .input-region .selector span").html("-- Select state --");
			jQuery("#billing_address_list li.control .shipping_save_address").val('1');
        } else {
            Element.hide('billing_address_list');
        }
        $$('input[name^=billing]', 'select[id=billing:region_id]').each(function(e){
            if(e.name=='billing[use_for_shipping]' || e.name=='billing[save_in_address_book]'){

            } else {
                e.value = '';
            }
        });
    },

    resetSelectedAddress: function(){
        var selectElement = $('billing-address-select')
        if (selectElement) {
            selectElement.value='';
        }
    },

    fillForm: function(transport){

    },

    setUseForShipping: function(flag) {

    },

    save: function(){

    },

    resetLoadWaiting: function(transport){

    },

    nextStep: function(transport){

    }
};

//shipping
var Shipping = Class.create();
    Shipping.prototype = {
            initialize: function(form, addressUrl, saveUrl){
        this.form = form;
    },

    setAddress: function(addressId){

    },

    newAddress: function(isNew){
        if (isNew) {
            this.resetSelectedAddress();
            Element.show('shipping_address_list');
			jQuery("#shipping_address_list .input-region .selector span").html("-- Select state --");
			if($('shipping:use_for_billing_yes').getValue() != "1" && $('billing-address-select').getValue() == ''){
                Element.show('billing_address_list');
				jQuery(".billing_address_list .input-region .selector span").html("-- Select state --");
            }
			jQuery("#shipping_address_list li.control .shipping_save_address").val('1');
        } else {
            Element.hide('shipping_address_list');
        }

        $$('#shipping_address input[name^=shipping],#shipping_address select[id=shipping:region_id]').each(function(e){
            if(e.name=='shipping[save_in_address_book]'){

            } else {
                e.value = '';
            }
        })

    },

    resetSelectedAddress: function(){
        var selectElement = $('shipping-address-select')
        if (selectElement) {
            selectElement.value='';
        }
    },

    fillForm: function(transport){

    },

    setSameAsBilling: function(flag) {
		
    },

    syncWithBilling: function () {

    },

    setRegionValue: function(){
        //$('shipping:region').value = $('billing:region').value;
    },

    save: function(){

    }
};

//payment object
var Payment = Class.create();
    Payment.prototype = {
            beforeInitFunc:$H({}),
            afterInitFunc:$H({}),
            beforeValidateFunc:$H({}),
            afterValidateFunc:$H({}),
            initialize: function(form, saveUrl){
        this.form = form;
        this.saveUrl = saveUrl;
    },

    init : function () {
        var elements = Form.getElements(this.form);
        if ($(this.form)) {
            //$(this.form).observe('submit', function(event){this.save();Event.stop(event);}.bind(this));
        }
        var method = null;
        for (var i=0; i<elements.length; i++) {
            if (elements[i].name=='payment[method]') {
                if (elements[i].checked) {
                    method = elements[i].value;
                }
            } else {
                elements[i].disabled = true;
            }
        }
        if (method) this.switchMethod(method);
    },

    switchMethod: function(method){
        if (this.currentMethod && $('payment_form_'+this.currentMethod)) {
            var form = $('payment_form_'+this.currentMethod);
            form.style.display = 'none';
            var elements = form.select('input').concat(form.select('select')).concat(form.select('textarea'));
            for (var i=0; i<elements.length; i++) elements[i].disabled = true;
        }

        if ($('payment_form_'+method)){
            var form = $('payment_form_'+method);
            form.style.display = '';
            var elements = form.select('input').concat(form.select('select')).concat(form.select('textarea'));
            for (var i=0; i<elements.length; i++) elements[i].disabled = false;
            this.currentMethod = method;
        }
    },

    addBeforeValidateFunction : function(code, func) {
        this.beforeValidateFunc.set(code, func);
    },

    beforeValidate : function() {
        var validateResult = true;
        var hasValidation = false;
        (this.beforeValidateFunc).each(function(validate){
            hasValidation = true;
            if ((validate.value)() == false) {
                validateResult = false;
            }
        }.bind(this));
        if (!hasValidation) {
            validateResult = false;
        }
        return validateResult;
    },

    validate: function() {
        var methods = document.getElementsByName('payment[method]');
        if (methods.length==0) {
            alert(Translator.translate('Your order can not be completed at this time as there is no payment methods available for it.'));
            return false;
        }
        for (var i=0; i<methods.length; i++) {
            if (methods[i].checked) {
                return true;
            }
        }
        alert(Translator.translate('Please specify payment method.'));
        return false;
    },

    save: function(){
    },
    addAfterInitFunction : function(code, func) {
        this.afterInitFunc.set(code, func);
    },

    afterInit : function() {
        (this.afterInitFunc).each(function(init){
            (init.value)();
        });
    }
};

/*******************************************************/

	
/*******************************************************/
