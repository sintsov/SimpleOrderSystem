<?php
/**
 * Index file: main logic
 *
 * @author Sintsov Roman <roman_spb@mail.ru>
 */
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once realpath(__DIR__ . '/../app/config/init.php');

?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<meta charset="utf-8">
		<title></title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<!--[if lt IE 9]>
			<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<link href="css/styles.css" rel="stylesheet">
	</head>
	<body>
		<div class="wrapper">
		<div class="box">
			<div class="row">

				<!-- sidebar -->
				<div class="column col-sm-3" id="sidebar">
					<a class="logo" href="/">B</a>

                    <? if(!isAuth()){
                        require_once $documentRoot . '/public/layout/auth.php';
                    } else {
                        require_once $documentRoot . '/public/layout/profile.php';
                    }?>

					<ul class="nav hidden-xs" id="sidebar-footer">
						<li>
						  <a href=""><h3>Simple Order System</h3>Made with <i class="glyphicon glyphicon-heart-empty"></i> by Sintsov Roman</a>
						</li>
					</ul>
				</div>
				<!-- /sidebar -->

				<!-- main -->
				<div class="column col-sm-9" id="main">
					<div class="padding">
						<div class="full col-sm-9">

							<!-- content -->

							<div class="col-sm-12" id="orders">
							  <div class="page-header text-muted">
							  Orders
							  </div>
							</div>

							<!-- orders -->
							<div class="row">
							  <div class="col-sm-10">
								<h3>List of role-playing video game</h3>
                                <h4><span class="label label-default">vk.com</span></h4>
                                <p>This is a comprehensive index of commercial role-playing video games, sorted chronologically by year. Information regarding date of release, developer, publisher, operating system, sub-genre and notability is provided where available. The table can be sorted by clicking on the small boxes next to the column headings. This list does not include roguelikes, MUDs or MMORPGs. It does include action RPGs and tactical RPGs.</p>
                                <? if (isAuth() && !isCustomer()){?>
                                    <p><button type="button" class="btn btn-primary">Make It!</button></p>
                                <?}?>
								<small class="text-muted">1 hour ago</small>
								</h4>
							  </div>
							  <div class="col-sm-2">
								<a href="#" class="pull-right"><img src="http://api.randomuser.me/portraits/thumb/men/19.jpg" class="img-circle"></a>
							  </div>
							</div>

                            <div class="row divider">
                                <div class="col-sm-12"><hr></div>
                            </div>
                            <!-- /orders -->

						</div><!-- /col-9 -->
					</div><!-- /padding -->
				</div>
				<!-- /main -->

			</div>
		</div>
	</div>

		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
        <script src="js/utils.js"></script>
	</body>
</html>