<?php
/*
KVM-VDI
Tadas Ustinavičius
tadas at ring.lt

Vilnius University.
Center of Information Technology Development.


Vilnius,Lithuania.
2016-09-05
*/
include('functions/config.php');
require_once('functions/functions.php');
if (!check_session()){
    header ("Location: $serviceurl/?error=1");
    exit;
}
slash_vars();
$vm=$_GET['vm'];
$hypervisor=$_GET['hypervisor'];
if (empty($vm)||empty($hypervisor)){
    exit;
}
$h_reply=get_SQL_line("SELECT * FROM hypervisors WHERE id='$hypervisor'");
$v_reply=get_SQL_array("SELECT * FROM vms WHERE id='$vm'");
ssh_connect($h_reply[2].":".$h_reply[3]);
$address=ssh_command("sudo virsh domdisplay " . $v_reply[0]['name'], true);
$address=str_replace("localhost",$h_reply[2],$address);
$address=str_replace("\n","",$address);
$html5_token_value=$address;
$html5_token_value=str_replace('spice://',"",$html5_token_value);
$address=$address . "?password=" . $v_reply[0]['spice_password'];
$rnd=uniqid();
set_lang();
?>
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <title><?php echo _("VM screen");?></title>  
</head>
<body>
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
             <h4 class="modal-title"><?php echo _("VM name: ") . $v_reply[0]['name']; ?></h4>
        </div>
        <div class="modal-body">
	    <?php echo '<img src="screenshot.php?vm=' . $vm . '&hypervisor=' . $hypervisor . '&' . $rnd . '">'; ?>
        </div>
        <div class="modal-footer">
	    <?php echo '<button type="button" class="btn btn-success" onclick="javascript:window.location=\'' . $address . '\'" target="_new" data-dismiss="modal">' . _("SPICE console") . '</button>';?>
	    <?php echo '<button type="button" class="btn btn-success" onclick="dashboard_open_html5_console_click()" data-dismiss="modal">' . _("HTML5 console") . '</button>';?>
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _("Close");?></button>
        </div>
    </div>
</body>
</html>
<script>
function dashboard_open_html5_console_click(){
    send_token(<?php echo "'".$websockets_address . "', '" . $websockets_port . "', '" . $v_reply[0]['name'] . "', '" . $html5_token_value . "', '" . $v_reply[0]['spice_password'] . "'";?>);

}
</script>