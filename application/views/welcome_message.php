<div id="container" class="container p-5">
    <div class="">
        <img class="img-fluid mb-3" src="<?= base_url('assets/images/msu.png');?>" alt="">
    </div>
    <div class="header mb-3">
        <h1>Welcome</h1>
    </div>
    <div class="sub-header">
        <h1>to our Visitor Information Form</h1>
    </div>
    <hr class="mt-3 mb-5">
    <div class="page-body mt-5 mb-5">
    <p>
        Welcome to MSU! We're excited to have you visit us. Please fill out the form to help us prepare for your arrival.
    </p>
    <p>
        Your cooperation in providing accurate information will help ensure a smooth and enjoyable experience during your visit.
        We look forward to welcoming you to our campus!
    </p>
</div>

    <div class="page-footer">
        <button type="submit" 
        onclick="redirectToForm(); displayLoader();"
        class="btn btn-danger mb-2" id="start-btn">
        <span id="loader" class="spinner-border spinner-border-sm" style="display: none;"></span> Get Started</button>
        
        <button type="submit" 
        onclick="redirectToExisting(); displayLoader2();"
        class="btn view-btn mb-2" id="view-btn">
        <span id="loader2" class="spinner-border spinner-border-sm" style="display: none;"></span> View Existing</button>
    </div>
</div>
