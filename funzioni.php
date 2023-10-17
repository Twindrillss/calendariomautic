<?php

//ogni volta che viene aggiunto un nuovo utente, trovarne l'id e associargli un colore (aggiungere la stessa cosa nei fogli di stile sidebar.css e calendar.css)

function aggiustacolore ($idutente){
    if ($idutente == 2){ //utente1
        $colore = 'blue';
    }else if ($idutente ==5){ //utente2
        $colore = 'green';
    } else if ($idutente == 3){ //utente3
        $colore = 'red';
    } else if ($idutente == 6) { //utente4
        $colore = 'purple';
    } else if ($idutente == 4) { //utente5
        $colore = 'orange';
    } else if ($idutente == 8) { //utente6
        $colore = 'lightblue';
    } else if ($idutente == 7){ //utente7
        $colore = 'yellow';
    } else {
        $colore = 'violet'; //utente non identificato
    }
    return $colore;
}


function nomemese ($mese){
    if ($mese == 1){
        $nomemese = 'Gennaio';
    }else if ($mese == 2){
        $nomemese = 'Febbraio';
    }else if ($mese == 3){
        $nomemese= 'Marzo';
    }else if ($mese == 4){
        $nomemese = 'Aprile';
    }else if ($mese == 5){
        $nomemese = 'Maggio';
    }else if ($mese == 6){
        $nomemese = 'Giugno';
    }else if ($mese == 7){
        $nomemese = 'Luglio';
    }else if ($mese == 8){
        $nomemese = 'Agosto';
    }else if ($mese == 9){
        $nomemese = 'Settembre';
    }else if ($mese == 10){
        $nomemese = 'Ottobre';
    }else if ($mese == 11){
        $nomemese = 'Novembre';
    }else if ($mese == 12){
        $nomemese = 'Dicembre';
    }
    return $nomemese;
}

function interpretadata($datosporco){
    $indicadata = date_parse($datosporco);
    
    $datacompleta = $indicadata["year"].'-'.$indicadata["month"].'-'.$indicadata["day"];
    
    return $datacompleta;
}


function cercainarray($array,$valore){
    
    $conteggio = count($array);
    $controllo = false;
    
    for ($i = 0; $i < $conteggio; $i++){
        
        if ($array[$i] == $valore){
            $controllo = true;
            break;
        }
        
    }
    return $controllo;
}

function rimuoviiscritto($dato){
    if ($dato == 1){
        $ok = false;
    }else {
        $ok =true;
    }
    return $ok;
}

?>
