<?php

header('Content-type: application/json; charset=UTF-8');
 
include_once "../bin/connect/DatabaseChat.php";

$DB = new DatabaseChat();

if(isset($_REQUEST["administrador"]) && $_REQUEST["administrador"]){$DB->listaHistoricoAdm();}

if(isset($_REQUEST["frist"]) && $_REQUEST["frist"]){$DB->listaTickets($_REQUEST["user"]);}

if(isset($_REQUEST["lastAll"]) && $_REQUEST["lastAll"]){$DB->listaChatAll($_REQUEST);}

if(isset($_REQUEST["last"]) && $_REQUEST["last"]){$DB->listaAll($_REQUEST);}

if(isset($_REQUEST["listFristAll"]) && $_REQUEST["listFristAll"]){$DB->listafristAll($_REQUEST);}

if(isset($_REQUEST["historico"]) && $_REQUEST["historico"]){$DB->listaHistorico($_REQUEST);}

if(isset($_REQUEST["contador"]) && $_REQUEST["contador"]){$DB->contador($_REQUEST);}

if(isset($_REQUEST["ler"]) && $_REQUEST["ler"]){ $DB->lerMsg($_REQUEST);}



// if(isset($_REQUEST["ticketsAll"]) && $_REQUEST["ticketsAll"]){echo json_encode($DB->listaTickets($_REQUEST["emitente"]));}

// if(isset($_REQUEST["emitente"]) && isset($_REQUEST["all"]) && $_REQUEST["all"]){echo json_encode($DB->listaChatAll($_REQUEST["emitente"]));}



// if(isset($_REQUEST["ticket"]) && isset($_REQUEST["emitente"]) && isset($_REQUEST["atendimento"]) && $_REQUEST["atendimento"]){echo json_encode($DB->listaChat($_REQUEST["emitente"],$_REQUEST["ticket"]));}

// if(isset($_REQUEST["ticket"]) && isset($_REQUEST["last"]) && $_REQUEST["last"]){echo json_encode($DB->listaChatLast($_REQUEST["emitente"],$_REQUEST["ticket"]));}



if(isset($_REQUEST["newchat"])){

	$dados = json_decode($_REQUEST["chat"]);

	$msg = $dados->text;
	$data = date('Y-m-d h:i:s', strtotime ($dados->createdAt));
	$canal = $dados->canal;
	$emitente = $dados->user->_id;
	$remetente = $dados->ticket;
	$nome = $dados->user->name;
	
	echo $DB->inserirChat($msg,$data,$canal,$emitente,$remetente,$nome);
}

if(isset($_REQUEST["newTicket"]) && $_REQUEST["newTicket"]){

	$dados = json_decode($_REQUEST["data"]);
	
	echo json_encode($DB->inserirTicket($dados));
}
?>