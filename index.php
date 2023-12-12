<?php
//controllo cookie per accesso

if (isset($_COOKIE["AccessoConsentitoMautic"])) {


?>

<?php
//CALENDARIO COMMERCIALE MAUTIC - 2023 DANIEL INTRIERI - TUTTI I DIRITTI RISERVATI
//CONCESSO PER L'UTILIZZO A 360FORMA
//VERSIONE 4.31 rel del 12-12-2023
$ambiente = "";

if(isset($_GET['ambiente'])){
$ambiente = $_GET['ambiente'];
}



if (empty ($ambiente)){
    $ambiente = 1;
}

//se ambiente 1 -> visualizza tutto
//se ambiente 2 -> visualizza i lead negativi
//se ambiente 3 -> visualizza i lead non negativi

//carica le risorse esterne per il funzionamento
include 'calendar.php';
include 'funzioni.php';
require("config.php");

//verifica presenza di un id utente
$idutente = $_GET['idutente'];

if (empty($idutente)){
    $idutente = 1;
}

//verifica presenza di mese
$mesepagina = "";
if (isset($_GET['mese'])){
$mesepagina = $_GET['mese'];
}
if (empty($mesepagina)){
$mesepagina = date("m");
$data = date("Y-m").'-1';
$data_anno = date("Y");
$data_mese = date("m");
} else {
$data = date("Y").'-'.$mesepagina.'-'.'1';
$data_mese = $mesepagina;
$data_anno = date("Y");
}

//calcolo per tasto mese successivo
if ($data_mese < 12){
    $datamesesucc = $data_mese + 1;
} else {
    $datamesesucc = 1;
}

//calcolo per tasto mese precedente
if ($data_mese == 1){
    $datameseprec = 12;
} else {
    $datameseprec = $data_mese - 1;
}




$calcolo = cal_days_in_month(CAL_GREGORIAN,$data_mese,$data_anno);
$fine = $data_anno.'-'.$data_mese.'-'.$calcolo;
if (date("m")==$mesepagina){
$calendar = new Calendar(date("Y-m-d"));
}else if (empty($mesepagina)) {
$calendar = new Calendar(date("Y-m-d"));
}else {
$calendar = new Calendar($data);
}

$data = $data.' 00:00';
$fine = $fine.' 23:59';

$result = mysqli_query($mysqli, "SELECT * FROM lead_notes WHERE created_by = $idutente AND type='call' AND date_time BETWEEN '$data' AND '$fine' ORDER BY date_time DESC");


$conteggiolead = 0;

//creo l'array per i confronti

$arrayconfronti = [];

while($res = mysqli_fetch_array($result)) {
    
    $cercami = $res['lead_id'];
    $result2 = mysqli_query($mysqli, "SELECT * FROM leads WHERE id = $cercami");
    while ($res2 = mysqli_fetch_array($result2)){
        $risultato = str_replace("'", "", $res2['firstname']). ' ' . str_replace("'", "", $res2['lastname']);
        $statolead = $res2['leadnegativo'];
        $idlead = $res2['id'];
        $checkiscritto = $res2['iscritto'];
        $proprietariocorrente = $res2['owner_id'];
    }
    
     //AGGIUNTA DEI LEAD TROVATI NEL CALENDARIO
 
    
    $colorepick = aggiustacolore($res['created_by']);
    
    
$rimuovi = array("'", "&nbsp", ';',"\r\n", "\r", "\n", "\t");
$clean = str_replace($rimuovi, "", trim(strip_tags($res['text'])));
    
    
//CONTROLLA SE QUESTO LEAD E' IN QUESTO MOMENTO ASSOCIATO A QUESTO COMMERCIALE

if($proprietariocorrente == $idutente){

if (rimuoviiscritto($checkiscritto)) {
    
//cerca in array valore, se non esiste aggiungilo e poi permetti l'esecuzione del blocco
if (!cercainarray($arrayconfronti,$idlead)){
    
if ($ambiente == 1){ // tutti i lead
    $calendar->add_event($risultato,interpretadata($res['date_time']),1,$colorepick,$res['lead_id'],$clean,$res['id']);
    $conteggiolead = $conteggiolead + 1;
} else if ($ambiente == 2){ //solo lead negativi
    
    if ($statolead == 'lead negativo'){
        $calendar->add_event($risultato,interpretadata($res['date_time']),1,$colorepick,$res['lead_id'],$clean,$res['id']);
        $conteggiolead = $conteggiolead + 1;
    }
    
} else if ($ambiente == 3){ //lead non negativi
    
    if (empty($statolead)){
        $calendar->add_event($risultato,interpretadata($res['date_time']),1,$colorepick,$res['lead_id'],$clean,$res['id']);
        $conteggiolead = $conteggiolead + 1;
    }
    
}
//alla fine aggiungilo all'array
array_push($arrayconfronti, $idlead);
}

}
}
//FINE CONTROLLO COMMERCIALE

}



?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>360Forma - Calendario Commerciale</title>
		<link href="stilemodal.css" rel="stylesheet" type="text/css">
		<link href="calendar.css" rel="stylesheet" type="text/css">
		<link href="nuovotooltip.css" rel="stylesheet" type="text/css">
		<link href="sidebar.css" rel="stylesheet" type="text/css">
		<link href="aggiunte.css" rel="stylesheet" type="text/css">
		<link href="favicon-1.ico" rel="icon">
		<meta name="viewport" content="width=device-width, initial-scale=1" />
	</head>
	<body>
	     
	    <div style="max-height:90%;" class="sidenav">
	        <p class="titolinav">Elenco commerciali</p>
	        
	        <?php 
	        // creazione lista commerciali su lato sinistro pagina
	        
	        $result = mysqli_query($mysqli, "SELECT * FROM users WHERE role_id = 4 OR role_id =1 OR role_id=2");
	        
	        while($res = mysqli_fetch_array($result)) {
	            if ($res['id'] != 1){
	            $colorepick = aggiustacolore($res['id']);
	            echo '<div class="event '.$colorepick.'"><a style="color:white;" href="index.php?ambiente='.$ambiente.'&idutente='.$res['id'].'">'.$res['first_name'].' '.$res['last_name'].'</a></div>';
	        
	            }
	            }
	        
	        // fine creazione lista commerciali su lato sinistro pagina
	        ?>
	        <p style="outline-style: dotted;outline-color:red;text-align:center;">Aggiornamento automatico tra<br><span style="font-weight:bold;color:red;" id="timer"></span><br><button onclick="aggiorna()">Aggiorna</button></p>
	        <p class="titolinav">Strumenti</p>
	        <?php 
	        if ($mesepagina == date("m")){
	        ?>
	        <a class="azioni" href="#giornocorrente">Vai a giorno corrente</a>
	        <?php
	        } else {
	        ?>
	        <a class="azioni" href="index.php?idutente=<?php echo $idutente ?>&mese=<?php echo date("m") ?>&ambiente=<?php echo $ambiente ?>">Vai al mese corrente</a>
	        <?php } ?>
	        <a class="azioni" href="http://gestionale.360forma.com/s/dashboard">Torna su Mautic</a>
	        <br>
	        
<p class="titolinav">Visualizzazione Lead</p>

<a class="vocilead <?php if ($ambiente == 2) {echo 'cerchiato';} ?>" href="index.php?mese=<?php echo $data_mese ?>&idutente=<?php echo $idutente ?>&ambiente=2">Negativi</a>
<a class="vocilead <?php if ($ambiente == 3) {echo 'cerchiato';} ?>" href="index.php?mese=<?php echo $data_mese ?>&idutente=<?php echo $idutente ?>&ambiente=3">Da lavorare</a>
<a class="vocilead <?php if ($ambiente == 1) {echo 'cerchiato';} ?>" href="index.php?mese=<?php echo $data_mese ?>&idutente=<?php echo $idutente ?>&ambiente=1">Tutto</a>

<p><b style="color:red;">Rosso</b> indica selezione corrente</p>

<br>

<p style="outline-style: dotted;outline-color:red;text-align:center;">
    <b>Statistiche:</b><br><br>
    Lead <?php if ($ambiente == 2) {echo 'negativi';} else if ($ambiente == 3) { echo 'da lavorare';} ?> per il mese di <?php echo nomemese($data_mese) ?>:<br><b style="color:red;"><?php echo $conteggiolead ?></b>
</p>
</div>
<!--QUI FINISCE LA BARRA DI NAVIGAZIONE -->
<div class="main">
	   
		<div class="content home">
			<?=$calendar?>
		</div>
		<div style="text-align:center;margin-top:20px;margin-bottom:20px;">
		<a href="index.php?mese=<?php echo $datameseprec ?>&idutente=<?php echo $idutente ?>&ambiente=<?php echo $ambiente ?>" class="previous">&laquo; Mese Precedente</a>

<?php 
	if ($datamesesucc == 1){
		?>
<a href="index.php?mese=<?php echo $datamesesucc ?>&idutente=<?php echo $idutente ?>&ambiente=<?php echo $ambiente ?>" class="next">Torna a Gennaio &raquo;</a>
			<?php
	} else {
	
	?>
			
<a href="index.php?mese=<?php echo $datamesesucc ?>&idutente=<?php echo $idutente ?>&ambiente=<?php echo $ambiente ?>" class="next">Mese Successivo &raquo;</a>
<?php
			} 
	
	?>
</div>
</div>


<!-- INSERIMENTO MODAL -->

 <div id="id01" class="w3-modal">
    <div class="w3-modal-content">
      <div class="w3-container">
        <span onclick="document.getElementById('id01').style.display='none'" class="w3-button w3-display-topright">&times;</span>
        
        <!-- tab -->
        
        <div class="tab">
  <button class="tablinks active" onclick="openCity(event, 'info')">Informazioni</button>
  <button class="tablinks" onclick="openCity(event, 'riprogramma')">Riprogramma</button>
</div>

<div id="info" class="tabcontent" style="display: block;">
  <h3 id="titololead">Nome Cognome</h3>
  <p id="contenuto">Contenuto.</p>
  <a class="azioni" id="tastolead" href="" target="_blank">Apri Lead</a>
</div>

<div id="riprogramma" class="tabcontent">
    <div style="margin-bottom:10px;">
        <p style="display:none;" id="databasenote">---</p><br>
        <p style="font-weight:bold;" id="datacorrente">---</p><br>
  <label for="datariprogrammazione">Inserisci nuova data:</label>


       <input type="date" id="datariprogrammazione" value="2023-01-01" />
       
       <button onclick="riprogramma()">Riprogramma</button>
       </div>
</div>

        <!-- tab -->
        
      </div>
    </div>
  </div>

<!-- FINE INSERIMENTO MODAL -->


<!-- SCRIPT FUNZIONAMENTO MODAL -->

<script>

function riprogramma(){
    var id = document.getElementById('databasenote').innerHTML;
    var data = document.getElementById('datariprogrammazione').value;
    
    var xmlhttp = new XMLHttpRequest();
    
    xmlhttp.open("GET", "riprogramma.php?id=" + id + "&data=" + data, true);
    xmlhttp.send();
    alert("lead riprogrammato per data " + data);
    location.reload();
    
}


function boxazioni(lead,nota,link,dbnota,data) {

document.getElementById('id01').style.display='block';
document.getElementById('titololead').innerHTML = lead;
document.getElementById('contenuto').innerHTML = nota;
document.getElementById('tastolead').href= link;
document.getElementById('databasenote').innerHTML = dbnota;
document.getElementById('datariprogrammazione').value = data;
document.getElementById('datacorrente').innerHTML = 'Data corrente: ' + data;
}

</script>

<script>
function openCity(evt, cityName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
}
</script>
<!-- FINE SCRIPT FUNZIONAMENTO MODAL -->
<!-- SCRIPT FUNZIONAMENTO TIMER -->
<script>

document.getElementById('timer').innerHTML =
  01 + ":" + 01;
startTimer();

function startTimer() {
 var controlla = document.getElementById('id01');
                
  var presentTime = document.getElementById('timer').innerHTML;
  var timeArray = presentTime.split(/[:]+/);
  var m = timeArray[0];
  var s = timeArray[1];
  if (controlla.style.display != "block"){
      s = checkSecond((timeArray[1] - 1));
  }
  if(s==59){m=m-1}
  if(m<0){location.reload();}
  
  document.getElementById('timer').innerHTML =
    m + ":" + s;
  setTimeout(startTimer, 1000);

}

function checkSecond(sec) {
  if (sec < 10 && sec >= 0) {sec = "0" + sec}; // add zero in front of numbers < 10
  if (sec < 0) {sec = "59"};
  return sec;
}

function aggiorna() {
    location.reload();
}

</script>
<!-- FINE SCRIPT FUNZIONAMENTO TIMER -->

	</body>
</html>

<?php
} else {
//condizione se cookie non trovato
echo 'Autorizzazione negata, accedi a Mautic.';
}
?>
