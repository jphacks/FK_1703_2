<?php

    if ($_FILES["detection"]["error"] > 0)
    {
        echo "Return Code: " . $_FILES["detection"]["error"] . "<br>";
    }
    else
    {
        if (file_exists($_FILES["detection"]["name"]))
        {
            echo $_FILES["detection"]["name"] . " already exists. ";
        }
        else
        {
            move_uploaded_file($_FILES["detection"]["tmp_name"],$_FILES["detection"]["name"]);
            echo "Stored in: ". $_FILES["detection"]["name"];
        }
    }
    
?>
