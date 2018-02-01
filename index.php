<?php
class connection{
    
    private $server="localhost";
    private $user="root";
    private $pwd="";
    private $database="consumoelectrico";
    private $con;
    
    public function connect(){
        
        $this->con = mysql_connect($this->server,$this->user,$this->pwd);
        mysql_select_db($this->database,$this->con);
        
    }
    
    public function showData($query){
        
        $res = mysql_query($query,$this->con);
        
        mysql_close();
        
        echo "<table border=1>";
        
        while($row = mysql_fetch_row($res)){
            
            echo "<tr>";
            
            for($x=0;$x<count($row);$x++){
                
                echo "<td>";
                echo $row[$x];
                echo "</td>";
                                
            }
            
            echo "</tr>";
            
        }
        
        echo "</table>";
        
    }
    
    public function getTypeOfFields($tabla){
        
        $query = "SELECT * FROM ".$tabla;
        
        $res = mysql_query($query,$this->con);
        
        //mysql_close();
        
        $fields = mysql_num_fields($res);
        
        $type_of_fields = array();
        
        for ($i=1; $i < $fields; $i++) {
            
            $type_of_fields[$i]['type'] = mysql_field_type($res, $i);
            $type_of_fields[$i]['name'] = mysql_field_name($res, $i);
            $type_of_fields[$i]['len'] = mysql_field_len($res, $i);
            $type_of_fields[$i]['flags'] = mysql_field_flags($res, $i);
        }
        
        return $type_of_fields;
        
    }
    
    public function showForm($tabla){
        
        $array_of_fields = $this->getTypeOfFields($tabla);
        
        //Create form
        echo "<form method='GET'>";
        echo "<table>";
        
        for ($i=1; $i<=count($array_of_fields); $i++) {
            
            $type = $array_of_fields[$i]['type'];
            $name = $array_of_fields[$i]['name'];
            $len = $array_of_fields[$i]['len'];
            $flags = $array_of_fields[$i]['flags'];
            
            //echo $type . " " . $name . " " . $len . " " . $flags . "<br>";
            
            echo "<tr>";
            echo "<td>";
            echo "<label>".$name."</label>";
            echo "</td>";
            echo "<td>";
            //echo "<input type='text' name=".$name." maxlength=".$len." required>";
            if($type=="int"){echo "<input type='number' name=".$name." min='0' maxlength='".$len."'>";}
            else{echo "<input type='text' name=".$name." maxlength=".$len.">";}            
            echo "</td>";
            echo "<td>";
            echo "<i>".$type." ".$flags."</i>";
            echo "</td>";
            echo "</tr>";            
            
        }
        
        echo "<tr>";
        echo "<td>";
        echo "<input type='submit' value='Send'>";
        echo "</td>";
        echo "</tr>";
        
        echo "</table>";
        echo "</form>";
        
    }
    
    private function _GenerateFieldsString($fields){
        
        $string = "";
        
        for($i=1;$i<count($fields);$i++){
            
            if(($i+1)!=count($fields)){
                $string .= $fields[$i]['name'].", ";
            }else{
                $string .= $fields[$i]['name'];
            }
            
        }
        
        return $string;
        
    }
    
    private function _GenerateValuesString($fields,$values){
        
        $string = "";
        
        for($i=1;$i<count($fields);$i++){
            
            if(($i+1)!=count($fields)){
                
                if($fields[$i]['type']=='string'){
                    
                    $string .= "'".$values[$fields[$i]['name']]."', ";
                    
                }else{
                    
                    $string .= $values[$fields[$i]['name']].", ";
                    
                }
                    
            
            }else{
                
                if($fields[$i]['type']=='string'){
                    
                    $string .= "'".$values[$fields[$i]['name']]."'";
                    
                }else{
                    
                    $string .= $values[$fields[$i]['name']];
                    
                }
                
            }
            
        }
        
        return $string;
        
    }
    
    public function insertData($fields,$values,$table){
        
        $str_fields = $this->_GenerateFieldsString($fields);
        $str_values = $this->_GenerateValuesString($fields,$values);
        
        $query = "INSERT INTO $table ($str_fields) VALUES ($str_values);";
        
        //mysql_query($query,$this->con);
        
    }
    
}

$con = new connection();

$con->connect();

$table = "clientes";

if($_REQUEST){
    
    echo "Datos recibidos";
    
    $fields = $con->getTypeOfFields($table);
    $values = $_REQUEST;
    
    $con->insertData($fields, $values,$table);
    
}

$query = "SELECT * FROM ".$table;

//$query = "SELECT a.Codigo,a.Nombre,a.Apellido,a.Apellido2,a.Calle,a.Numero,a.Piso,a.Metros,b.Poblacion,c.Provincia "
//        . "FROM clientes as a, poblaciones as b, provincias as c WHERE a.CodigoPoblacion=b.CodigoPoblacion AND b.CodigoProvincia=c.CodigoProvincia";


$con->showData($query);
$con->showForm($table);

echo "<br>";


?>