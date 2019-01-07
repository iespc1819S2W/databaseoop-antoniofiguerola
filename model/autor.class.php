<?php
$base = __DIR__ . '/..';
require_once("$base/lib/resposta.class.php");
require_once("$base/lib/database.class.php");

class Autor
{
    private $conn;       //connexió a la base de dades (PDO)
    private $resposta;   // resposta
    
    public function __CONSTRUCT()
    {
        $this->conn = Database::getInstance()->getConnection();      
        $this->resposta = new Resposta();
    }
    
    public function getAll($orderby="id_aut")
    {
		try
		{
			$result = array();                        
			$stm = $this->conn->prepare("SELECT id_aut,nom_aut,fk_nacionalitat FROM autors ORDER BY $orderby");
			$stm->execute();
            $tuples=$stm->fetchAll();
            $this->resposta->setDades($tuples);    // array de tuples
			$this->resposta->setCorrecta(true);       // La resposta es correcta        
            return $this->resposta;
		}
        catch(Exception $e)
		{   // hi ha un error posam la resposta a fals i tornam missatge d'error
			$this->resposta->setCorrecta(false, $e->getMessage());
            return $this->resposta;
		}
    }
    
    public function get($id)
    {
        try {
            $stm = $this->conn->prepare("SELECT id_aut, nom_aut, fk_nacionalitat FROM autors WHERE ID_AUT =:id");
            $stm->bindValue(':id', $id);
            $stm->execute();
            $autor=$stm->fetch();
            $this->resposta->setDades($autor);
			$this->resposta->setCorrecta(true);      
            return $this->resposta;
		}
        catch(Exception $e) {   // hi ha un error posam la resposta a fals i tornam missatge d'error
            $this->resposta->setCorrecta(false, $e->getMessage());
            return $this->resposta;
        }
    }

    
    public function insert($data)
    {
		try 
		{
                $sql = "SELECT max(id_aut) as N from autors";
                $stm=$this->conn->prepare($sql);
                $stm->execute();
                $row=$stm->fetch();
                $id_aut=$row["N"]+1;
                $nom_aut=$data['nom_aut'];
                $fk_nacionalitat=$data['fk_nacionalitat'];

                $sql = "INSERT INTO autors
                            (id_aut,nom_aut,fk_nacionalitat)
                            VALUES (:id_aut,:nom_aut,:fk_nacionalitat)";
                
                $stm=$this->conn->prepare($sql);
                $stm->bindValue(':id_aut',$id_aut);
                $stm->bindValue(':nom_aut',$nom_aut);
                $stm->bindValue(':fk_nacionalitat',!empty($fk_nacionalitat)?$fk_nacionalitat:NULL,PDO::PARAM_STR);
                $stm->execute();
            
       	        $this->resposta->setCorrecta(true);
                return $this->resposta;
        }
        catch (Exception $e) 
		{
                $this->resposta->setCorrecta(false, "Error insertant: ".$e->getMessage());
                return $this->resposta;
		}
    }   
    
    public function update($data)
    {
        try 
		{
                $id_aut=$data['id_aut'];
                $nom_aut=$data['nom_aut'];
                $fk_nacionalitat=$data['fk_nacionalitat'];

                $sql = "UPDATE autors SET nom_aut =:nom_aut, fk_nacionalitat =:fk_nacionalitat WHERE id_aut =:id_aut";

                // UPDATE `autors` SET `NOM_AUT` = '$valorGuardar', `FK_NACIONALITAT` = null WHERE `ID_AUT` = $idguardar";
                
                $stm=$this->conn->prepare($sql);
                $stm->bindValue(':id_aut',$id_aut);
                $stm->bindValue(':nom_aut',$nom_aut);
                $stm->bindValue(':fk_nacionalitat',!empty($fk_nacionalitat)?$fk_nacionalitat:NULL,PDO::PARAM_STR);
                $stm->execute();
            
       	        $this->resposta->setCorrecta(true);
                return $this->resposta;
        }
        catch (Exception $e) 
		{
                $this->resposta->setCorrecta(false, "Error actualitzant: ".$e->getMessage());
                return $this->resposta;
		}
    }

    
    
    public function delete($id)
    {
        try 
		{
            $id_aut=$id;

            $sql = "DELETE FROM autors WHERE id_aut =:id_aut";
            
            $stm=$this->conn->prepare($sql);
            $stm->bindValue(':id_aut',$id_aut);
            $stm->execute();
            
       	    $this->resposta->setCorrecta(true);
            return $this->resposta;
        }
        catch (Exception $e) 
		{
            $this->resposta->setCorrecta(false, "Error eliminant: ".$e->getMessage());
            return $this->resposta;
		}

    }

    public function filtra($where,$orderby,$offset,$count)
    {
        // TODO
        try 
		{
            $sql = "SELECT id_aut,nom_aut,fk_nacionalitat FROM autors";
            $sqlCount="SELECT COUNT(*) AS total FROM autors";
            
            if (!empty($where)) {
                
                // $sql = $sql." WHERE ID_AUT = :where";
                $sql = $sql." WHERE ID_AUT = :where OR NOM_AUT LIKE :whereLike";
                $sqlCount = $sqlCount." WHERE ID_AUT = :where OR NOM_AUT LIKE :whereLike";
            }
            
            if (!empty($orderby)) {
                $sql = $sql." ORDER BY $orderby";
            }
            
            $sql = $sql." LIMIT $offset, $count";

            
            $stm=$this->conn->prepare($sql);
            $stm->bindValue(':where', $where);
            $stm->bindValue(':whereLike', "%$where%");
            $stm->execute();
            
            $tuples=$stm->fetchAll();
            $this->resposta->setDades($tuples);
            $this->resposta->setCorrecta(true);

            $stmC=$this->conn->prepare($sqlCount);
            $stmC->bindValue(':where', $where);
            $stmC->bindValue(':whereLike', "%$where%");
            $stmC->execute();
            $num=$stmC->fetchAll();
            $this->resposta->setRegistres($num[0]["total"]);

            return $this->resposta;
        }
        catch (Exception $e) 
		{
            $this->resposta->setCorrecta(false, "Error filtrant: ".$e->getMessage());
            return $this->resposta;
		}

    }
    
          
}
