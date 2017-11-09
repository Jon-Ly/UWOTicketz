<div class="container">
  <button type="button" class="btn btn-info btn-lg marginTop10px" data-toggle="modal" data-target="#addUserModal">Add User</button>
	<table class="table table-striped">
      <thead>
        <tr>
          <th>First Name</th>
          <th>Last Name</th>
          <th>Username</th>
		  <th>Access</th>
        </tr>
      </thead>
      <tbody>
		<?php userTable(); ?>
      </tbody>
    </table>
</div>

<div class="container">
  <!-- Modal -->
  <div class="modal fade" id="addUserModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
		<div class="modal-header">
			<h4 class="modal-title">Add User</h4>
			<button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
			  <form id="addUserForm" method="POST">
				<div class="form-group">
					<label>First Name</label>
					<input class="form-control" type="text" autofocus required novalidate />
				</div>
				<div class="form-group">
					<label>Last Name</label>
					<input class="form-control" type="text" required novalidate />
				</div>
				<div class="form-group">
					<label>Username</label>
					<input class="form-control" type="text" required novalidate />
				</div>
				<button class="btn btn-success floatRight" type="submit" id="submitUserButton">Submit</button>
				<button type="button" class="btn btn-default floatRight marginRight10px" data-dismiss="modal">Cancel</button>
			  </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="scripts/users.js"></script>