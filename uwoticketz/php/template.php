<html>
<head>
	<meta charset="UTF-8">
	<title>UWO Ticketz</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
	<link rel="stylesheet" href="styles/stylesheet.css">
	<script
	  src="https://code.jquery.com/jquery-3.2.1.min.js"
	  integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
	  crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
	<script src="scripts/submit.js"></script>
	<script src="scripts/index.js"></script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light" id="header">
  <?php iconImg(); ?>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      <?php
		navMenu()
	  ?>
    </ul>
  </div>
</nav>

<div id="mainContent">
	<?php pageContent(); ?>

	<!-- Submit Ticket Modal is available on all pages -->
	<div class="wrapper">
		<div class="modal fade" id="submitTicketModal" role="dialog">
			<div class="modal-dialog">
			  <!-- Modal content-->
			  <div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Submit Ticket</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<form id="submitTicketForm" method="POST">
						<div class="form-group">
							<label>Computer Number</label>
							<input type="text" class="form-control numeric" id="computerId" name="computerId" placeholder="Computer Number" maxlength="5" required autofocus novalidate/>
						</div>
						<div class="form-group">
							<label>Description</label>
							<textarea class="form-control" id="description" rows="12" name="description" placeholder="Issue Description" required novalidate></textarea>    
						</div>
						<button class="btn btn-success floatRight" type="submit" id="submitTicketButton">Submit</button>
						<button type="button" class="btn btn-default floatRight marginRight10px" data-dismiss="modal">Cancel</button>
					</form>
				</div>
			  </div>
			</div>
		  </div>
	</div>
</div>

</body>
</html>