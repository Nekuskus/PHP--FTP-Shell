<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP->FTP</title>
    <style>
        input {
            padding: 5px;
        }

        div#controls {
            padding: 20px;
        }
        #params input, #params select {
            margin-left: 5px;
            margin-right: 5px;
        }
    </style>
    <script src="index.js" defer></script>
    </head>
<body>
    <h1>Konsola PHP->FTP</h1>
    <div id="panel">
        <h3>Panel</h3>
        <div id="controls">
            <form method="post" action="index.php">
                <label for="type">Wybierz polecenie</label>
                <select name="cmd" id="cmd">
                    <option value="mlsd" selected>ftp_mlsd (ls)</option>
                    <option value="append">ftp_append</option>
                    <option value="cdup">ftp_cdup</option>
                    <option value="chdir">ftp_chdir</option>
                    <option value="chmod">ftp_chmod</option>
                    <option value="delete">ftp_delete</option>
                    <option value="exec">ftp_exec</option>
                    <option value="get">ftp_get</option>
                    <option value="put">ftp_put</option>
                    <option value="fget">ftp_fget</option>
                    <option value="fput">ftp_fput</option>
                    <option value="get_option">ftp_get_option</option>
                    <option value="set_option">ftp_set_option</option>
                    <option value="mdtm">ftp_mdtm</option>
                    <option value="mkdir">ftp_mkdir</option>
                    <option value="rmdir">ftp_rmdir</option>
                    <option value="rename">ftp_rename</option>
                    <option value="nbput">ftp_nb_put (non-blocking send)</option>
                    <option value="nbget">ftp_nb_get (non-blocking recv)</option>
                    <option value="nlist">ftp_nlist (ls + param)</option>
                    <option value="pwd">ftp_pwd</option>
                    <option value="raw">ftp_raw</option>
                    <option value="rawlist">ftp_rawlist</option>
                    <option value="site">ftp_site</option>
                    <option value="systype">ftp_systype</option>
                </select>
                <button type="submit">Wykonaj</button>
                <input type="checkbox" name="ssl" id="ssl">
                <label for="pasv">(Use ssl?)</label>
                <input type="checkbox" name="pasv" id="pasv">
                <label for="pasv">(Use pasv?)</label>
                <br>
                <fieldset>
                    <legend>Parametry</legend>
                    <div id="params">
                        <input type="text" name="arg1">
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
    <?php

        #region INIT
        function br() {
            print("<br/>");
        }

        // W pliku php.ini: extension=ftp, extension_dir = ".\ext"
        // Nie można przez ini_set()
        $url = 'ftpupload.net'; // 185.27.134.154 jeżeli dns failure

        $username = "***REMOVED***";
        $password = "***REMOVED***";
        
        $ftp = null;
        
        if(isset($_POST['ssl'])) {
            $ftp = ftp_ssl_connect($url); // Implicit, nie działa z infinityfree ftp
        } else {
            $ftp = ftp_connect($url);
        }

        if(isset($_POST['pasv'])) {
            ftp_pasv($ftp, true);
        }

        # https://bugs.php.net/bug.php?id=9969&edit=2

        $login_result = ftp_login($ftp, $username, $password);
        
        if(!$login_result) {
            die('Error when logging into ftp: ' . $login_result);
        }

        ftp_chdir($ftp, "htdocs");

        print("Output polecenia:");
        br();

        #endregion INIT

        #region PROCESS

        print("<pre>");
        if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST['cmd']) || empty($_POST['cmd'])) {
            $listing = ftp_mlsd($ftp, '/');
            if($listing) {
                foreach ($listing as $idx => $value) {
                    // print($idx . " ");
                    // print("<code>" . $value['type'] . "</code>" . " " . $value['name']);
                    print($value['type'] . " " . $value['name']);
                    br();
                }
            } else {
                print("Error");
            }
        } else {
            switch ($_POST['cmd']) {
                case 'mlsd':
                    $listing = ftp_mlsd($ftp, $_POST['arg1']);
                    if(!$listing) {
                        print("Error");
                        break;
                    }
                    foreach ($listing as $value) {
                        print($value['type'] . " " . $value['name']);
                        br();
                    }
                    break;
                case 'append':
                    if(ftp_append($ftp, $_POST['arg1'], $_POST['arg2'])) {
                        print("Successful");
                    } else {
                        print("Error");
                    }
                    break;
                case 'cdup':
                    if(ftp_cdup($ftp)) {
                        print("Successful");
                    } else {
                        print("Error");
                    }
                    break;
                case 'chdir':
                    if(ftp_chdir($ftp, $_POST['arg1'])) {
                        print("Successful");
                    } else {
                        print("Error");
                    }
                    break;
                case 'chmod':
                    $ret = ftp_chmod($ftp, $_POST['arg1'], $_POST['arg2']);
                    if($ret) {
                        print("Uprawnienia pliku " . $_POST['arg2'] . " są teraz ustawione na: " . $ret);
                    } else {
                        print("Error");
                    }
                    break;
                case 'delete':
                    if(ftp_delete($ftp, $_POST['arg1'])) {
                        print("Successful");
                    } else {
                        print("Error");
                    }
                    break;
                case 'exec':
                    if(ftp_exec($ftp, $_POST['arg1'])) {
                        print("Successful");
                    } else {
                        print("Error");
                    }
                    break;
                case 'get':
                    if(ftp_get($ftp, $_POST['arg1'], $_POST['arg2'])) {
                        print("Successful");
                    } else {
                        print("Error");
                    }
                    break;
                case 'put':
                    if(ftp_put($ftp, $_POST['arg1'], $_POST['arg2'])) {
                        print("Successful");
                    } else {
                        print("Error");
                    }
                    break;
                case 'fget':
                    $resource = fopen($_POST['arg1'], 'w');
                    if(ftp_fget($ftp, $resource, $_POST['arg2'])) {
                        print("Successful");
                    } else {
                        print("Error");
                    }
                    break;
                case 'fput':
                    $resource = fopen($_POST['arg2'], 'r');
                    if(ftp_fput($ftp, $_POST['arg1'], $resource)) {
                        print("Successful");
                    } else {
                        print("Error");
                    }
                    break;
                case 'get_option':
                    $opt = -1;
                    switch ($_POST['arg1']) {
                        case "FTP_TIMEOUT_SEC":
                            $opt = FTP_TIMEOUT_SEC;
                            break;
                        case "FTP_AUTOSEEK":
                            $opt = FTP_AUTOSEEK;
                        case "FTP_USEPASVADDRESS": // Nie ma w dokumentacji ftp_get_option, ale jest w dokumentacji ftp_set_option
                            $opt = FTP_USEPASVADDRESS;
                            break;
                    }
                    $ret = ftp_get_option($ftp, $opt);
                    if($ret) {
                        print("Opcja " . $opt . " jest ustawiona na: " . $ret);
                    } else {
                        print("Error");
                    }
                    break;
                case 'set_option':
                    $opt = -1;
                    $arg2 = -1;
                    switch ($_POST['arg1']) {
                        case "FTP_TIMEOUT_SEC":
                            $opt = FTP_TIMEOUT_SEC;
                            $arg2 = (int)$_POST['arg2'];
                            break;
                        case "FTP_AUTOSEEK":
                            $opt = FTP_AUTOSEEK;
                            $arg2 = (int)$_POST['arg2'];
                            break;
                        case "FTP_USEPASVADDRESS":
                            $opt = FTP_USEPASVADDRESS;
                            $arg2 = (bool)$_POST['arg2'];
                            break;
                    }
                    $ret = ftp_set_option($ftp, $opt, $arg2);
                    if($ret) {
                        print("Opcja " . $opt . " jest teraz ustawiona na: " . $ret);
                    } else {
                        print("Error");
                    }
                    break;
                case 'mdtm':
                    $ret = ftp_mdtm($ftp, $_POST['arg1']);
                    if($ret != -1) {
                        print("Czas ostatniej modyfikacji pliku " . $_POST['arg1'] . " to " . $ret);
                    } else {
                        print("Error");
                    }
                    break;
                case 'mkdir':
                    $ret = ftp_mkdir($ftp, $_POST['arg1']);
                    if($ret != false) {
                        print("Utworzono folder " . $ret);
                    } else {
                        print("Error");
                    }
                    break;
                case 'rmdir':
                    if(ftp_rmdir($ftp, $_POST['arg1'])) {
                        print("Usunięto folder");
                    } else {
                        print("Error");
                    }
                    break;
                case 'rename':
                    if(ftp_rename($ftp, $_POST['arg1'], $_POST['arg2'])) {
                        print("Successful");
                    } else {
                        print("Error");
                    }
                    break;
                case 'nbput':
                    $ret = ftp_nb_put($ftp, $_POST['arg1'], $_POST['arg2']); // FTP_FAILED or FTP_FINISHED or FTP_MOREDATA, or false
                    if(!$ret) {
                        print("Error");
                        break;
                    }
                    
                    while($ret == FTP_MOREDATA) {
                        print("Sending more...");
                        br();
                        $ret = ftp_nb_continue($ftp);
                    }

                    if($ret != FTP_FINISHED) {
                        print("Error");
                    } else {
                        print("File sent successfully");
                    }
                    break;
                case 'nbget':
                    $ret = ftp_nb_get($ftp, $_POST['arg1'], $_POST['arg2']); // FTP_FAILED or FTP_FINISHED or FTP_MOREDATA, or false
                    if(!$ret) {
                        print("Error");
                        break;
                    }
                    
                    while($ret == FTP_MOREDATA) {
                        print("Sending more...");
                        br();
                        $ret = ftp_nb_continue($ftp);
                    }

                    if($ret != FTP_FINISHED) {
                        print("Error");
                    } else {
                        print("File sent successfully");
                    }
                    break;
                case 'nlist':
                    $listing = ftp_nlist($ftp, $_POST['arg1']);
                    if(!$listing) {
                        print("Error");
                        break;
                    }
                    foreach ($listing as $value) {
                        print($value);
                        br();
                    }
                    break;
                case 'pwd':
                    $dir = ftp_pwd($ftp);
                    if($dir) {
                        print("Aktualny folder to " . $dir);
                    } else {
                        print("Error");
                    }
                    break;
                case 'raw':
                    $res = ftp_raw($ftp, $_POST['arg1']);
                    if(!$res) {
                        print("Error");
                    } else {
                        foreach ($res as $value) {
                            print($value);
                            br();
                        }
                    }
                    break;
                case 'rawlist':
                    $res = ftp_rawlist($ftp, $_POST['arg1'], isset($_POST['arg2']));

                    if(!$res) {
                        print("Error");
                    } else {
                        foreach ($res as $value) {
                            print($value);
                            br();
                        }
                    }
                    break;
                case 'site':
                    if(ftp_site($ftp, $_POST['arg1'])) {
                        print("Successful");
                    } else {
                        print("Error");
                    }
                    break;
                case 'systype':
                    $systype = ftp_systype($ftp);
                    if($systype) {
                        print("System to: " . $systype);
                    } else {
                        print("Error");
                    }
                    break;
                
                
                default:
                    print("PHP ERROR: Unknown cmd received.");
                    break;
            }    
        }
        
        print("</pre>");

        #endregion PROCESS

        br();
        br();
        print("Post: <br/><pre>");
        print_r($_POST);
        print("</pre>");

        ftp_close($ftp); // lub ftp_quit($ftp);
    ?>
</body>
</html>