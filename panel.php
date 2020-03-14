<?php
// config /etc/sudoers.d/010_wwwdata-nopasswd
//
// www-data ALL = NOPASSWD: /sbin/shutdown
// www-data ALL = NOPASSWD: /bin/mount
// www-data ALL = NOPASSWD: /bin/date
//

$responseArray = Array();
$response = '(none)';
$returnVal = Array();

if( !empty( $_POST )){
    switch( $_POST['action']){
    case 'poweroff':
        $response = exec('sudo /sbin/shutdown', $returnVal );
        break;
    case 'reboot':
        $response = exec('sudo /sbin/shutdown -r', $returnVal);
        break;
    case 'mount':
        $response = exec('sudo mount', $returnVal);
        break;
    case 'syncTime':
        $response = exec("sudo date -s '" . $_POST['timestamp'] . "'", $returnVal );
    default:
        $response = '(!action)';
        break;
    }
    $responseArray['response'] = $response;
    $responseArray['returnVal'] = $returnVal;
    
    header('Content-Type: application/json');
    echo( json_encode( $responseArray) );
    die();
}

?>
<!DOCTYPE html>

<html>
    <head>
        <title>Local Panel</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            /* Color Theme Swatches in Hex */
            .defenders-comic-panel-1-hex { color: #B92B3B; }
            .defenders-comic-panel-2-hex { color: #8C8C66; }
            .defenders-comic-panel-3-hex { color: #648781; }
            .defenders-comic-panel-4-hex { color: #EF8B31; }
            .defenders-comic-panel-5-hex { color: #C1BA82; }

            /* Color Theme Swatches in RGBA */
            .defenders-comic-panel-1-rgba { color: rgba(184, 42, 59, 1); }
            .defenders-comic-panel-2-rgba { color: rgba(140, 140, 102, 1); }
            .defenders-comic-panel-3-rgba { color: rgba(100, 135, 128, 1); }
            .defenders-comic-panel-4-rgba { color: rgba(239, 138, 49, 1); }
            .defenders-comic-panel-5-rgba { color: rgba(193, 186, 130, 1); }

            /* Color Theme Swatches in HSLA */
            .defenders-comic-panel-1-hsla { color: hsla(353, 62, 44, 1); }
            .defenders-comic-panel-2-hsla { color: hsla(60, 15, 47, 1); }
            .defenders-comic-panel-3-hsla { color: hsla(169, 14, 46, 1); }
            .defenders-comic-panel-4-hsla { color: hsla(28, 85, 56, 1); }
            .defenders-comic-panel-5-hsla { color: hsla(53, 33, 63, 1); }

            #returnValBox{
                font-size: 20px;
            }
            .panel{
                width: 100vw;  
            }
            .panel .center{
                margin: 20px auto;
            }
            .panel .row{
                width: 100vw;
            }
            .panel .row .btns{
                width: 80vw;
                margin: 30px auto;
            }
            .panel .row .btns:after{
                content: "";
                display: table;
                clear: both;
            }
            .panel .row .btns button{
                width: 32%;
                height: 100px;
                font-size: 4vw;
                border-radius: 4px;
                border: 2px solid black;
                float: left;
                margin: 0 1vw 0 0;
            }
            .panel .row .btns button[value=poweroff]{
                height: 100px;
                background-color: #B92B3B;
            }
            .panel .row .btns button[value=reboot]{
                height: 100px;
                background-color: #EF8B31;
            }
            .panel .row .btns button[value=mount]{
                height: 100px;
                background-color: #C1BA82;
            }
        </style>
    </head>
    <body>
        <div class='panel'>
            <div class="row">
                <div class="btns" id="controlBtns">
                    <button type="submit" value="poweroff">Power Off</button>
                    <button type="submit" value="reboot">Reboot</button>
                    <button type="submit" value="mount" >Mount</button>
                    <button type="submit" value="syncTime" >SyncTime</button>
                </div>
            </div>
            <div class="row">
                <div class="center">
                    <span>Response: <?php echo $response ;?> </span>
                </div>
            </div>
            <div class="row">
                <textarea rows="40" cols="80" spellcheck="false" class="center" id="returnValBox">
                </textarea>
            </div>
        </div>
        <script>
            var returnValBox = document.getElementById('returnValBox');
            function onClickHandler(e){
                e.preventDefault();

                var xhr = new XMLHttpRequest();
                var formData = new FormData();
                xhr.onreadystatechange = function () {
                    if(xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200){
                        var responseJson = JSON.parse(xhr.response);
                        var returnString = '';

                        responseJson.returnVal.forEach( function(line){
                            returnString += line + '\n';
                        })

                        returnValBox.value = returnString;
                    }
                };

                formData.append("action", this.value );
                if( this.value == 'syncTime' ){
                    formData.append('timestamp', Date());
                }
                xhr.open("POST", '/panel.php', true);
                xhr.send(formData);

                console.log( 'action: ' + this.value )
            }

            var buttons = document.getElementById("controlBtns").querySelectorAll("button");
            buttons.forEach( function( val, index, obj ){
                val.addEventListener( 'click', onClickHandler );
            })
        </script>
    </body>
</html> 
