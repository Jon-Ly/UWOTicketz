<div class="container table-responsive">
	<?php
	if(isAdmin()){
	?>
	<button type="button" class="btn btn-info btn-lg marginTop10px" data-toggle="modal" data-target="#addCompModal">Add Computer</button>
	<?php } ?>
	<table class="table table-striped" id="computers_table">
		  <thead>
				<tr>
					<th>Computer Number</th>
					<th>Location</th>
				</tr>
		  </thead>
		  <tbody>
				<?php computerTable(); ?>
		  </tbody>
    </table>
</div>

<div class="container">
	  <div class="modal fade" id="addCompModal" role="dialog">
			<div class="modal-dialog">
				  <div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title">Add Computer</h4>
							<button type="button" class="close" data-dismiss="modal">&times;</button>
						</div>
						<div class="modal-body">
							  <form id="addComputerForm" method="POST">
									<div class="form-group">
										<label>Computer Number</label>
										<input type="text" class="form-control numeric" id="computerNumberAdd" name="computerNumber" maxlength="5" autofocus required novalidate />
									</div>
									<div class="form-group">
										<label>Location</label>
										<select class="form-control" id="locationsAdd">
											<?php getLocationList(); ?>
										</select>
									</div>
									<button class="btn btn-success floatRight" type="submit" id="submitComputerButton">Submit</button>
									<button type="button" class="btn btn-default floatRight marginRight10px" data-dismiss="modal">Cancel</button>
							  </form>
						</div>
				  </div>
			</div>
	  </div>
</div>

<div class="modal fade" id="edit_comp_modal" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Edit Computer</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				  <form id="edit_computer_form" method="POST">
						<div class="form-group">
							<label>Computer Number</label>
							<input type="text" class="form-control numeric" id="computerNumberEdit" name="computerNumber" maxlength="5" autofocus required novalidate />
						</div>
						<div class="form-group">
							<label>Location</label>
							<select class="form-control" id="locationsEdit">
								<?php getLocationList(); ?>
							</select>
						</div>
						<button class="btn btn-success floatRight" type="submit" id="edit_computer_button">Submit</button>
						<button type="button" class="btn btn-default floatRight marginRight10px" data-dismiss="modal">Cancel</button>
				  </form>
			</div>
		</div>
	</div>
</div>

<script src="scripts/computers.js"></script>