$(function() {
    $('[data-toggle="tooltip"]').tooltip()

    $(".cancel_receivement").on('click', function(event) {
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
})