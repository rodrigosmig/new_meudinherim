$(function() {
    $('[data-toggle="tooltip"]').tooltip()

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