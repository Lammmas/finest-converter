<? $this->assign('title', 'Magic Converter'); ?>

<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="alert alert-danger alert-dismissible hidden" role="alert" id="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<strong>Oh snap!</strong> <span id="alertcontent"></span>
				</div>

				<form>
					<div class="row">
						<div class="col-sm-4">
							<div class="input-group">
								<input class="form-control" value="" id="fromamount" placeholder="Kogus"
								       name="fromamount" type="number" step="0.01" min="0.01">
								<div class="input-group-btn">
									<button type="button" class="btn btn-default dropdown-toggle"
									        data-toggle="dropdown" data-currency="">
										<span class="currency-name">Vali</span> <span class="caret"></span>
									</button>
									<ul class="dropdown-menu scroll-dropdown" id="fromselect"></ul>
									<input name="fromcurrency" id="fromcurrency" class="dropdown-target" type="hidden">
								</div>
							</div>
						</div>

						<div class="col-sm-4">
							<button type="button" class="btn btn-default btn-block dropdown-toggle" data-currency=""
							        data-toggle="dropdown" style="text-align: left">
								<span class="currency-name">Vali</span> <span class="caret"></span>
							</button>
							<ul class="dropdown-menu scroll-dropdown" id="targetselect"></ul>
							<input name="targetcurrency" id="targetcurrency" class="dropdown-target" type="hidden">
						</div>

						<div class="form-group col-sm-4">
							<input type="datetime" class="form-control readonly" id="time" name="time"
							       placeholder="Aeg" value="2010-12-30" readonly>
						</div>
					</div>
					<div class="row">
						<button class="btn btn-success col-xs-6 col-md-4 col-xs-offset-3 col-md-offset-4 disabled"
						        id="confirmbtn" type="button" disabled>
							Saada
						</button>
					</div>
				</form>

				<br>

				<!-- History -->
				<table class="table table-striped table-hover table-condensed" id="historytable">
					<thead>
					<tr>
						<th>Kogus</th>
						<th>Konversioon</th>
						<th>Aeg</th>
						<th>Eesti Bank</th>
						<th>Leedu Bank</th>
					</tr>
					</thead>
					<tbody>
					<? if (isset($history)) {
						foreach ($history as $id => $entry) { ?>
							<tr>
								<td><?= $entry['amount'] ?></td>
								<td><?= $entry['from'] . " -> " . $entry['to'] ?></td>
								<td><?= $entry['time'] ?></td>
								<td><?= $entry['est'] ?></td>
								<td><?= $entry['est'] ?></td>
							</tr>
						<? }
					} ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
