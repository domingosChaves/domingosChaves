<?php 
include_once 'ConnDataBase.php';

class DatabaseCanais extends ConnDataBase
{
	
	public function canais()
	{
		$lista = array();

		$conn = $this->conn;
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stmt = $conn->prepare("SELECT * FROM canal");
		$stmt->execute();

		while ($row = $stmt->fetch()) {
			$lista[] = array("id"=>$row["id_canal"],"label"=>$row["nome_canal"],"subcanais" => $this->subcanais($row["id_canal"]));
		}			

		return $lista;
	}

	public function subcanais($id)
	{
		$lista = array();
		$conn = $this->conn;
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stmt = $conn->prepare("SELECT * FROM solicitacao_cham WHERE canal = ?");
		$stmt->execute(array($id));

		while ($row = $stmt->fetch()) {
		$lista[] = array("id"=>$row["id"],"label"=> utf8_encode($row["descricao"]));
		}

		return $lista;  
	}
}








?>