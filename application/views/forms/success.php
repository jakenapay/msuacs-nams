<div id="container" class="p-5">
	<div>
		<img class="img-fluid" src="<?= base_url('assets/images/msu.png');?>" alt="">
	</div>
	<div class="header mb-4">
		<h1>Success!</h1>
	</div>
	<hr class="mt-3 mb-5">
	<div class="page-body mt-5">
		<p>
			Thank you for submitting your visitor information form. We have received your details.
		</p>
		<div class="header mb-4">
			<p>Your Transaction Number: <span class="bg-success rounded text-white p-2"><?= $transaction_number ?></span></p>
		</div>
		<p>
			You can now proceed to visit. We look forward to welcoming you!
		</p>
	</div>

    <div class="page-footer">
        <button type="submit" 
        onclick="redirectToHome(); displayLoader();"
        class="btn btn-danger mt-5 mb-2" id="start-btn">
        <span id="loader" class="spinner-border spinner-border-sm" style="display: none;"></span> Back to home</button>
    
    </div>
</div>
