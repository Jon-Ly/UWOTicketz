  <div class="wrapper">
    <form id="ticketForm" method="POST" class="form-signin">       
      <h2 class="form-signin-heading">Submit a Ticket</h2>
      <div class="divider"></div>
      <input type="text" class="form-control" id="computerId" name="computerId" placeholder="Computer Number" required autofocus />
      <div class="divider"></div>
      <textarea class="form-control" id="description" rows="10" name="description" placeholder="Issue Description" required></textarea>    
      <hr/>
      <button class="btn btn-lg btn-primary btn-block" type="submit" id="submitTicketButton">Submit</button>
    </form>
  </div>