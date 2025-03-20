<style>
    body {}

    #addIdLog {
        padding: 20px;
    }

    form label {
        font-size: .9rem;
    }

    input,
    .input-group-text,
    select {
        font-size: .9rem !important
    }

    img#user-image {
        border-radius: 100%;
        height: 200px;
        width: 200px;
    }

    .user-id img {
        width: 15rem;
        height: 12rem;

    }
</style>

<?= form_open('admin/security/add_id_log', ['id' => 'addIdLog', 'enctype' => 'multipart/form-data']) ?>

<div class="row mb-2">
    <div class="col-sm-6 mb-2">
        <label for="id_number">ID Number <span class="text-danger">*</span></label>
        <input type="text" name="id_number" class="form-control <?= form_error('id_number') ? 'form-error' : '' ?>"
            id="id_number" value="<?= set_value('id_number') ?>">
    </div>
    <div class="col-sm-6 mb-2">
        <label for="last_name">RFID Number<span class="text-danger">*</span></label>
        <input class="form-control <?= form_error('rfid') ? 'form-error' : '' ?>" name="rfid" id="rfid" type="text"
            value="<?= set_value('rfid') ?>">
    </div>
</div>

<div class="row mb-2">
    <div class="col-sm-6 mb-2">
        <label for="status">Status <span class="text-danger">*</span></label>
        <select class="form-control <?= form_error('status') ? 'form-error' : '' ?>" name="status" id="status">
            <option value="">Select Status</option>
            <option value="loss">Loss</option>
            <option value="issued">Issued</option>
        </select>
    </div>

    <div class="col-sm-6 mb-2">
        <label for="remarks">Remarks <span class="text-danger">*</span></label>
        <select class="form-control <?= form_error('remarks') ? 'form-error' : '' ?>" name="remarks" id="remarks">
            <option value="">Select Remarks</option>
            <option value="Requested new ID">Requested new ID</option>
            <option value="ID re-issued">ID re-issued</option>
            <option value="Reported by staff">Reported by staff</option>
            <option value="Temporary ID given">Temporary ID given</option>
            <option value="Filed police report">Filed police report</option>
            <option value="Pending verification">Pending verification</option>
            <option value="Awaiting replacement">Awaiting replacement</option>
            <option value="No further action required">No further action required</option>
            <option value="Found and returned">Found and returned</option>
            <option value="Duplicate report">Duplicate report</option>
            <option value="other">Other</option>
        </select>
    </div>

</div>

<div class="row mb-2">

    <div class="col-sm-6 mb-2">
        <label for="reason">Reason <span class="text-danger">*</span></label>
        <textarea class="form-control" name="reason" id="reason" placeholder="Enter reason" required></textarea>
    </div>
</div>



<div class="d-flex mt-5">
    <button id="save-btn" class="btn btn-primary ml-auto">Save changes</button>
</div>
<?= form_close() ?>

