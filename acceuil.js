$(document).ready(function () {
    var $carrousel = $('#carrousel');
    var $img = $carrousel.find('img');
    var indexImg = $img.length - 1;
    var i = 0;

    // Affiche uniquement la première image
    $img.hide().eq(i).show();

    // Bouton suivant
    $carrousel.find('.next').click(function () {
        $img.eq(i).hide();
        i = (i + 1) > indexImg ? 0 : i + 1;
        $img.eq(i).show();
    });

    // Bouton précédent
    $carrousel.find('.prev').click(function () {
        $img.eq(i).hide();
        i = (i - 1) < 0 ? indexImg : i - 1;
        $img.eq(i).show();
    });
});