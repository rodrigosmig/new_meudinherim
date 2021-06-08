$(function() {
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
                            url: '/invoice_entries/' + id,
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

    $(document).on('click', '.delete-parcels', function(event) {
        event.preventDefault()
        
        var id = $(this).attr('data-entry')
        
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
                            url: '/invoice_entries/' + id,
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

    $(".anticipate-parcels").on('click', function() {
        const entry_id = $(this).attr('data-entry_id')
        const card_id = $(this).attr('data-card_id')
        const parcel_number = $(this).attr('data-parcel_number')

        $.ajax({
            type: 'GET',
            url: `/invoice_entries/${entry_id}/next-parcels`,
            datatype: 'JSON',
            data: {
                'entry_id': entry_id,
                'card_id': card_id,
                'parcel_number': parcel_number
            },
            success: function(response) {
                if (response.parcels.length === 0) {
                    $("#loading").hide()
                    $("#no-entries").html("No entries found")
                    return
                }

                $('#form-anticipate').attr('action', `/invoice_entries/${response.parcels[0].parcelable_id}/anticipate-parcels`)

                const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
                let remaining = 0;

                $("#table-caption").append(response.total.toLocaleString('pt-br', {style: 'currency', currency: 'BRL'}))

                for (const parcel of response.parcels) {
                    const date = new Date(parcel.date + "T00:00:00.000-03:00")
                    const value = parcel.value.toLocaleString('pt-br', {style: 'currency', currency: 'BRL'});
                    remaining += parcel.value;

                    html = "<tr> \
                        <td> " + parcel.parcel_number + "</td> \
                        <td> " + date.toLocaleDateString('pt-BR', options) + "</td> \
                        <td> " + parcel.description + "</td> \
                        <td> " + value + "</td> \
                        <td><input type='checkbox' name='parcels[]' value=" + parcel.id +"></td> \
                    </tr>"

                    $("#table-body").append(html)
                }

                $("#table-remaining").append(remaining.toLocaleString('pt-br', {style: 'currency', currency: 'BRL'}))

                $("#loading").hide()
                $("#table-parcels").show()
                
            },
            error: function(response) {
                console.log(response)
                $("#loading").hide()
                $("#no-entries").html(response.responseJSON.text)
            },
          });
    });

    $('#modal-anticipate').on('hidden.bs.modal', function (e) {
        $('#table-body').html("")
        $("#no-entries").html("")
        $("#table-caption").html("")
        $("#table-remaining").html("")
        $("#table-parcels").hide()
        $("#loading").show()
    })
    
    $.ajaxSetup({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
})