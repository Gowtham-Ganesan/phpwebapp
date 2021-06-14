<?php
include "config.php";
?>

<!DOCTYPE html>
<html>      
<head>
    <title>Upload CSV File into Database</title>
    <link rel="stylesheet" href="file.css">
    <?php
    if(isset($_POST['but_import'])){
    if (isset($_FILES['importfile']['error']) && $_FILES['importfile']['error'] == 4) {
          echo "Please select an image file ..";
  
        }
        else{
        $target_dir = "uploads/";
        $target_file = $target_dir.basename($_FILES['importfile']['name']);
        $filename=$_FILES['importfile']['name'];
        $table = basename($filename,".csv");
        $records = file($filename);
        $noofrec = count($records);
        $pass = -1;
        $percent =0;
        $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

        $uploadOk = 1;
        if($imageFileType != 'csv'){
            $uploadOk = 0;
        }

        if($uploadOk != 0){
            if(move_uploaded_file($_FILES['importfile']['tmp_name'],$target_file)){

                $fileexists = 0;
                if(file_exists($target_file)){
                    $fileexists=1;
                }

                if($fileexists == 1){

                    $file = fopen($target_file,"r");
                    $index=0;

                    $importData_arr = array();

                    while (($data = fgetcsv($file,1000,",")) != FALSE) {
                        $num = count($data);
                        for($c=0;$c<$num;$c++){
                            $importData_arr[$index][]=$data[$c];
                        }
                        $index++;
                    }
                    fclose($file);
                    
                    $skip = 0;
                    $dat=[];
                    $str='';
                    foreach($importData_arr as $data){

                        if($skip == 0){
                            for($c=0;$c<$num;$c++){
                                $new = array_push($dat,$data[$c]);
                                if($data[$c]=='password'){
                                    $pass = $c;
                                }
                            }

                        }
                        if($skip != 0){
                            for($c=0;$c<$num;$c++){
                                $str1="`$dat[$c]` varchar(50) NOT NULL, ";
                                $str=$str.$str1;
                            }
                            }
                        if($skip == 1){
                            break;
                        }
                        $skip++;
                    }
                    $str=substr_replace($str,"",-2);
                    
                       
                    $ntable = "CREATE TABLE $table ($str)";
                    if ($con->query($ntable) == TRUE) {
                        echo "";
                    }

                    mysqli_query($con,"TRUNCATE TABLE $table");
                    $skip = 0;
                    foreach($importData_arr as $data){

                        if($skip != 0){
                            $val="";
                            for($c=0;$c<$num;$c++){
                                if($pass == $c){
                                    $data[$c] = md5($data[$c]);
                                    $val = $val."'$data[$c]',";
                                }
                                else
                                    $val = $val."'$data[$c]',";
                            }
                            $val=substr_replace($val,"",-1);                            
                            $res = "insert into $table values($val)";
                            mysqli_query($con,$res);
                            $percent = intval($skip/($noofrec-1) * 100)."%";
                        }
                        $skip++;
                    }

                    if(file_exists($target_file)){
                        unlink($target_file);
                    }           
                  if($skip == $noofrec){
                    echo '<script type="text/javascript">';
                    echo ' alert("All records in CSV file is uploaded to Database successfully")';  
                    echo '</script>';
                      
                  }
                  else{
                      $st=strval($skip);
                      $st=$st." number of records in CSV file is uploaded to Database";
                    echo '<script type="text/javascript">';
                    echo ' alert('.$st.')';  
                    echo '</script>';
                  }
                }
            }
        }
        }
    
    }
    ?>
</head>
<body>
<h1>Batman Server</h1>
<h2>Upload the CSV data to database</h2>
<h3>
        <form method='post' action='' enctype="multipart/form-data">
        <label for="myfile">Select a file:</label>
            <input type="file" name="importfile"><br><br><br>
            <h4><input type="submit" name="but_import" value='Start'></h4>
        </form>
</h3>
<div id="progress" style="width:480px;border:1px solid #ccc;"></div>

<div id="information" style="width"></div>
<?php

if(isset($_POST['but_import'])){
if (!(isset($_FILES['importfile']['error']) && $_FILES['importfile']['error'] == 4)) {
  $filename=$_FILES['importfile']['name'];
$table = basename($filename,".csv");
$records = file($filename);
$noofrec = count($records);
while($skip<=$noofrec){
    $result = mysqli_query($con,"SELECT * FROM $table");
    $skip = mysqli_num_rows($result);
    $percent = intval(($skip/$noofrec * 100)+2)."%";

    echo '<script language="javascript">
    document.getElementById("progress").innerHTML="<div style=\"width:'.$percent.';background-color:#ddd;\">&nbsp;</div>";
    </script>';
    echo str_repeat(' ',1024*64);
    flush();
}
}
}

?>

</body>
</html> 