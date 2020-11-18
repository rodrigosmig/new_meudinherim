$(function() {
    $('[data-toggle="tooltip"]').tooltip()
    
    $(document).on('click', '.delete', function(event) {
        event.preventDefault()

        var id = $(this).attr('data-entry')
        
        if (id) {
            swal({
                title: entry_title,
                text: entry_text,
                icon: 'warning',
                buttons: [button_cancel, button_confirm],
                }).then(function(confirm) {
                    if (confirm) {          
                        $.ajax({
                            type: 'DELETE',
                            url: '/account_entries/' + id,
                            datatype: 'JSON',
                            success: function(response) {
                                swal({
                                    title: response.title,
                                    text: response.text,
                                    icon: 'success',
                                }).then(function() {
                                    location.reload();
                                })
                            },
                            error: function(response) {
                                swal({
                                    title: 'Oops...',
                                    text: response.responseJSON.text,
                                    icon: 'error',
                                }).then(function() {
                                    location.reload();
                                })
                            },
                          });	
                    }
                });
        }
        
        })
    
    $.ajaxSetup({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var dateFormat = 'yy-mm-dd'
    from = $( "#filter_from" ).datepicker({
        locale: 'pt-br',
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        numberOfMonths: 1
    }).on( "change", function() {
        to.datepicker( "option", "minDate", getDate( this ) );
    }),
    
    to = $( "#filter_to" ).datepicker({
        locale: 'pt-br',
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        numberOfMonths: 1
    }).on( "change", function() {
        from.datepicker( "option", "maxDate", getDate( this ) );
    });

    function getDate( element ) {
        var date;
        try {
            date = $.datepicker.parseDate( dateFormat, element.value );
        } catch( error ) {
            date = null;
        }
    
        return date;
    }
})