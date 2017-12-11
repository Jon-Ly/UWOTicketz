<div class="container">
	<div class="form-group">
		<label>Start Date:</label>
		<input type='date' class="form-control" />
		<label>End Date:</label>
		<input type='date' class="form-control" />
		<hr>
		<button class="btn btn-primary floatRight">Generate</button>
	</div>
</div>
<table class="table table-striped marginTop15px" id="reports_table">
	<thead>
		<tr>
			<th>Ticket Number</th>
			<th>Computer</th>
			<th>Submitted</th>
			<th>Completed</th>
			<th>Location</th>
			<th>Feedback</th>
			<th>User</th>
			<th>Status</th>
			<th>Description</th>
		</tr>
	</thead>
	<tbody>
		<?php userTicketTable(); ?>
	</tbody>
</table>

<script src="scripts/report.js"></script>