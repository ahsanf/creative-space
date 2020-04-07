<?php
    include("../../../config.php");
    $courses = get_courses("all","c.fullname ASC");
    
    foreach($courses as $key=>$value){
        if($value->id != 1){
            echo "<option value='".$value->id."'>".$value->fullname."</option>";
        }
    }
    return;
?>
