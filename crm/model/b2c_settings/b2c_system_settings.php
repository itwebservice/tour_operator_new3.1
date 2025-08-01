<?php
class b2c_system_settings{

    function save(){

        $entry_id = $_POST['entry_id'];
        $answer = $_POST['answer'];

        begin_t();
      

            $sq = mysqlQuery("UPDATE `b2c_generic_settings` SET `answer`='$answer' WHERE entry_id='$entry_id'");
          
            if(!$sq){
                rollback_t();
                echo "System settings not updated!";
                exit;
            }
      
        if($sq){
            commit_t();
            echo "System settings saved successfully!";
            exit;
        }
    }
}