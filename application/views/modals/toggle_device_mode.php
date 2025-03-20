<style>
    body{
    }
    
    form{
        padding: 20px;
    }

    form label{
        font-size: .9rem;
    }
    
    input, .input-group-text, select{
        font-size: .9rem !important
    }
</style>

<?= form_open('admin/configurations/devices/toggle_mode/set', ['id' => 'toggleForm', 'enctype' => 'multipart/form-data']) ?>

<div class="row mb-2">
    <div class="col-sm-12 mb-2">
        <p><strong>Testing Mode:</strong> In this mode, the turnstile allows multiple taps without requiring an exit between entries. This is useful for system testing, demonstrations, and troubleshooting. Users can repeatedly tap their RFID cards to simulate high traffic or test various scenarios without restrictions.</p>
        
        <p><strong>Production Mode:</strong> This is the standard operational mode for day-to-day use. In Production Mode, the system enforces strict entry/exit rules. Users must exit before they can enter again, preventing multiple consecutive entries. This ensures accurate tracking of building occupancy and enhances security by preventing unauthorized access sharing.</p>
    </div>
</div>

<div class="row mb-2">
    <div class="col-sm-12 mb-2">
        <p><strong>Current Mode:</strong> <?= $mode == 2 ? '<span class="bg-success text-white rounded p-2">Testing</span>' : '<span class="bg-success text-white rounded p-2">Production<span>' ?><p>
    </div>            
</div>

<div class="row mb-2">
    <div class="col-sm-12 mb-2">
        <label for="mode">Device Mode:</label>
        <select class="form-control" name="mode" id="mode">
            <option value="1" <?= set_select('mode', 1, isset($mode) && $mode == 1); ?>>Production</option>
            <option value="2" <?= set_select('mode', 2, isset($mode) && $mode == 2); ?>>Testing</option>
        </select>
    </div>
</div>


<div class="d-flex mt-5">
    <button id="save-btn" class="btn btn-primary ml-auto">Save changes</button>
</div>
<?= form_close() ?>

