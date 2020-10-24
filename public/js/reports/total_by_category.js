$(function() {
    $(".show-entries").on('click', function() {
        let from = $(this).attr('data-from')
        let to = $(this).attr('data-to')
        let category = $(this).attr('data-category')
        let type = $(this).attr('data-type')

        $.ajax({
            type: 'GET',
            url: 'total-by-category/ajax',
            datatype: 'JSON',
            data: {
                'from': from,
                'to': to,
                'category_id': category,
                'type': type,
            },
            success: function(response) {
                if (response.length === 0) {
                    $("#loading").hide()
                    $("#no-entries").html("No entries found")
                    return
                }

                for (const entry of response) {
                    let date        = new Date(entry.date)
                    let type_entry  = ""                    

                    if (type === 'account') {
                        type_entry = entry.account.name
                    } else if (type === 'card') {
                        type_entry = entry.invoice.card.name
                    }

                    const value     = entry.value.toLocaleString('pt-br', {style: 'currency', currency: 'BRL'});                    
                    const options   = { year: 'numeric', month: '2-digit', day: '2-digit' };

                    html = "<tr> \
                        <td> " + date.toLocaleDateString('pt-BR', options) + "</td> \
                        <td> " + entry.description + "</td> \
                        <td> " + value + "</td> \
                        <td> " + type_entry + "</td> \
                    </tr>"

                    $("#table-body").append(html)
                }

                $("#loading").hide()
                $("#table-caption").html(response[0].category.name)
                $("#table-entries").show()
                
            },
            error: function(response) {
                $("#loading").hide()
                $("#no-entries").html(response.responseJSON.message)
            },
          });	
        
    });

    $('#modal-entries').on('hidden.bs.modal', function (e) {
        $('#table-body').html("")
        $("#no-entries").html("")
        $("#table-entries").hide()
        $("#loading").show()
    })

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
})