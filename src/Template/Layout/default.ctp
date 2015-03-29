<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <?= $this->Html->css('styles') ?>
	<?= $this->Html->css('nprogress') ?>
	<?= $this->Html->css('jquery-ui.min') ?>
	<?= $this->Html->css('jquery-ui.structure.min') ?>
	<?= $this->Html->css('jquery-ui.theme.min') ?>
	<?= $this->Html->css('dataTables.bootstrap') ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
</head>
<body>

<div class="container-fluid">
	<div class="row">&nbsp;</div>

	<div class="row">
		<div class="col-md-6 col-md-offset-3">
			<nav class="navbar navbar-default">
				<div class="container-fluid">
					<!-- Brand and toggle get grouped for better mobile display -->
					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<span class="navbar-brand">
							<?= $this->Html->link("Valuutakalkulaator", "/") ?>
						</span>
					</div>

					<!-- Collect the nav links, forms, and other content for toggling -->
					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
						<!--<ul class="nav navbar-nav nav-tabs">-->
						<!--	<li role="presentation" class="active"><a href="#">Home</a></li>-->
						<!--	<li role="presentation"><a href="#">Link</a></li>-->
						<!--</ul>-->

						<p class="navbar-text navbar-right">
							<a href="https://github.com/Lammmas/finest-converter">Vaata GitHub-is</a>
						</p>
					</div><!-- /.navbar-collapse -->
				</div><!-- /.container-fluid -->
			</nav>

			<?= $this->fetch('content') ?>

			<p class="text-center">
				This site uses cookies, by continuing to use this site you accept the <?=
				$this->Html->link(
					"cookie policy",
					["controller" => 'pages', "action" => "cookies"]
				)
				?>
			</p>
		</div>
	</div>
</div>

<?= $this->fetch('script') ?>
<?= $this->Html->script('jquery-2.1.3.min') ?>
<?= $this->Html->script('jquery-ui.min') ?>
<?= $this->Html->script('bootstrap.min') ?>
<?= $this->Html->script('jquery.cookie') ?>
<?= $this->Html->script('jquery.dataTables.min') ?>
<?= $this->Html->script('dataTables.bootstrap.min') ?>
<?= $this->Html->script('nprogress') ?>
<?= $this->Html->script('scripts') ?>
</body>
</html>
