jQuery(document).ready(function($) {
    $(document).on('click', '.vc-order a.expand', function(e) {
        e.preventDefault();
        $(this).hide();
        $(this).closest('.vc-order').find('.collapse').show();
        $(this).closest('.vc-order').find('.collapsed').each(function() {
            $(this).slideDown(200).removeClass('collapsed').addClass('show');
        })
    });

    $(document).on('click', '.vc-order a.collapse', function(e) {
        e.preventDefault();
        $(this).hide();
        $(this).closest('.vc-order').find('.expand').show();
        $(this).closest('.vc-order').find('.show').each(function() {
            $(this).slideUp(200).removeClass('show').addClass('collapsed');
        })
    });

    $(document).on('click', '.vc-order .actions button', function (e) {
        e.preventDefault();
        var data = {
            'order_id': $(this).closest('.vc-order').data('order_id'),
            'pizzeria_id' : $(this).closest('.vc-order').data('pizzeria_id')
        };
        var endpoint = '';

        if($(this).hasClass('accept')) {
            endpoint = '/accept-order';
        }

        if($(this).hasClass('deny')) {
            endpoint = '/deny-order';
        }

        if($(this).hasClass('done')) {
            endpoint = '/done-order';
        }
        var that = $(this);
        $.ajax({
            url: api_url + endpoint,
            data: data,
            method: 'POST',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
            }
        }).complete(function(response) {
            that.closest('.vc-order').after(response.responseText);
            that.closest('.vc-order').remove();
        });
    });

    $(document).on('click', '.vc-order button.edit', function(e) {
        e.preventDefault();
        $(this).hide();
        $(this).closest('.vc-order').find('button.save').show();
        var price = $(this).closest('.vc-order').find('span.total-price-val').text();
        $(this).closest('.vc-order').find('span.total-price-val').html('<input type="text" name="offer_total_price" value="'+price+'" />');

        var time = $(this).closest('.vc-order').find('span.time-val').text();
        $(this).closest('.vc-order').find('span.time-val').html('<input type="text" name="time_val" value="'+time+'" />');
        $('input[name=time_val]').datetimepicker();
    });

    $(document).on('click', '.vc-order button.save', function(e) {
        e.preventDefault();
        var data = {
            'order_id': $(this).closest('.vc-order').data('order_id'),
            'pizzeria_id' : $(this).closest('.vc-order').data('pizzeria_id'),
            'total_price' : $(this).closest('.vc-order').find('input[name=offer_total_price]').val(),
            'when' : $(this).closest('.vc-order').find('input[name=time_val]').val()
        };

        $(this).hide();
        $(this).closest('.vc-order').find('button.edit').show();
        $(this).closest('.vc-order').find('span.total-price-val').text(data.total_price);
        $(this).closest('.vc-order').find('span.time-val').text(data.when);

        $.post(api_url+'/order-offer', data, function(response) {

        });
    });
});