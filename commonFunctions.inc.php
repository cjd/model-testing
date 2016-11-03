<?php


//update plugin

function updatePluginFromGitHub($gitURL, $branch="master", $pluginName) {


        global $settings;
        logEntry ("updating plugin: ".$pluginName);

        logEntry("settings: ".$settings['pluginDirectory']);

        //create update script
        //$gitUpdateCMD = "sudo cd ".$settings['pluginDirectory']."/".$pluginName."/; sudo /usr/bin/git git pull ".$gitURL." ".$branch;

        $pluginUpdateCMD = "/opt/fpp/scripts/update_plugin ".$pluginName;

        logEntry("update command: ".$pluginUpdateCMD);


        exec($pluginUpdateCMD, $updateResult);

        //logEntry("update result: ".print_r($updateResult));

        //loop through result
        return;// ($updateResult);



}
?>
