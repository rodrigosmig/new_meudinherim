$(function() {
    $('[data-toggle="tooltip"]').tooltip()
    
    $(document).on('click', '.delete', function(event) {
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

    $(document).on('click', '.delete-parcels', function(event) {
        event.preventDefault()
        
        var id = $(this).attr('data-payable')
        
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

    $(document).on('click', '.cancel_payment', function(event) {
        event.preventDefault()
        
        var id = $(this).attr('data-payable')
        var payable_id = $(this).attr('data-parcelable')

        console.log(id, payable_id)
        swal({
            title: payable_title,
            text: payable_text,
            icon: 'warning',
            buttons: [button_cancel, button_confirm]
            }).then(function(confirm) {
                if (confirm) {          
                    window.location.href = "/payables/" + id + "/cancel?parcelable_id=" + payable_id;
                }
            });

    })
    
    $.ajaxSetup({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
})
