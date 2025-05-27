$(document).ready(function () {
    var $carrousel = $('#carrousel');
    var $img = $carrousel.find('img');
    var indexImg = $img.length - 1;
    var i = 0;

    // Affiche uniquement la première image
    $img.removeClass('active').eq(i).addClass('active');

    // Bouton suivant
    $carrousel.find('.next').click(function () {
        $img.eq(i).removeClass('active');
        i = (i + 1) > indexImg ? 0 : i + 1;
        $img.eq(i).addClass('active');
    });

    // Bouton précédent
    $carrousel.find('.prev').click(function () {
        $img.eq(i).removeClass('active');
        i = (i - 1) < 0 ? indexImg : i - 1;
        $img.eq(i).addClass('active');
    });
});