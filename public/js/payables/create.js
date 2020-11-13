$(function() {
    $(".bootstrap-switch-handle-on").on('click', function() {
        installmentsCalculate()
    })

    $(".bootstrap-switch-handle-off").on('click', function() {
        installmentsCalculate()
    })

    $(".bootstrap-switch-label").on('click', function() {
        installmentsCalculate()
    })

    $(".bootstrap-switch-handle-on").on('click', function() {
        installmentsCalculate()
    })

    $(document).on('change', '#payables-installments_number', function() {
        const quantity = $(this).val()
        const value = calculateInstallment(quantity)

        $("#payables-installment_value").val(value)
    })

    function installmentsCalculate() {
        if ($('#payables-installment').is(":checked")) {

            $('#installments').html(getFields())

            $("#installments").show(300)
            $("#view-monthly").hide(300)
        } else {
            $("#installments").hide(300)
            $("#view-monthly").show(300)
            $('#installments').html("")
        }        
    }

    function calculateInstallment(quantity) {
        const amount = $('#payable-value').val()
        const value = amount / quantity

        return value.toFixed(2)
    }
    
    function getFields() {
        let html = "<div class='form-group row'> \
            <label for='payables-installments_number' class='col-sm-2 col-form-label'>" + installments_number + "</label> \
            <div class='col-sm-10'> \
                <select id='payables-installments_number' class='form-control' name='installments_number' style='width: 50%' required> \
                    <option value=''>" + number_installments + "</option> \
                    <option value='2'>2</option> \
                    <option value='3'>3</option> \
                    <option value='4'>4</option> \
                    <option value='5'>5</option> \
                    <option value='6'>6</option> \
                    <option value='7'>7</option> \
                    <option value='8'>8</option> \
                    <option value='9'>9</option> \
                    <option value='10'>10</option> \
                    <option value='11'>11</option> \
                    <option value='12'>12</option> \
                </select> \
            </div> \
        </div> \
        <div class='form-group row'> \
            <label for='payables-installment_value' class='col-sm-2 col-form-label'>" + installment_value + "</label> \
            <div class='col-sm-10'> \
                <input type='number' id='payables-installment_value' class='form-control' name='installment_value' placeholder='0.00' readonly> \
            </div> \
        </div>"

        return html
    }
})
