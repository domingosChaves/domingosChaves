<?php 
include_once 'ConnDataBase.php';

class DatabaseChat extends ConnDataBase
{
	public function listaTickets($user){
		$conn = $this->conn;
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stmt = $conn->prepare("SELECT id, num_ticket, user_ticket, canal_ticket, status_ticket, date_ticket FROM ticket WHERE user_ticket = ? ORDER BY	id ASC");
		$stmt->execute(array($user));

		$lista = array();
		$geral = array();

		while ($row = $stmt->fetch()) {
			$lista[] = array(
				"id" => $row["id"],
				"ticket" => $row["num_ticket"],
				"usuario" => $row["user_ticket"],
				"canal" =>	$row["canal_ticket"],
				"lastdate" => $row["date_ticket"],
				"status" => $row["status_ticket"]
			);
		}
		$geral["ticket"] = $lista;
		$this->listaChatAll($geral);

	}

	// public function listaHistorico($user)
	// {
	// 	$lista = array();

	// 	$conn = $this->conn;
	// 	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	// 	$stmt = $conn->prepare("SELECT t.id,af.nomeprofessor AS usuario,af.tipo AS tipoUser,(c.data) AS data,(
	// 		SELECT mensagem FROM chat WHERE id=max(c.id) AND tipo='txt') AS mensagem,(
	// 		SELECT sts FROM chat WHERE id=max(c.id)) AS sts,(
	// 		SELECT tipo FROM chat WHERE id=max(c.id)) AS tipo,(
	// 		SELECT emitente FROM chat WHERE id=max(c.id)) AS emitente,(
	// 		SELECT remetente FROM chat WHERE id=max(c.id)) AS remetente,t.num_ticket,t.msg_ticket,t.status_ticket,t.user_ticket,t.adm_ticket,cal.id_canal,cal.nome_canal,(
	// 		SELECT count(id) FROM chat WHERE (emitente=t.num_ticket AND remetente=t.user_ticket) AND sts='e') AS qt FROM chat c JOIN ticket t ON (t.num_ticket=c.emitente OR t.num_ticket=c.remetente) JOIN canal cal ON cal.id_canal=t.canal_ticket JOIN admpedagogo.funcionariologin af ON t.user_ticket=af.idadm WHERE (c.emitente=? OR c.remetente=?) GROUP BY t.num_ticket ORDER BY max(c.id) DESC");
	// 	$stmt->execute(array($user,$user));

	// 	while ($row = $stmt->fetch()) {
	// 		$lista[] = array("id" => $row["id"],
	// 			"usuario" => $row["usuario"],
	// 			"tipoUser" => $row["tipoUser"],
	// 			"data" => date('d/m/Y h:i', strtotime ($row["data"])),
	// 			"mensagem" =>  utf8_encode($row["mensagem"]),
	// 			"sts" => $row["sts"],
	// 			"tipo" => $row["tipo"],
	// 			"emitente" => $row["emitente"],
	// 			"remetente" => $row["remetente"],
	// 			"num_ticket" => $row["num_ticket"],
	// 			"msg_ticket" =>  utf8_encode($row["msg_ticket"]),
	// 			"status_ticket" => $row["status_ticket"],
	// 			"user_ticket" => $row["user_ticket"],
	// 			"adm_ticket" => $row["adm_ticket"],
	// 			"id_canal" => $row["id_canal"],
	// 			"nome_canal" => $row["nome_canal"],
	// 			"qt" => $row["qt"]
	// 		);
	// 	}			

	// 	return $lista;
	// }

	public function listaChatAll(array $geral)
	{

		$lista = array();
		foreach($geral["ticket"] as $array){
		$conn = $this->conn;
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stmt = $conn->prepare("SELECT c.mensagem,c.DATA,t.num_ticket AS ticket,c.emitente,c.sts,c.tipo,(
				SELECT
				nomeprofessor 
				FROM
				admpedagogo.funcionariologin 
				WHERE
				idadm = t.adm_ticket 
				) AS administrador 
				FROM
				chat c
				INNER JOIN ticket t ON ( t.num_ticket = :ticket OR t.num_ticket = :ticket ) 
				WHERE
				( c.emitente = :user	OR c.remetente = :user ) AND  c.DATA > :lastdata 
				ORDER BY
				c.id ASC");

		$stmt->execute(array("ticket"=>$array["ticket"], "user"=>$array["usuario"], "lastdata"=>$array["lastdate"]));
		while ($row = $stmt->fetch()) {
			$lista[$array["ticket"]][] = array(
				"ticket"=> $row["ticket"],
				"data" => $row["DATA"],
				"emitente" => $row["emitente"],
				"mensagem" =>  utf8_encode($row["mensagem"]),
				"sts" => $row["sts"],
				"tipo" => $row["tipo"],
				"administrador" => utf8_encode($row["administrador"])
			);
		}			
	}

	$geral["lastChat"] = $lista;
	print_r($geral);

	}
	
	// public function listaChat($user,$ticket)
	// {
	// 	$lista = array();

	// 	$conn = $this->conn;
	// 	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	// 	$stmt = $conn->prepare("SELECT
	// 		c.*,t.num_ticket as ticket,cc.nome_canal as canal,(
	// 			SELECT
	// 			nomeprofessor 
	// 			FROM
	// 			admpedagogo.funcionariologin 
	// 			WHERE
	// 			idadm = t.user_ticket 
	// 			) AS usuario,(
	// 			SELECT
	// 			nomeprofessor 
	// 			FROM
	// 			admpedagogo.funcionariologin 
	// 			WHERE
	// 			idadm = t.adm_ticket 
	// 			) AS administrador 
	// 			FROM
	// 			chat c
	// 			INNER JOIN ticket t ON ( t.num_ticket = c.emitente OR t.num_ticket = c.remetente ) INNER JOIN canal cc ON (t.canal_ticket = cc.id_canal)
	// 			WHERE
	// 			(
	// 				c.emitente = :user
	// 				AND c.remetente = :ticket 
	// 				OR c.remetente = :user
	// 				AND c.emitente = :ticket) 
	// 			ORDER BY
	// 			c.id ASC");
	// 	$stmt->execute(array("user"=> $user,"ticket"=>$ticket));

	// 	while ($row = $stmt->fetch()) {
	// 		$lista[] = array(
	// 			"id" => $row["id"],
	// 			"data" => $row["data"],
	// 			"emitente" => $row["emitente"],
	// 			"remetente" => $row["remetente"],
	// 			"mensagem" =>  utf8_encode($row["mensagem"]),
	// 			"grupo" => $row["grupo"],
	// 			"sts" => $row["sts"],
	// 			"tipo" => $row["tipo"],
	// 			"ticket" => $row["ticket"],
	// 			"canal" => $row["canal"], 
	// 			"usuario" => utf8_encode($row["usuario"]),
	// 			"administrador" => utf8_encode($row["administrador"])
	// 		);
	// 	}			

	// 	return $lista;
	// }
	
	// public function listaChatLastAll($user,$data,$ticket)
	// {
	// 	$lista = array();

	// 	$conn = $this->conn;
	// 	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	// 	$stmt = $conn->prepare("SELECT
	// 		c.*,t.num_ticket as ticket,cc.nome_canal as canal,(
	// 			SELECT
	// 			nomeprofessor 
	// 			FROM
	// 			admpedagogo.funcionariologin 
	// 			WHERE
	// 			idadm = t.user_ticket 
	// 			) AS usuario,(
	// 			SELECT
	// 			nomeprofessor 
	// 			FROM
	// 			admpedagogo.funcionariologin 
	// 			WHERE
	// 			idadm = t.adm_ticket 
	// 			) AS administrador 
	// 			FROM
	// 			chat c
	// 			INNER JOIN ticket t ON ( t.num_ticket = c.emitente OR t.num_ticket = c.remetente ) INNER JOIN canal cc ON (t.canal_ticket = cc.id_canal)
	// 			WHERE
	// 			( c.emitente = :user	OR c.remetente = :user ) AND c.data > :data  AND t.num_ticket = :ticket 
	// 			ORDER BY
	// 			c.id ASC");

	// 	$stmt->execute(array("user"=> $user,"data"=>$data, "ticket"=>$ticket));

	// 	while ($row = $stmt->fetch()) {
	// 		$lista[] = array(
	// 			"id" => $row["id"],
	// 			"data" => $row["data"],
	// 			"emitente" => $row["emitente"],
	// 			"remetente" => $row["remetente"],
	// 			"mensagem" =>  utf8_encode($row["mensagem"]),
	// 			"grupo" => $row["grupo"],
	// 			"sts" => $row["sts"],
	// 			"tipo" => $row["tipo"],
	// 			"ticket" => $row["ticket"],
	// 			"canal" => $row["canal"], 
	// 			"usuario" => utf8_encode($row["usuario"]),
	// 			"administrador" => utf8_encode($row["administrador"])
	// 		);
	// 	}			

	// 	return $lista;
	// }

	// public function listaChatLast($user,$ticket)
	// {
	// 	$lista = array();

	// 	$conn = $this->conn;
	// 	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	// 	$stmt = $conn->prepare("SELECT
	// 		c.*,(
	// 			SELECT
	// 			nomeprofessor 
	// 			FROM
	// 			admpedagogo.funcionariologin 
	// 			WHERE
	// 			idadm = t.user_ticket 
	// 			) AS usuario,(
	// 			SELECT
	// 			nomeprofessor 
	// 			FROM
	// 			admpedagogo.funcionariologin 
	// 			WHERE
	// 			idadm = t.adm_ticket 
	// 			) AS administrador 
	// 			FROM
	// 			chat c
	// 			INNER JOIN ticket t ON ( t.num_ticket = c.emitente OR t.num_ticket = c.remetente ) 
	// 			WHERE
	// 			(
	// 				c.emitente = :user 
	// 				AND c.remetente = :ticket 
	// 				OR c.remetente = :user
	// 				AND c.emitente = :ticket) 
	// 			ORDER BY
	// 			c.id DESC
	// 			LIMIT 1");
	// 	$stmt->execute(array("user"=> $user,"ticket"=>$ticket));

	// 	while ($row = $stmt->fetch()) {
	// 		$lista[] = array("id" => $row["id"],
	// 			"data" => $row["data"],
	// 			"emitente" => $row["emitente"],
	// 			"remetente" => $row["remetente"],
	// 			"mensagem" =>  utf8_encode($row["mensagem"]),
	// 			"grupo" => $row["grupo"],
	// 			"sts" => $row["sts"],
	// 			"tipo" => $row["tipo"],
	// 			"usuario" => utf8_encode($row["usuario"]),
	// 			"administrador" => utf8_encode($row["administrador"])
	// 		);
	// 	}			

	// 	return $lista;
	// }

	public function inserirChat($msg,$data,$emitente,$remetente){
		$conn = $this->conn;
		$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		$stmt = $conn->prepare("INSERT INTO chat (data, emitente, remetente, mensagem, sts, tipo) VALUES (?, ?, ?, ?, ?, ?)");
		$res = $stmt->execute(array($data,$emitente,$remetente,$msg,"e","txt"));

		if ($res) {
			return json_encode(array("sts"=>true,"id"=>$this->conn->lastInsertId()));
		}else{
			return json_encode(array("sts"=>false));

		}
	}

	public function lerMsg($emitente,$remetente){
		$conn = $this->conn;
		$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		$stmt = $conn->prepare("UPDATE chat SET sts = ? WHERE emitente = ? AND remetente = ?");
		$res = $stmt->execute(array("l",$emitente,$remetente));
	}


}


$DB = new DatabaseChat();
$DB->listaTickets(2);





?>