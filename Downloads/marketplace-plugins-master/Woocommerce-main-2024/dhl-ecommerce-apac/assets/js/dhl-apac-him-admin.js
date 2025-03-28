(function($) {

	//Ready jquery
	jQuery(document).ready(function() {

		//Create Lable button on click jquery
		jQuery(document).on('click','.button.dhl_him_create_lable',function(){
			jQuery('#him_dhl_order_lable_form_data_save').prop('checked', true);
			jQuery("#poststuff #woocommerce-order-actions .inside .save_order").trigger("click");
		});
		
		//Check Numeric validation for Form.
		jQuery("input[name^=dhl_soldto_account]").bind("keypress", function (e) {
			var keyCode = e.which ? e.which : e.keyCode
			 
			if (!(keyCode >= 48 && keyCode <= 57)) {
				jQuery(".error").css("display", "inline");
				return false;
			}else{
				jQuery(".error").css("display", "none");
			}
		});

		//Check Numeric validation for COD DHL Field.
		jQuery("input[name^=him_dhl_shipping_cash_on_delivery]").bind("keypress", function (e) {
			var keyCode = e.which ? e.which : e.keyCode
			
			if( keyCode != 46 ){
				if (!(keyCode >= 48 && keyCode <= 57)) {
					alert("Only allow numeric values!");
					return false;
				}
			} 
		});
		jQuery("input.form-control.dhl-him-cash-on-del").bind("keypress", function (e) {
			var keyCode = e.which ? e.which : e.keyCode
			
			if( keyCode != 46 ){
				if (!(keyCode >= 48 && keyCode <= 57)) {
					alert("Only allow numeric values!");
					return false;
				}
			}
		});

		//Check Numeric validation for Shipment Value Protection DHL Field.
		jQuery("input[name^=him_dhl_shipping_shipment_value_protection]").bind("keypress", function (e) {
			var keyCode = e.which ? e.which : e.keyCode
			
			if( keyCode != 46 ){ 
				if (!(keyCode >= 48 && keyCode <= 57)) {
					alert("Only allow numeric values!");
					return false;
				}
			}
		});
		jQuery("input.form-control.dhl-him-shipment-insurance").bind("keypress", function (e) {
			var keyCode = e.which ? e.which : e.keyCode
			
			if( keyCode != 46 ){ 
				if (!(keyCode >= 48 && keyCode <= 57)) {
					alert("Only allow numeric values!");
					return false;
				}
			}
		});
		jQuery("input[name^=him_dhl_shipment_weight]").bind("keypress", function (e) {
			var keyCode = e.which ? e.which : e.keyCode
			 
			if (!(keyCode >= 48 && keyCode <= 57)) {
				alert("Only allow numeric values!");
				return false;
			}
		});

		//Test keypress connection jquery
		jQuery("input[name^=dhl_client_id]").bind("change paste keyup", function (e) {
			var dInput = $(this).val();
			$("input[name^=hiddencliendid]").val(dInput);
		});

		jQuery("input[name^=dhl_client_secret_passworrd]").bind("change paste keyup", function (e) {
			var dInput = $(this).val();
			$("input[name^=hiddencliendsecretid]").val(dInput);
		});

		//Check Test Connection AUth API Jquery
		jQuery(document).on('click','button.button.testconnectioncheck',function(){
			var hidden_cliendid          = jQuery('input[name="hiddencliendid"]').val();
			var hidden_cliendsecretid    = jQuery('input[name="hiddencliendsecretid"]').val();
			him_check_dhl_auth_client_api( hidden_cliendid, hidden_cliendsecretid );
		});

		function him_check_dhl_auth_client_api( dhl_client_id, dhl_secret_client_id  ) {

			jQuery.ajax({
				url:    himVars.admin_url,
				type:   'POST',
				dataType:"json",
				data:
				{
					dhl_client_id:dhl_client_id,
					dhl_secret_client_id:dhl_secret_client_id,
					action:'him_check_dhl_auth_client_api'
				},
				success: function(response)
				{
					if(response.status)
					{
						jQuery('input[name="hiddencliendid"]').val( response.dhl_client_id);
						jQuery('input[name="hiddencliendsecretid"]').val( response.dhl_secret_client_id);
					}

					jQuery( response.him_auth_response ).insertBefore( ".him-test-connection-message" );
					jQuery('.test-connection-check .successfull').addClass('him-ajax-successfull');
					jQuery('.test-connection-check .unsuccessfull').addClass('him-ajax-successfull');
				},
				beforeSend:function()
				{
					jQuery('.loading-icon').show();
					
				},
				complete:function()
				{
					jQuery('.loading-icon').hide();
					jQuery('.him-ajax-successfull').fadeOut(5000);
				},
				error:function(xhr,rrr,error)
				{
					
				}
			});

		}


		//Order page dropoff field remove readonly jquery
		jQuery("#him_dhl_shipping_handover_method").change(function() {
			
			var gethandoverval = jQuery('option:selected', this).val();
			var getReturnmode  = jQuery('#him_dhl_shipping_address_return_mode option:eq(1)').val();

			if ( gethandoverval == '2' ) {

				jQuery('input[name="him_dhl_shipping_pickup_date"]').attr("readonly", false);
				jQuery('input[name="him_dhl_shipping_companyName"]').attr("readonly", false);
				jQuery('input[name="him_dhl_shipping_buyer_name"]').attr("readonly", false);
				jQuery('input[name="him_dhl_shipping_address_line_one"]').attr("readonly", false);
				jQuery('input[name="him_dhl_shipping_address_line_two"]').attr("readonly", false);
				jQuery('input[name="him_dhl_shipping_address_line_three"]').attr("readonly", false);
				jQuery('input[name="him_dhl_shipping_address_city"]').attr("readonly", false);

				jQuery('input[name="him_dhl_shipping_address_state"]').attr("readonly", false);
				jQuery('input[name="him_dhl_shipping_address_district"]').attr("readonly", false);
				jQuery('input[name="him_dhl_shipping_address_country"]').attr("readonly", false);
				jQuery('input[name="him_dhl_shipping_address_postcode"]').attr("readonly", false);
				jQuery('input[name="him_dhl_shipping_address_phone"]').attr("readonly", false);
				jQuery('input[name="him_dhl_shipping_address_email"]').attr("readonly", false);

				if ( getReturnmode == '02' ) {
					jQuery('#him_dhl_shipping_address_return_mode option:eq(1)').attr("disabled", false);
				}

				jQuery('#him_dhl_shipping_address_country').removeClass('add-disable-css');

			}else{

				jQuery('input[name="him_dhl_shipping_pickup_date"]').val("");
				jQuery('input[name="him_dhl_shipping_pickup_date"]').attr("readonly", true);
				jQuery('input[name="him_dhl_shipping_companyName"]').attr("readonly", true);
				jQuery('input[name="him_dhl_shipping_buyer_name"]').attr("readonly", true);
				jQuery('input[name="him_dhl_shipping_address_line_one"]').attr("readonly", true);
				jQuery('input[name="him_dhl_shipping_address_line_two"]').attr("readonly", true);
				jQuery('input[name="him_dhl_shipping_address_line_three"]').attr("readonly", true);
				jQuery('input[name="him_dhl_shipping_address_city"]').attr("readonly", true);

				jQuery('input[name="him_dhl_shipping_address_state"]').attr("readonly", true);
				jQuery('input[name="him_dhl_shipping_address_district"]').attr("readonly", true);
				jQuery('input[name="him_dhl_shipping_address_country"]').attr("readonly", true);
				jQuery('input[name="him_dhl_shipping_address_postcode"]').attr("readonly", true);
				jQuery('input[name="him_dhl_shipping_address_phone"]').attr("readonly", true);
				jQuery('input[name="him_dhl_shipping_address_email"]').attr("readonly", true);
				
				if ( getReturnmode == '02' ) {
					jQuery('#him_dhl_shipping_address_return_mode option:eq(1)').attr("disabled", true);
				}
				jQuery('#him_dhl_shipping_address_country').addClass('add-disable-css');

			}

		});

		//End Order page dropoff field remove readonly jquery

		jQuery(window).load(function() {

			//On load scroll jquery
			if(jQuery("p").hasClass("him_dhl_lable_error_message")){
				jQuery('html, body').animate({
			        scrollTop: jQuery(".him_dhl_lable_error_message").offset().top
			    }, 2000);
			    setTimeout(function () {
			    	jQuery('#him-woocommerce-create-dhl-label').css('top',150);
			    }, 2500);
			}

			var dropoff = jQuery('option:selected', '#him_dhl_shipping_handover_method').val();
			var optiontwo  = jQuery('#him_dhl_shipping_address_return_mode option:eq(1)').val();

			if ( dropoff == '2' ) {

				jQuery('input[name="him_dhl_shipping_pickup_date"]').attr("readonly", false);
				jQuery('input[name="him_dhl_shipping_companyName"]').attr("readonly", false);
				jQuery('input[name="him_dhl_shipping_buyer_name"]').attr("readonly", false);
				jQuery('input[name="him_dhl_shipping_address_line_one"]').attr("readonly", false);
				jQuery('input[name="him_dhl_shipping_address_line_two"]').attr("readonly", false);
				jQuery('input[name="him_dhl_shipping_address_line_three"]').attr("readonly", false);
				jQuery('input[name="him_dhl_shipping_address_city"]').attr("readonly", false);

				jQuery('input[name="him_dhl_shipping_address_state"]').attr("readonly", false);
				jQuery('input[name="him_dhl_shipping_address_district"]').attr("readonly", false);
				jQuery('input[name="him_dhl_shipping_address_country"]').attr("readonly", false);
				jQuery('input[name="him_dhl_shipping_address_postcode"]').attr("readonly", false);
				jQuery('input[name="him_dhl_shipping_address_phone"]').attr("readonly", false);
				jQuery('input[name="him_dhl_shipping_address_email"]').attr("readonly", false);

				if ( optiontwo == '02' ) {
					jQuery('#him_dhl_shipping_address_return_mode option:eq(1)').attr("disabled", false);
				}
				jQuery('#him_dhl_shipping_address_country').removeClass('add-disable-css');

			}else{

				jQuery('input[name="him_dhl_shipping_pickup_date"]').attr("readonly", true);
				jQuery('input[name="him_dhl_shipping_companyName"]').attr("readonly", true);
				jQuery('input[name="him_dhl_shipping_buyer_name"]').attr("readonly", true);
				jQuery('input[name="him_dhl_shipping_address_line_one"]').attr("readonly", true);
				jQuery('input[name="him_dhl_shipping_address_line_two"]').attr("readonly", true);
				jQuery('input[name="him_dhl_shipping_address_line_three"]').attr("readonly", true);
				jQuery('input[name="him_dhl_shipping_address_city"]').attr("readonly", true);

				jQuery('input[name="him_dhl_shipping_address_state"]').attr("readonly", true);
				jQuery('input[name="him_dhl_shipping_address_district"]').attr("readonly", true);
				jQuery('input[name="him_dhl_shipping_address_country"]').attr("readonly", true);
				jQuery('input[name="him_dhl_shipping_address_postcode"]').attr("readonly", true);
				jQuery('input[name="him_dhl_shipping_address_phone"]').attr("readonly", true);
				jQuery('input[name="him_dhl_shipping_address_email"]').attr("readonly", true);
				if ( optiontwo == '02' ) {
					jQuery('#him_dhl_shipping_address_return_mode option:eq(1)').attr("disabled", true);
				}
				jQuery('#him_dhl_shipping_address_country').addClass('add-disable-css');

			}

		});

		//Order page dropoff field remove readonly jquery ( after save load )


		// On scroll remove top css for Error
		jQuery(window).scroll(function(){
			jQuery('#him-woocommerce-create-dhl-label').css('top',0);
		});


		//End Order page dropoff field remove readonly jquery ( after save load )

		//Order page Return Mode dropoff field show new address jquery
		jQuery("#him_dhl_shipping_address_return_mode").change(function() {

			var gethandoverval = jQuery('option:selected', this).val();

			if ( gethandoverval == '03' ) {

				jQuery('.h-row.him-dhl-return-form').removeAttr('style');

			}else{

				jQuery('.h-row.him-dhl-return-form').css('display','none');
			}

		});

		jQuery(window).load(function() {

			var gethandoverval = jQuery('option:selected', '#him_dhl_shipping_address_return_mode').val();

			if ( gethandoverval == '03' ) {

				jQuery('.h-row.him-dhl-return-form').removeAttr('style');

			}else{

				jQuery('.h-row.him-dhl-return-form').css('display','none');
			}

		});

		//End Order page Return Mode dropoff field show new address jquery

		//Cash on delivery toggle jquery
		jQuery(document).on('click','.him-dhl-cashon-delievry',function(){

			jQuery('#him_dhl_shipping_cash_on_delivery').toggleClass('show-cash-field');
			jQuery('.dhl-him-cash-on-del').toggleClass('dhl-him-cash-on-true');
			if (jQuery(".dhl-him-cash-on-del").hasClass("dhl-him-cash-on-true")) {
				jQuery('.dhl-him-cash-on-del').prop("disabled", false);
			}else{
				jQuery('.dhl-him-cash-on-del').prop("disabled", true);
				jQuery('.dhl-him-cash-on-del').val('');
				jQuery('#him_dhl_shipping_cash_on_delivery').val('');
			}

		});

		jQuery(document).on('click','.him-dhl-shipment-protection',function(){

			jQuery('#him_dhl_shipping_shipment_value_protection').toggleClass('show-cash-field');
			jQuery('.dhl-him-shipment-insurance').toggleClass('dhl-him-shipment-insurance-true');
			if (jQuery(".dhl-him-shipment-insurance").hasClass("dhl-him-shipment-insurance-true")) {
				jQuery('.dhl-him-shipment-insurance').prop("disabled", false);
			}else{
				jQuery('.dhl-him-shipment-insurance').prop("disabled", true);
				jQuery('.dhl-him-shipment-insurance').val('');
				jQuery('#him_dhl_shipping_shipment_value_protection').val('');
			}

		});

		// Input field Cash on Delivery summession jquery
		jQuery(".dhl-him-cash-on-del").bind("change paste keyup", function (e) {
			var dhlhimcashonsum = 0;
			jQuery(".dhl-him-cash-on-del").each(function(){
				dhlhimcashonsum += +jQuery(this).val();
			});
			jQuery('#him_dhl_shipping_cash_on_delivery').val(dhlhimcashonsum);

		});
		//End Input field Cash on Delivery summession jquery

		// Input field Shipment Weight summession jquery
		jQuery(".dhl-him-shipment-weight").bind("change paste keyup", function (e) {
			var dhlhimcashonsum = 0;
			jQuery(".dhl-him-shipment-weight").each(function(){
				dhlhimcashonsum += +jQuery(this).val();
			});
			jQuery('#him_dhl_shipping_weight').val(dhlhimcashonsum);

		});
		//End Input field Shipment Weight summession jquery

		// Input field Shipment Insurance summession jquery
		jQuery(".dhl-him-shipment-insurance").bind("change paste keyup", function (e) {
			var dhlhimShipmentinsurance = 0;
			jQuery(".dhl-him-shipment-insurance").each(function(){
				dhlhimShipmentinsurance += +jQuery(this).val();
			});
			jQuery('#him_dhl_shipping_shipment_value_protection').val(dhlhimShipmentinsurance);

		});
		//End Input field Shipment Insurance summession jquery

		//Check if input field is not empty then triggering to click toggle
		jQuery(window).load(function() {

			var getinputfield = jQuery('#him_dhl_shipping_cash_on_delivery');
			var getinputfield2 = jQuery('#him_dhl_shipping_shipment_value_protection');
			var getinputfield3 = jQuery('#him_dhl_shipping_shipment_value_ppod');

			if( jQuery(getinputfield).val().length !== 0 ) {
				 
				jQuery('.him-dhl-cashon-delievry').trigger('click');
			}
			if( jQuery(getinputfield2).val().length !== 0 ) {
				 
				jQuery('.him-dhl-shipment-protection').trigger('click');
			}

		});

		//Repeater field jquery
		jQuery( '#him-dhl-add-row' ).on('click', function() {
			var row = jQuery( '.empty-row.dhl-him-custom-repeter-text' ).clone(true);
			row.removeClass( 'empty-row dhl-him-custom-repeter-text' ).css('display','table-row');
			row.insertBefore( '#dhl-him-repeatable-fieldset-one tbody>tr:last' );
			return false;
		});

		jQuery( '.dhl-him-remove-row' ).on('click', function() {
			jQuery(this).parents('tr').remove();
			return false;
		});

		//Check Multi Pieces Shipment checked jquery
		jQuery('#him_dhl_shipping_multi_pieces_shipment').click(function() {

			if ( jQuery(this).is(":checked") ) {
				jQuery('.him-dhl-delivery-option').removeAttr('style');
				jQuery('.him-dhl-repeated-multi-shipment').removeAttr('style');
			}else{
				jQuery('.him-dhl-delivery-option').css('display','none');
				jQuery('.him-dhl-repeated-multi-shipment').css('display','none');
			}

		});

		//Check if window load and repeater option save
		jQuery(window).load(function() {

			var getinputfield = jQuery('#him_dhl_shipping_multi_pieces_shipment');

			if ( jQuery(getinputfield).is(":checked") ) {
				jQuery('.him-dhl-delivery-option').removeAttr('style');
				jQuery('.him-dhl-repeated-multi-shipment').removeAttr('style');
			}else{
				jQuery('.him-dhl-delivery-option').css('display','none');
				jQuery('.him-dhl-repeated-multi-shipment').css('display','none');
			}

		});

		//Edit Lable jquery
		jQuery('.dhl-him-edit-lable-btn').click(function() {

			var currenturl  = window.location.href; 
			window.location.href = currenturl + '&editlable=true';
		
		});
		//End Edit Lable jquery

		//Delete Lable jquery
		jQuery('.dhl-him-delete-lable-btn').click(function() {

			var currenturl  = window.location.href; 
			location.href = currenturl + '&deletelable=true';
			jQuery('#him_dhl_order_lable_form_data_save').prop('checked', false);
			jQuery("#poststuff #woocommerce-order-actions .inside .save_order").trigger("click");
		
		});
		//End Delete Lable jquery

		//On Order page weight is 0 condition

		jQuery(window).load(function() {
			var Dhlweight = jQuery('input[name=him_dhl_shipping_weight]').val();
			if ( Dhlweight == 0 ) {
				alert('Please Enter a Shipment Weight');
				jQuery('.dhl_him_create_lable').prop("disabled", true);
				return false;
			}else{
				jQuery('.dhl_him_create_lable').prop("disabled", false);
			}
		});

		jQuery("input[name^=him_dhl_shipping_weight]").bind("change paste keyup", function (e) {
			var inputVal = jQuery(this).val();
			if ( $.isNumeric(inputVal) ) {
				jQuery('.dhl_him_create_lable').prop("disabled", false);
			}else{
				jQuery('.dhl_him_create_lable').prop("disabled", true);
			}
		});

		//End On Order page weight is 0 condition



		//Start pickup account ID apeend in URL

		jQuery("#him_dhl_order_pickup_account").change(function() { 
			var picID = jQuery(this).val();
			var newUrl = window.location.href + '&pickup=' + picID;
    		window.location.href = newUrl;
		});

		//End pickup account ID apeend in URL

	});


})(jQuery);