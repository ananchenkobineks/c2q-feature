jQuery( document ).ready(function() {
  
  var ajax_url      	= c2qvars.ajax_url,
  	  cart_url      	= c2qvars.cart_url,
  	  woo_ajax      	= c2qvars.woo_ajax,
   	  cartredirect  	= c2qvars.cartredirect,
  	  items_in_quote	= c2qvars.items_in_quote;
  
  //set update quantities value
  jQuery('.update_quotelist_button').on('click', function(event) {
    event.preventDefault();
    jQuery('.c2q_quotelist_update').val(1);
    jQuery(this).closest('form.c2q_quotelist_form').submit();
    return false;
  });
    
  if (woo_ajax == 'yes') {
    jQuery(document).on('click', '.c2q_archive_product button', function(e) {
      e.preventDefault();
      
      var thisform = jQuery(this).closest('form');

      var c2q_product_id  = thisform.find('input[name="c2q_product_id"]').val();
      var c2q_quantity    = thisform.find('input[name="c2q_quantity"]').val();
      
      // add loading icon
      thisform.find('.loading').css("display","inline-block");;
      thisform.find('.loaded').hide();
      
      
      jQuery.ajax({
      	type:"POST",
      	url: ajax_url,
     	 data: {
        	"action" : "check_product_quantity",
        	"c2q_product_id" : c2q_product_id
      	},
      	success:function(data){

      		c2q_quantity = data.data;
      		add_product_to_quotelist( thisform, ajax_url, c2q_product_id, c2q_quantity );
      	}
	  });

      return false;
    });
  }

  function add_product_to_quotelist( thisform, ajax_url, c2q_product_id, c2q_quantity ) {

  	var c2q_add_to_cart = thisform.find('input[name="c2q_add-to-cart"]').val();
	var nonce           = thisform.find('input[name="nonce"]').val();
	var c2q_form_submit = thisform.find('input[name="c2q_form_submit"]').val();

  	jQuery.ajax({
      type:"POST",
      url: ajax_url,
      data: {
        "action" : "add_product_to_quotelist",
        "c2q_quantity" : c2q_quantity,
        "c2q_add-to-cart" : c2q_add_to_cart,
        "c2q_product_id" : c2q_product_id,
        "nonce" : nonce,
        "c2q_form_submit" : c2q_form_submit
      },
      success:function(data){
        
        if (data.success == true) {

          
          if (jQuery('.mini_quotelist_wrap').length > 0) {
            jQuery('.mini_quotelist_wrap').html(data.data);

            items_in_quote = parseInt(items_in_quote) + parseInt(c2q_quantity);
            jQuery('nav .title-quantity').html(items_in_quote);
          }


          thisform.find('.loading').hide();
          thisform.find('.success').show();
          
          thisform.find('.c2q_added_to_cart_inline_message').show();
          
          
          // if (cartredirect == 'yes') {                
            // window.location.href = ""+carturl+"";
          // }
          
          setTimeout(function (){

            thisform.find('.success').hide();
            thisform.find('.loaded').show();

          }, 2000);
          
          setTimeout(function (){
            thisform.find('.c2q_added_to_cart_inline_message').fadeOut();  
          }, 10000);
          
        } else {
          console.log('failed');
          
          thisform.find('.loading').hide();
          thisform.find('.error').show();
          
          setTimeout(function (){

            thisform.find('.error').hide();
            thisform.find('.loaded').show();

          }, 3500);
          
        }
      },
      error: function(data) {
        console.log(data);
      }
    });

  }
  
  jQuery(document).on('change', '.woocommerce-variation-add-to-cart input[name="variation_id"]', function() {
    jQuery('#c2q_variation_id').val(jQuery(this).val());
  });
  jQuery(document).on('change', 'form.cart input[name="quantity"]', function() {
    jQuery('#c2q_quantity').val(jQuery(this).val());
  });
  
  
  jQuery(document).on('change', 'table.variations select', function() {
    get_selected_variations();    
  });
  
  jQuery( window ).load(function() {
    get_selected_variations();    
  });
  
  function get_selected_variations(el) {
    if (jQuery('form.variations_form table.variations tr').length > 0) {
      
      var attr = [];
      var values = [];
      
      jQuery('table.variations tr').each( function(index) {
        var selected = jQuery(this).find('select option:selected');
        attr.push(jQuery(this).find('select').attr('name') + '||' + selected.val());
        if (selected.val() != '') {
          values.push(selected.val());
        }
      });

      jQuery('#c2q_attributes').val(attr.join(','));
    }

    if (jQuery('form.c2q_is_variable').length > 0 && jQuery('body.c2q_hide_addtocart').length == 0 && (jQuery('form.cart button').attr('disabled') == 'disabled' || jQuery('form.cart button').hasClass('disabled') || jQuery('form.cart button').hasClass('wc-variation-selection-needed') )) {
      jQuery('form.quote_form button.c2q_button').attr('disabled', 'disabled').addClass('disabled').addClass('wc-variation-selection-needed');
    } else {
      jQuery('form.quote_form button.c2q_button').removeAttr('disabled').removeClass('disabled').removeClass('wc-variation-selection-needed');
    }
    
  }

	jQuery(document).on('click', '.mini_quotelist_wrap .remove', function(e) {
		e.preventDefault();

		jQuery('.quotelist-submenu').addClass('action-remove');

		jQuery.ajax({
      		type:"POST",
      		url: ajax_url,
			data: {
				"action" : "remove_item_from_quotelist",
				"c2q_remove_link" : jQuery(this).attr('href'),
			},
			success:function(result) {

				var removed_item_id = result.data.item_id;
				var removed_quantity = result.data.quantity;

				items_in_quote = parseInt(items_in_quote) - parseInt(removed_quantity);
            	jQuery('nav .title-quantity').html(items_in_quote);

            	if( items_in_quote == 0 ) {
            		jQuery('.mini_quotelist_wrap').html('Your quote list is empty!');
            	}

				jQuery('.quotelist-submenu .product_'+removed_item_id).remove();
				jQuery('.quotelist-submenu').removeClass('action-remove');
			},
			error: function(data) {
				console.log('Something is wrong with removing!!!');
			}
		});

	});

});