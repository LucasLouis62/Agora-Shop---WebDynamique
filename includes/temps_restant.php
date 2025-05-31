<?php
    $temps_restant = '';
    if (!empty($annonce['date_ajout'])) {
        $date_ajout = new DateTime($annonce['date_ajout']);
        $date_fin = clone $date_ajout;
        $date_fin->modify('+72 hours');
        $now = new DateTime();
        if ($now < $date_fin) {
            $interval = $now->diff($date_fin);
            $temps_restant = $interval->format('%a jours %h h %i min %s s');
        } else {
            $temps_restant = 'Enchère terminée';
        }
    }
?>