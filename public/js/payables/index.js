$(function() {
    $('[data-toggle="tooltip"]').tooltip()
    
    $(".delete").on('click', function(event) {
        event.preventDefault()
        
        var id = $(this).attr('data-payable')
        
        if (id) {
            swal({
                title: payable_title,
                text: payable_text,
                icon: 'warning',
                buttons: [button_cancel, button_confirm],
                }).then(function(confirm) {
                    if (confirm) {          
                        $.ajax({
                            type: 'DELETE',
                            url: '/payables/' + id,
                            datatype: 'JSON',
                            success: function(response) {
                                swal({
                                    title: response.title,
                                    text: response.text,
                                    icon: 'success',
                                }).then(function() {
                                    window.location="/payables";
                                })
                            },
                            error: function(response) {
                                swal({
                                    title: 'Oops...',
                                    text: response.responseJSON.text,
                                    icon: 'error',
                                }).then(function() {
                                    window.location="/payables";
                                })
                            },
                          });	
                    }
                });
        }
    })

    $(".cancel_payment").on('click', function(event) {
        event.preventDefault()
        
        var id = $(this).attr('data-payable')

        swal({
            title: payable_title,
            text: payable_text,
            icon: 'warning',
            buttons: [button_cancel, button_confirm]
            }).then(function(confirm) {
                if (confirm) {          
                    window.location.href = "/payables/" + id + "/cancel";
                }
            });

    })
    
    $.ajaxSetup({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
})
