$(function() {
    $(document).on('click', '.delete', function(event) {
        event.preventDefault()
        
        var id = $(this).attr('data-card')
        
        if (id) {

        }
        swal({
            title: card_title,
            text: card_text,
            icon: 'warning',
            buttons: [button_cancel, button_confirm],
            }).then(function(confirm) {
                if (confirm) {          
                    $.ajax({
                        type: 'DELETE',
                        url: '/cards/' + id,
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
        })
    
    $.ajaxSetup({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
})