$(document).ready(function() {


    /*
        $('body').on('change', '#company-industry_id', function(e) {
            e.preventDefault();
            let industry_id = $(this).val;
            /*$.ajax({
                url: '/ajax/product-list',
                type: 'POST',
                data: {industry_id: industry_id},
                success: function (res) {
                    console.log('res', res)
                    if(res.result == 1) {
                        $('.field-company-industry_id').replaceWith(res.html)
                    }
                },
                error: function () {
                    alert('Error!');
                }
            });
        });
    */

    /**
     * сортирует изображения
     * */
    $('.image-preview-container-o').sortable({
        stop(ev, ui) {
            let sort = [];
            $('.image-preview-container-o .image-preview-o').each(function(index, element){
                sort.push($(element).attr('data-id'));
            });
            $.ajax({
                url: '/images/save-sort',
                method: 'post',
                data: {ids: JSON.stringify(sort)},
                success(response) {
                    if(response.result) {
                        displaySuccessMessage(response.message)
                    }
                },
                error(e) {
                    console.log('error', e)
                }
            });
        }
    });

    /**
     * сворачивает/разворачивает карточку
     * */
    $('body').on('click', '.card-header-o', function(e) {
        //e.preventDefault();
        let target = e.target;
        if($(target).is('.card-header-o')) {
            let parent = $(this).closest('.card-img-o');
            let body = parent.find('.card-body-o');
            let icon = $(this).find('.bi')
            if(body.is(':visible')) {
                body.slideUp();
                icon.removeClass('bi-chevron-up').addClass('bi-chevron-down')
            }
            else {
                body.slideDown();
                icon.removeClass('bi-chevron-down').addClass('bi-chevron-up')
            }
        }

    });






    initPlugins();

    function displaySuccessMessage(message) {
        $('.info-message').text(message);
        setTimeout(function() {
            $('.info-message').text('');
        }, 3000)
    }
    function displayErrorMessage(message) {
        $('.info-message').addClass('error').text(message);
        setTimeout(function() {
            $('.info-message').text('');
        }, 3000)
    }

    function initSelect2() {
        $('.select2').select2({
            placeHolder: '[не выбрано]'
        })
    }
    function initProductListSelect2() {
        $('.select-product-list').chosen({
            placeHolder: '[не выбрано]'
        })
        .on('change', function(ev) {
            let element = $(ev)[0];
            let target = element.target
            let industry_id = $(target).val();
            let company_id = $('.company-btn-submit').attr('data-id');
            $.ajax({
                url: '/ajax/product-list',
                type: 'POST',
                data: {industry_id: industry_id, company_id: company_id},
                success: function (res) {
                    console.log('res', res)
                    if(res.result == 1) {
                        $('.field-company-product_ids').replaceWith(res.html);
                        initSelect2()
                        //initProductListSelect2()
                    }
                },
                error: function () {
                    console.log('Error!');
                }
            });
        })
    }

    function initPlugins() {
        $(".phone-mask").inputmask({"mask": "+7 (999) 999-99-99"});
        $('.chosen').chosen()
        initSelect2()
        initProductListSelect2()
    }

    $('body').on('change', '#document-appointment_id', function(e) {
        e.preventDefault();
        let patient = $('.patient_container');
        patient.html('');
        let appointment_id = $(this).val();
        $.ajax({
            url: '/ajax/show-appointment',
            type: 'POST',
            data: {appointment_id: appointment_id},
            success: function (res) {
                if(res.result == 1) {
                    patient.html(res.html)
                }
                else if(res.message != null) {
                    patient.html(res.message)
                }
                return false;
            },
            error: function () {
                console.log('Error!');
            }
        });
    });
})
