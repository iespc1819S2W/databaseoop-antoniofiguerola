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

            if (!empty($where)) {
                $sql += $where;
            }

            if (!empty($orderBy)) {
                $sql += $orderBy;
            }

            if (!empty())

            // $id_aut=$id;

            // $sql = "DELETE FROM autors WHERE id_aut =:id_aut";
            
            // $stm=$this->conn->prepare($sql);
            // $stm->bindValue(':id_aut',$id_aut);
            // $stm->execute();
            
       	    // $this->resposta->setCorrecta(true);
            // return $this->resposta;
        }
        catch (Exception $e) 
		{
            $this->resposta->setCorrecta(false, "Error filtrant: ".$e->getMessage());
            return $this->resposta;
		}

        // $sql="SELECT ID_AUT, NOM_AUT, FK_NACIONALITAT FROM `autors`";
        // $where="";
        // $valor = "";
        // $numRegPag = isset($_POST['numRegPag'])?$_POST['numRegPag']:20;
        // // Cercar
        // if (isset($_POST['cercar']) && $_POST['cercar'] != "") {
        //     $valor = $mysqli->real_escape_string($_POST['cercar']);
        //     $where=" WHERE ID_AUT = '$valor' OR NOM_AUT LIKE '%$valor%'";
        // }
        // // Consulta paginacio
        // $orderBy=" ORDER BY $ordre"; 
        // $result = $mysqli->query($sql.$where);
        // $numRegistres = mysqli_num_rows($result);
        // $numPaginas = ceil($numRegistres/$numRegPag);
        // $iniciTuples = ($pagina - 1) * $numRegPag;
        // $limit = " LIMIT $iniciTuples , $numRegPag";
        // $sql=$sql.$where.$orderBy.$limit;
        // $result = $mysqli->query($sql);
    }
    
          
}
