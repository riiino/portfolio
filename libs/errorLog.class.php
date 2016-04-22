<?php 

class errorLog{
  
  public  $save_dir_path;
  
  function __construct(){
  
    global $mdb2;
    $this->mdb2                    = $mdb2;
  
  }

  function saveMailErrorLog($result){
  
    if ($result == 1) {
      $send_result = "Success";
    } else {
      $send_result = "Failure";
    }
  
    $message = "(".$send_result.")".date('Y-m-d H:i:s')."\n";
    error_log($message, 3, $this->save_dir_path."/php_script.log");
  
  }

  function insertDbErrorLog($affectedRows, $file_path, $line_number){
    $message = "(Insert Error)".date('Y-m-d H:i:s')." ".$file_path."[".$line_number."]\n";
    $message.= $affectedRows->message."\n";;
    error_log($message, 3, $this->save_dir_path."/php_script.log");
    
  }

}
