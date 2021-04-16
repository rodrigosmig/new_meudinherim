$(function() {
    $('[data-toggle="tooltip"]').tooltip()

    $(".cancel_payment").on('click', function(event) {
        event.preventDefault()
        
        var id = $(this).attr('data-payable')
        var payable_id = $(this).attr('data-parcelable')

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