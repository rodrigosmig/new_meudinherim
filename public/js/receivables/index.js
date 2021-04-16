$(function() {
    $('[data-toggle="tooltip"]').tooltip()
    
    $(document).on('click', '.delete', function(event) {
        event.preventDefault()
        
        var id = $(this).attr('data-receivable')
        
        if (id) {
            swal({
                title: receivable_title,
                text: receivable_text,
                icon: 'warning',
                buttons: [button_cancel, button_confirm],
                }).then(function(confirm) {
                    if (confirm) {          
                        $.ajax({
                            type: 'DELETE',
                            url: '/receivables/' + id,
                            datatype: 'JSON',
                            success: function(response) {
                                swal({
                                    title: response.title,
                                    text: response.text,
                                    icon: 'success',
                                }).then(function() {
                                    window.location="/receivables";
                                })
                            },
                            error: function(response) {
                                swal({
                                    title: 'Oops...',
                                    text: response.responseJSON.text,
                                    icon: 'error',
                                }).then(function() {
                                    window.location="/receivables";
                                })
                            },
                          });	
                    }
                });
        }
    })

    $(document).on('click', '.delete-parcels', function(event) {
        event.preventDefault()
        
        var id = $(this).attr('data-receivable')
        
        if (id) {
            swal({
                title: parcel_title,
                text: parcel_text,
                icon: 'warning',
                buttons: [button_cancel, button_confirm],
                }).then(function(confirm) {
                    if (confirm) {          
                        $.ajax({
                            type: 'DELETE',
                            url: '/receivables/' + id,
                            datatype: 'JSON',
                            success: function(response) {
                                swal({
                                    title: response.title,
                                    text: response.text,
                                    icon: 'success',
                                }).then(function() {
                                    window.location="/receivables";
                                })
                            },
                            error: function(response) {
                                swal({
                                    title: 'Oops...',
                                    text: response.responseJSON.text,
                                    icon: 'error',
                                }).then(function() {
                                    window.location="/receivables";
                                })
                            },
                          });	
                    }
                });
        }
    })

    $(document).on('click', '.cancel_receivement', function(event) {
        event.preventDefault()
        
        var id = $(this).attr('data-receivable')
        var payable_id = $(this).attr('data-parcelable')

        swal({
            title: receivable_title,
            text: receivable_text,
            icon: 'warning',
            buttons: [button_cancel, button_confirm]
            }).then(function(confirm) {
                if (confirm) {          
                    window.location.href = "/receivables/" + id + "/cancel?parcelable_id=" + payable_id;
                }
            });

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