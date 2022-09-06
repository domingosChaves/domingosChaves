<?php 

include_once 'ConnDataBase.php';
date_default_timezone_set('America/Belem');
class DatabaseChat extends ConnDataBase
{
	public function listaTickets($user)
	{
		$conn = $this->conn;
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stmt = $conn->prepare("SELECT id, user_ticket, canal_ticket, status_ticket, date_ticket,nome_canal FROM ticket INNER JOIN canal  ON (canal_ticket = id_canal ) WHERE user_ticket = :user ORDER BY id ASC");
		$stmt->execute(array("user"=>$user));

		$lista = array();
		$geral = array();

		while ($row = $stmt->fetch()) {
			$lista[$row["id"]] = array(
				"nome_canal" => $row["nome_canal"],
				"ticket" => $row["id"],
				"usuario" => $row["user_ticket"],
				"canal" =>	$row["canal_ticket"],
				"lastdate" => $row["date_ticket"],
				"status" => $row["status_ticket"]
			);

			// $this->updateLastTicket($row["id"]);
		}

		echo json_encode($lista);
	}
	public function listaChatAll($geral)
	{
		$lista = array();		
		$conn = $this->conn;
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stmt = $conn->prepare("SELECT c.id,c.mensagem,c.DATA,t.id AS ticket,c.emitente,c.sts,c.tipo,(
				SELECT
				nomeprofessor 
				FROM
				admpedagogo.funcionariologin 
				WHERE
				idadm = t.adm_ticket 
				) AS administrador 
				FROM
				chat c
				INNER JOIN ticket t ON ( t.id = c.emitente OR t.id = c.remetente ) 
				WHERE
				((c.emitente = :user AND c.remetente = :ticket) OR (c.remetente = :user AND c.emitente = :ticket)) AND  c.DATA >= :lastdata 
				ORDER BY
				c.id ASC");

				$stmt->execute(array("ticket"=>$geral["ticket"], "user"=>$geral["usuario"], "lastdata"=>$geral["lastdate"]));
				while ($row = $stmt->fetch()) {
					$lista[ $row["id"]] = array(
						"_id" =>$row["id"],
						"text" =>  utf8_encode($row["mensagem"]),
						"ticket"=> $row["ticket"],
						"createdAt" => $row["DATA"],
						"user" => array("_id"=>$row["emitente"],"name"=>utf8_encode($row["administrador"]))
					);
			}

			echo json_encode($lista);
	}
	public function listaAll($geral)
	{
		$lista = array();
		
		$conn = $this->conn;
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stmt = $conn->prepare("SELECT c.id,c.mensagem,c.DATA,t.id AS ticket,c.emitente,c.sts,c.tipo,(
				SELECT
				nomeprofessor 
				FROM
				admpedagogo.funcionariologin 
				WHERE
				idadm = t.adm_ticket 
				) AS administrador 
				FROM
				chat c
				INNER JOIN ticket t ON ( t.id = c.emitente OR t.id = c.remetente ) 
				WHERE
				((c.emitente = :user ) OR (c.remetente = :user )) AND  c.DATA >= :lastdata 
				ORDER BY
				c.id ASC");

				$stmt->execute(array("user"=>$geral["user"], "lastdata"=>$geral["lastdate"]));
				while ($row = $stmt->fetch()) {
					$lista[ $row["id"]] = array(
						"_id" =>$row["id"],
						"text" =>  utf8_encode($row["mensagem"]),
						"ticket"=> $row["ticket"],
						"createdAt" => $row["DATA"],
						"user" => array("_id"=>$row["emitente"],"name"=>utf8_encode($row["administrador"]))
					);
			}

			echo json_encode($lista);
	}
	public function listafristAll($geral)
	{
		$lista = array();
		
		$conn = $this->conn;
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stmt = $conn->prepare("SELECT c.id,c.mensagem,c.DATA,t.id AS ticket,c.emitente,c.sts,c.tipo,(
				SELECT
				nomeprofessor 
				FROM
				admpedagogo.funcionariologin 
				WHERE
				idadm = t.adm_ticket 
				) AS administrador 
				FROM
				chat c
				INNER JOIN ticket t ON ( t.id = c.emitente OR t.id = c.remetente ) 
				WHERE
				((c.emitente = :user ) OR (c.remetente = :user ))  
				ORDER BY
				c.id ASC");

				$stmt->execute(array("user"=>$geral["user"]));
				while ($row = $stmt->fetch()) {
				$lista[ $row["id"]] = array(
					"_id" =>$row["id"],
					"text" =>  utf8_encode($row["mensagem"]),
					"ticket"=> $row["ticket"],
					"createdAt" => $row["DATA"],
					"user" => array("_id"=>$row["emitente"],"name"=>utf8_encode($row["administrador"]))
				);
			}

			echo json_encode($lista);
	}
	public function listaHistorico($user)
	{
		$lista = array();

		$conn = $this->conn;
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stmt = $conn->prepare("SELECT max(t.id) as id,af.nomeprofessor AS usuario,af.tipo AS tipoUser,max(c.data) AS data,(
			SELECT mensagem FROM chat WHERE id=max(c.id) AND tipo='txt') AS mensagem,(
			SELECT sts FROM chat WHERE id=max(c.id)) AS sts,(
			SELECT tipo FROM chat WHERE id=max(c.id)) AS tipo,(
			SELECT emitente FROM chat WHERE id=max(c.id)) AS emitente,(
			SELECT remetente FROM chat WHERE id=max(c.id)) AS remetente,t.msg_ticket,t.status_ticket,t.user_ticket,t.adm_ticket,cal.id_canal,cal.nome_canal,(
			SELECT count(id) FROM chat WHERE (emitente=t.id AND remetente=t.user_ticket) AND sts='e') AS qt FROM chat c JOIN ticket t ON (t.id=c.emitente OR t.id=c.remetente) JOIN canal cal ON cal.id_canal=t.canal_ticket JOIN admpedagogo.funcionariologin af ON t.user_ticket=af.idadm WHERE (c.emitente=:user OR c.remetente=:user) GROUP BY t.id ORDER BY max(c.id) DESC");
		$stmt->execute(array("user" => $user["emitente"]));

		while ($row = $stmt->fetch()) {
			$lista[] = array("id" => $row["id"],
				"usuario" => $row["usuario"],
				"tipoUser" => $row["tipoUser"],
				"data" => $row["data"],
				"mensagem" =>  utf8_encode($row["mensagem"]),
				"sts" => $row["sts"],
				"tipo" => $row["tipo"],
				"emitente" => $row["emitente"],
				"remetente" => $row["remetente"],
				"num_ticket" => $row["id"],
				"msg_ticket" =>  utf8_encode($row["msg_ticket"]),
				"status_ticket" => $row["status_ticket"],
				"user_ticket" => $row["user_ticket"],
				"adm_ticket" => $row["adm_ticket"],
				"id_canal" => $row["id_canal"],
				"nome_canal" => $row["nome_canal"],
				"qt" => $row["qt"]
			);
		}			

		echo json_encode($lista);
	}
	public function listaHistoricoAdm()
	{
		$lista = array();

		$conn = $this->conn;
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stmt = $conn->prepare("SELECT max(t.id) as id,af.nomeprofessor AS usuario,af.tipo AS tipoUser,max(c.data) AS data,(
			SELECT mensagem FROM chat WHERE id=max(c.id) AND tipo='txt') AS mensagem,(
			SELECT sts FROM chat WHERE id=max(c.id)) AS sts,(
			SELECT tipo FROM chat WHERE id=max(c.id)) AS tipo,(
			SELECT emitente FROM chat WHERE id=max(c.id)) AS emitente,(
			SELECT remetente FROM chat WHERE id=max(c.id)) AS remetente,t.msg_ticket,t.status_ticket,t.user_ticket,t.adm_ticket,cal.id_canal,cal.nome_canal,(
			SELECT count(id) FROM chat WHERE (emitente=t.id AND remetente=t.user_ticket) AND sts='e') AS qt FROM chat c JOIN ticket t ON (t.id=c.emitente OR t.id=c.remetente) JOIN canal cal ON cal.id_canal=t.canal_ticket JOIN admpedagogo.funcionariologin af ON t.user_ticket=af.idadm GROUP BY t.id ORDER BY max(c.id) DESC");
		$stmt->execute();

		while ($row = $stmt->fetch()) {
			$lista[] = array("id" => $row["id"],
				"usuario" => $row["usuario"],
				"tipoUser" => $row["tipoUser"],
				"data" => $row["data"],
				"mensagem" =>  utf8_encode($row["mensagem"]),
				"sts" => $row["sts"],
				"tipo" => $row["tipo"],
				"emitente" => $row["emitente"],
				"remetente" => $row["remetente"],
				"num_ticket" => $row["id"],
				"msg_ticket" =>  utf8_encode($row["msg_ticket"]),
				"status_ticket" => $row["status_ticket"],
				"user_ticket" => $row["user_ticket"],
				"adm_ticket" => $row["adm_ticket"],
				"id_canal" => $row["id_canal"],
				"nome_canal" => $row["nome_canal"],
				"qt" => $row["qt"]
			);
		}			

		echo json_encode($lista);
	}
	public function updateLastTicket($id)
	{
		$conn = $this->conn;
		$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		$stmt = $conn->prepare("UPDATE ticket SET date_ticket = :dt WHERE id = :id");
		$res = $stmt->execute(array("id"=>$id,"dt"=> date("Y-m-d H:i:s")));
	}
	public function inserirChat($msg,$data,$canal,$emitente,$remetente,$nome)
	{
		$conn = $this->conn;
		$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		$stmt = $conn->prepare("INSERT INTO chat (data,grupo, emitente, remetente, mensagem, sts, tipo) VALUES (?, ?, ?, ?, ?, ?, ?)");
		$res = $stmt->execute(array($data,$canal,$emitente,$remetente,$msg,"e","txt"));

		if ($res) {
			$lista  = array(
				"_id" =>$this->conn->lastInsertId(),
				"text" =>  utf8_encode($msg),
				"ticket"=> $remetente,
				"createdAt" => $data,
				"user" => array("_id"=> $emitente,"name"=>$nome)
				);
			return json_encode($lista);
		}
	}
	public function inserirTicket($dados)
	{
		$conn = $this->conn;
		$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		$stmt = $conn->prepare("INSERT INTO ticket (canal_ticket, tipo_ticket, user_ticket, msg_ticket, status_ticket, date_ticket) VALUES (?,?,?,?,?,?)");
		$res = $stmt->execute(array($dados->departamento,$dados->subdepartamento,$dados->user,$dados->message,1,date('Y-m-d h:i:s')));
		if ($res) {

			return $this->inserirChat($dados->msgAtendimento,date('Y-m-d h:i:s'),$dados->departamento,$dados->user,$this->conn->lastInsertId(),$dados->usuario);

		}else{
			return array("sts"=>false);

		}
	}	
	public function lerMsg($geral)
	{
		$conn = $this->conn;
		$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		$stmt = $conn->prepare("UPDATE chat SET sts = ? WHERE emitente = ? AND remetente = ?");
		$res = $stmt->execute(array("l",$geral["ticket"],$geral["user"]));
	}
	public function contador($usuario)
	{
		try {
		$conn = $this->conn;
		$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		$stmt = $conn->prepare("SELECT COUNT(id) as qt  from chat WHERE emitente <> :user AND remetente = :user AND sts = 'e'");
		$res = $stmt->execute(array("user"=>$usuario["user"]));

		$count = 0;
		while ($row = $stmt->fetch()) {
			$count = $row[0];
		}
		echo json_encode(array("qt"=>$count));

		} catch (Exception $th) {
			//throw $th;
		}
	}

}

?>