<?php
/**
 * zip - Create Zip File with directory content
 * Author : Naval Kishor
 * Version: 1.0
 * Updated On: May 15, 2022
 */

// set password
$_require_password     = false; // Set it to true if you want to use a password to access this script. Password can be set in the next variable.
$_password             = ''; // Enter any string as password. You'll need to use this password as value of password parameter if you enable require_password option above

if($_require_password){
    $zipcf_pass = htmlspecialchars($_REQUEST['password']);
    if(empty($zipcf_pass) || $zipcf_pass != $_password){
        die();
    }
}

function getDirItems($dir, $recursive_display = false, &$results = array()){
    $files = scandir($dir);
    foreach($files as $key => $value){
        $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
        list($unused_path, $used_path) = explode(basename(__DIR__).'/', $path);
        $file_name = $dir.DIRECTORY_SEPARATOR.$value;
        if(!is_dir($path)) {
            $results[] = $used_path;
        } else if($value != "." && $value != "..") {
            // if recursive_display is set to true, it will display all the files inside every directory
            if($recursive_display){
                getDirItems($path, true, $results);
            }
            $results[] = $value.'/';
        }
    }
    return $results;
}

/* creates a compressed zip file */
function generate_zip_file($files = array(),$destination = '',$overwrite = false) {
    //if the zip file already exists and overwrite is false, return false
    if(file_exists($destination) && !$overwrite) { return false; }
    //vars
    $valid_files = array();
    //if files were passed in...
    if(is_array($files)) {
        //cycle through each file
        foreach($files as $file) {
            //make sure the file exists
            if(file_exists($file)) {
                $valid_files[] = $file;
            }
        }
    }
    //if we have good files...
    if(count($valid_files)) {
        //create the archive
        $zip = new ZipArchive();
        if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
            return false;
        }
        //add the files
        foreach($valid_files as $file) {
            if (file_exists($file) && is_file($file)){
                $zip->addFile($file,$file);
            }
        }
        //debug
        //echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;

        //close the zip -- done!
        $zip->close();

        //check to make sure the file exists
        return file_exists($destination);
    }
    else
    {
        return false;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Zip PHP 2.0 - Create a Zip with contents in the current Direcory (php script)</title>
    <meta name="robots" content="noindex">
    <style type="text/css">
        body{
            font-family: arial;
            font-size: 14px;
            padding: 0;
            margin: 0;
            text-align: left;
            padding-bottom: 50px;
        }
        h3{
            text-align: center;
        }
        .container{
            width: 600px;
            margin: 100px auto 0 auto;
            max-width: 100%;
        }
        label{
            font-weight: bold;
            margin: 10px 0;
        }
        input[type="text"]{
            border: 1px solid #eee;
            padding: 10px;
            display: block;
            margin: 10px auto;
            width:100%;
        }
        input[type="checkbox"]{
            margin: 10px 0;
        }
        label.fs-label{
            padding-left: 5px;
            font-weight: normal;
        }
        input[type="submit"]{
            padding: 10px 20px;
            display: block;
            margin: 20px auto;
            border: 2px solid green;
            background: #fff;
            width: 100%;
            font-weight: bold;
        }
        .copyright{
            position: fixed;
            bottom:0;
            background: #333;
            color: #fff;
            width: 100%;
            padding: 10px 20px;
            text-align: center;
        }
        .copyright a{
            color: #eee;
        }
        #zip-file-name {border:2px solid #333;}
        .text-center {
    text-align: center;
}
    </style>

</head>
<body>
    <div class="text-center">
    <div itemscope itemtype='http://schema.org/Person' class='fiverr-seller-widget' style='display: inline-block;'>
       <a itemprop='url' href=https://www.fiverr.com/navalkishor709 rel="nofollow" target="_blank" style='display: inline-block;'>
        <div class='fiverr-seller-content' id='fiverr-seller-widget-content-5aeb12eb-19d5-455f-b939-74350ae8acb8' itemprop='contentURL' style='display: none;'></div>
        <div id='fiverr-widget-seller-data'>
            <div itemprop='jobtitle'><b>Naval Kishor</b></div><br/>
            <div itemprop='description'>I am a professional freelancer. I shall create anything for you . I have good technical skills and i am very honest to my work. Please let me know how i can help you.

                If you hire me, I will be responsible for completing your work within the stipulated time and with complete confidence. Hire me for your project, I promise to give you the best  gift.
            </div>
        </div>
    </a>
</div>
</div>
        <script id='fiverr-seller-widget-script-5aeb12eb-19d5-455f-b939-74350ae8acb8' src='https://widgets.fiverr.com/api/v1/seller/navalkishor709?widget_id=5aeb12eb-19d5-455f-b939-74350ae8acb8' data-config='{"category_name":"Graphics \u0026 Design"}' async='true' defer='true'></script>
    <div class="container">
        <h3>Make zip file with current directory! </h3>
        <?php
            if(isset($_POST['zip_file_name'])){
                if(!empty($_POST['zip_file_name'])){
                    ini_set('max_execution_time', 10000);

                    $get_name = $_POST['zip_file_name'];
                    $get_ext  = '.zip';
                    $final_name = $get_name.$get_ext;

                    $get_selected_files = $_POST['selected_files'];

                    $file_array = array();

                    foreach ($get_selected_files as $key => $file_dir_name) {
                        if(is_dir($file_dir_name)){
                            $get_files = getDirItems($file_dir_name,true);
                            foreach($get_files as $key => $file){
                                $file_array[] = $file;
                            }
                        } else {
                            $file_array[] = $file_dir_name;
                        }
                    }

                    //if true, good; if false, zip creation failed
                    $result = generate_zip_file($file_array,$final_name);
                    if($result){
                        echo '<p style="text-align: center;">Successfully Created Zip file ' . $final_name . '. <br><br /> <strong style="color: red;">Please don\'t forget to delete this file when you\'re done.</strong></p>';
                    } else {
                        echo '<p style="text-align: center;">Failed to create zip file, Please try again</p>';
                    }
                } else {
                    echo '<p style="text-align: center;">Please provide a name for the zip file</p>';
                }
            }
        ?>
        <form action="" method="POST">
            <label for="zip-file-name">Zip File Name(without .zip extension)</label> <br>
            <input type="text" id="zip-file-name" name="zip_file_name" value="" placeholder="Name of the zip file" />
            <p><strong>Select Items</strong></p>
            <p><input type="checkbox" id="select-all-files" value="Select All"><label for="select-all-files" class="fs-label">Select All</label></p>
            <?php
            $list_all_files_folders = getDirItems(dirname(__FILE__));

            foreach ($list_all_files_folders as $key => $value) {
                echo '<input type="checkbox" name="selected_files[]" id="file-'.$key.'" value="'.$value.'" /> <label for="file-'.$key.'" class="fs-label">'.$value.'</label><br />';
            }
            ?>
            <input type="submit" value="Create Zip File" />
        </form>
        <br />
        
    </div>

    <div class="copyright">Copyright &copy; <?php echo date("Y"); ?> . By - <a href="http://www.navalkishor.xyz" target="_blank">Naval Kishor</a></div>
    <script type="text/javascript" src="//code.jquery.com/jquery-1.12.4.min.js"></script>
    <script type="text/javascript">
        $('#select-all-files').click(function(event) {
            if(this.checked) {
                // Iterate each checkbox
                $(':checkbox').each(function() {
                    this.checked = true;
                });
            } else {
                $(':checkbox').each(function() {
                    this.checked = false;
                });
            }
        });
    </script>
</body>
</html>
