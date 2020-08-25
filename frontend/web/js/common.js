
$(document).ready(function () {

    $('.owl-one').owlCarousel({
        loop: true,
        margin: 10,
        nav: false,
        dots: true,
        responsive: {
            0: {
                items: 1
            },
            600: {
                items: 1
            },
            1000: {
                items: 1
            }
        }
    });


    $('#whatsnew').owlCarousel({
        loop: true,
        margin: 30,
        nav: true,
        dots: false,
        navText: "",
        responsive: {
            0: {
                items: 1
            },
            600: {
                items: 3
            },
            1000: {
                items: 4
            }
        }
    });

    $('#insta').owlCarousel({
        loop: true,
        margin: 30,
        nav: true,
        dots: false,
        navText: "",
        responsive: {
            0: {
                items: 1
            },
            600: {
                items: 3
            },
            1000: {
                items: 4
            }
        }
    });

    /* Select DropDown JS*/
    $("#Day,#Month,#Year").select2();

    $('#frmSignup').validate({
        
    });

});


$('.icon').click(function () {
    $('.search').toggleClass('expanded');
});

function addtocart(url) {
    var variation_id = 1;
    $.ajax({
        url: url + '&variation_id=' + variation_id,
        success: function (data) {
            alert('added to cart.');
        }
    });

}

function addtowishlist(url) {
    $.ajax({
        url: url,
        success: function (data) {
            alert('added to wishlist.');
        }
    });

}