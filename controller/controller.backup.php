<?php

require_once "controller.sanitizer.php";
require_once "controller.db.php";
require_once "controller.auth.php";
require_once "../model/model.backup.php";

$backup = new Backup();
$auth = new Auth();
$mode = Sanitizer::filter('mode', 'get');
$user_name = $auth->getSession("name");


switch($mode) {
    
    case "table";
        $backup = $backup->getAllDatabase();
        foreach($backup as $k=>$v) {
            $backup[$k]['action'] = '<button class="btn btn-sm btn-primary" type="button" onclick="download_db(\''.$v['database_name'].'\')"><i class="material-icons myicon-lg">download</i></button> ';
        }
        $response = array("data" => $backup);
        break;
    
    case "backup";
        $sqlScript = "";

        $sqlScript .= 'SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";'."\n";
		$sqlScript .= 'SET AUTOCOMMIT = 0;'."\n";
		$sqlScript .= 'START TRANSACTION;'."\n";
		$sqlScript .= 'SET time_zone = "+00:00";'."\n\n";
        $table = "";
        // show all tables
        $tables = $backup->getAllTables();
        foreach($tables as $k=>$v) {
            $table = $v['Tables_in_pmc_wms'];

            // create tables
            $create_table = $backup->getCreatetable($table);
            $sqlScript .= "\n\n" . $create_table . ";\n\n";

            // inserting data
            $countdata = $backup->getCountfromTable($table);
            $tableVal = $backup->getdatafromTable($table);
            $newline = 0;
            if($countdata != 0){

                $newline += 500;
                $sqlScript .= "INSERT INTO `$table` (";

                $countCol = $backup->getCountcolumnTable($table);
                $columnVal = $backup->getcolfromTable($table);
                $a = 0;
                $i[] = "";
                foreach($columnVal as $k=>$w){
                    $i[$a] = $w['Field'];
                    $a++;
                    if($a >= $countCol){
                        $sqlScript .= "`".$w['Field']."`";
                    }else{
                        $sqlScript .= "`".$w['Field']."`,";
                    }
                }

                $sqlScript .= ") VALUES\n";
                $b = 0;
                foreach($tableVal as $k=>$z){
                    $b++;
                    $sqlScript .= "(";
                    $c = 0;
                    $d = 0;
                    for($d=0;$d<$countCol;$d++){
                        $field_name = $i[$d];
                        $c++;
                        if($c >= $countCol){
                            $sqlScript .= "'" .addslashes($z[$field_name]). "'";
                        }else{
                            $sqlScript .= "'" .addslashes($z[$field_name]). "',";
                        }
                        
                    }
                    if($b >= $countdata){
                        $sqlScript .= ");\n"; 
                    }else{
                        if($b==$newline){
                            $sqlScript .= ");\n"; 
                            $newline += 500;

                            //new insert line here
                            $sqlScript .= "INSERT INTO `$table` (";
                            $countCol = $backup->getCountcolumnTable($table);
                            $columnVal = $backup->getcolfromTable($table);
                            $a = 0;
                            foreach($columnVal as $k=>$w){
                                $a++;
                                if($a >= $countCol){
                                    $sqlScript .= "`".$w['Field']."`";
                                }else{
                                    $sqlScript .= "`".$w['Field']."`,";
                                }
                            }
                            $sqlScript .= ") VALUES\n";

                        }else{
                            $sqlScript .= "),\n";
                        }
                        
                    }
                      
                }
            }

        }

        $sqlScript .= "COMMIT;";

        if(!empty($sqlScript))
		{
		    // Save the SQL script to a backup file
		    $backup_file_name = 'pmc_wms' . date('Ymdhis') . '.sql';
		    $fileHandler = fopen('../dbase/'.$backup_file_name, 'w+');
		    $number_of_lines = fwrite($fileHandler, $sqlScript);
		    fclose($fileHandler);
		}
        $date_today = date('F d, Y h:i:s');
        $backup->SaveDatabase($backup_file_name,$date_today,$user_name);
        $response = array("code"=>1,"message"=>"Successful");
        break;

}


echo json_encode($response);

