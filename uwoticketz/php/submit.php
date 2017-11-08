  <div class="wrapper">
    <form id="ticketForm" method="POST" class="form-signin">       
      <h2 class="form-signin-heading">Submit a Ticket</h2>
	  <p id="successMessage" class="noDisplay greenText">Your ticket has been submitted.</p>
	  <p id="errorMessage" class="noDisplay redText">The computer number does not exist.</p>
      <div class="divider"></div>
      <input type="text" class="form-control" id="computerId" name="computerId" placeholder="Computer Number" required autofocus novalidate/>
      <div class="divider"></div>
      <textarea class="form-control" id="description" rows="10" name="description" placeholder="Issue Description" required novalidate></textarea>    
      <hr/>
      <button class="btn btn-lg btn-primary btn-block" type="submit" id="submitTicketButton">Submit</button>
    </form>
  </div>

  <script src="scripts/submit.js"></script>