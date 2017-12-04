<div class="container">
	<table class="table table-striped">
      <thead>
        <tr>
          <th>Ticket Number</th>
          <th>Computer</th>
          <th>Submitted</th>
          <th>Completed</th>
          <th>Status</th>
		  <th>Feedback</th>
        </tr>
      </thead>
      <tbody>
        <?php ticketTable(); ?>
      </tbody>
    </table>

	<div class="wrapper">
		<div class="modal fade" id="ticketDataModal" role="dialog">
			<div class="modal-dialog">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title">Ticket #<span id="ticket_id"></span></h4>
						<button type="button" class="close" data-dismiss="modal">&times;</button>
					</div>
					<div class="modal-body">
						<div class="comment_thread">
						
						</div>
						<hr>
						<form id="submit_comment_form" method="POST">
							<div class="form-group">
								<label class='bold'>Comment</label>
								<textarea class="form-control" id="user_comment" name="user_comment" placeholder="" maxlength="600" novalidate></textarea>
							</div>
							<button class="btn btn-success floatRight" type="submit" id="submit_comment_button">Submit</button>
							<button type="button" class="btn btn-default floatRight marginRight10px" data-dismiss="modal">Close</button>
						</form>
					</div>
				</div>
			</div>
		 </div>
	</div>
</div>

<script src="scripts/tickets.js"></script>