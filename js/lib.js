(function() {
    'use strict';
    window.addEventListener('load', function() {

        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.getElementsByClassName('needs-validation');

        // Loop over them and prevent submission
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

function onLoaderFunc() {
    $(".seatStructure *").prop("disabled", true);
}
function takeData() {

    let inputs = document.querySelectorAll(".needs-validation input, .needs-validation select");

    /* Break, if main booking for not pass validation */
    for (let i = 0; i < inputs.length; i++) {
        if (!inputs[i].validity.valid){
            return false;
        }
    }

    /** Checking reserved seats */
    let data = {
        'film_id' : $("#booking-filmId").val(),
        'username' : $("#booking-username").val(),
        'email' : $("#booking-email").val(),
        'phone' : $("#booking-phone").val(),
        'session' : $("#booking-session").val()
    };
    $.ajax({
        url: "/films/checkReservedSeats",
        type: "POST",
        data: data,
        dataType: 'json',
        success: function (response) {

            if (Array.isArray(response) && response.length > 0){
                $(".seatStructure input[type=checkbox]").each(function(index, element){
                    if (response.includes($(element).val())){
                        $(element).addClass('reserved');
                    }else{
                        $(element).prop('checked', false);
                        $(element).removeClass('reserved');
                    }
                });
            }else{
                $(".seatStructure input[type=checkbox]").each(function(index, element){

                    $(element).prop('checked', false);
                    $(element).removeClass('reserved');
                });
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $('.select-places-now').html('Что-то пошло не так. Пожалуйста попробуйте ещё.');
        }
    });

    /* Break, if main booking for not pass validation */
    $(".seatStructure *").not('.reserved').prop("disabled", false);
    $(".seatStructure input[type=checkbox]").not('.reserved').addClass('select-places-now-available');
    document.getElementById("notification").innerHTML = "<div class='select-places-now'>Сейчас вы можете выбрать места!</div>";
    return false;
}
function updateTextArea() {

    $(".seatStructure *").not('.reserved').prop("disabled", true);

    let seats = [];
    $('#seatsBlock :checked').each(function() {
        seats.push($(this).val());
    });

    let data = {
        'film_id' : $("#booking-filmId").val(),
        'username' : $("#booking-username").val(),
        'email' : $("#booking-email").val(),
        'phone' : $("#booking-phone").val(),
        'session' : $("#booking-session").val(),
        'seats' : seats
    };

    $.ajax({
        url: "/films/booking",
        type: "POST",
        data: data,
        dataType: 'json',
        success: function (response) {
            $('.select-places-now').html(response['0']);
            $(".seatStructure :checkbox").removeClass('select-places-now-available');
            $(".seatStructure :checkbox").prop('disabled', true);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $('.select-places-now').html('Что-то пошло не так. Пожалуйста попробуйте ещё.');
            // console.log(thrownError);
        }
    });
}
$(":checkbox").click(function() {
    $(":checkbox").not('.reserved').prop('disabled', true);
    $(':checked').not('.reserved').prop('disabled', false);
});

$(document).ready(function(){

    /** Clear seats if, user change session */
    if ($('.needs-validation input, .needs-validation select').length > 0){
        jQuery('.needs-validation input, .needs-validation select').change(function(input){
            $(".seatStructure :checkbox").removeClass('reserved');
            $(".seatStructure :checkbox").removeClass('select-places-now-available');
            $(".seatStructure :checkbox").removeProp('checked');
            $(".seatStructure :checkbox").prop('disabled', true);
        });
    }
    if ($("#owl").length > 0){
        $('#owl').owlCarousel({
        items: 5,
        margin: 15,
        autoplay: true,
        autoplayHoverPause: true,
        loop: true,
        navigation: true,
        nav: true,
        dots: false,
        mouseDrag: true,
        responsiveClass: true,
        responsive: {
            0:{
                items: 1
            },
            480:{
                items: 1
            },
            769:{
                items: 3
            },
            1024:{
                items: 5
            }
        }
    });
    }

    if ($(".img-wrap").length > 0){
        // Background image
        $('.img-wrap').each( function(){
            let img = $( this ).find( 'img' );
            let src = img.attr( 'src' );
            $( this ).css( 'background-image', 'url( '+ src +' )' );
        });
    }

    if ($(".gridSelector").length > 0){
        let grid = $(".gridSelector");
        let asyncGrid = new Gridifier(grid, {
            "class": "gridItem",
        });
        asyncGrid.append(asyncGrid.collectNew());
    }

    $('#myModal').on('show.bs.modal', function (e) {
        let element = $(e.relatedTarget).parent();
        let index = $(e.relatedTarget).index( 'td' );

        let data = {
            'film_id' : $(element).children('.film-id').text().replace(".", ""),
            'session' : $(e.relatedTarget).closest('table').children('thead').children('tr').children('th').eq(index).text().replace("-00", "")
        };

        $.ajax({
            url: "/films/checkReservedSeats",
            type: "POST",
            data: data,
            dataType: 'json',
            success: function (response) {
                if (Array.isArray(response) && response.length > 0){
                    $(".seatStructure input[type=checkbox]").each(function(index, element){
                        if (response.includes($(element).val())){
                            $(element).addClass('reserved');
                        }else{
                            $(element).prop('checked', false);
                            $(element).removeClass('reserved');
                        }
                    });
                }else{
                    $(".seatStructure input[type=checkbox]").each(function(index, element){

                        $(element).prop('checked', false);
                        $(element).removeClass('reserved');
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $('.select-places-now').html('Что-то пошло не так. Пожалуйста попробуйте ещё.');
            }
        });
    })
});