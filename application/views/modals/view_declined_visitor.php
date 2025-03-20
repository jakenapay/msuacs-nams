<style>
    body{
    }
    
    #editVisitorForm{
        padding: 20px;
    }

    form label{
        font-size: .9rem;
    }
    
    input, .input-group-text, select{
        font-size: .9rem !important
    }

    img#user-image{
        border-radius: 100%;
        height: 200px;
        width: 200px;
    }

    .user-id img{
        width: 15rem;
        height: 12rem;
        
    }

</style>

<?= form_open('admin/visit_management/visitors_active/update/', ['id' => 'editVisitorForm']) ?>
<div class="row mb-2">
    <div class="col-sm-12 mb-2 text-center">
        <p>User Image</p>
        <img id="user-image"src="<?= base_url($visitor['image']); ?>" alt="Visitor Image" class="rounded-full">
    </div>
</div>

<div class="row mb-2">
    <div class="col-sm-6 mb-2">
        <label for="first_name">First Name</label>
        <input class="form-control" name="first_name" id="first_name" type="text" value="<?= $visitor['first_name'] ?>" readonly>
    </div>
    <div class="col-sm-6 mb-2">
        <label for="last_name">Last Name</label>
        <input class="form-control" name="last_name" id="last_name" type="text" value="<?= $visitor['last_name'] ?>" readonly>
    </div>
</div>

<div class="row mb-2">
    <div class="col-sm-6 mb-2">
        <label for="phone_number">Phone Number</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">+63</span>
            </div>
            <input type="text" name="phone_number" class="form-control" id="phone_number" value="<?= $visitor['phone_number'] ?>" readonly>
        </div>
    </div>
    <div class="col-md-6 mb-2">
        <label for="visit_purpose">Purpose of Visit</label>
        <input type="text" name="visit_purpose" class="form-control" value="<?= $visitor['visit_purpose'] ?>" readonly>
    </div>
</div>



<div class="row mb-2">
    <div class="col-md-6 mb-2">
        <label for="visit_date">Date of Visit</label>
        <input type="date"
            name="visit_date" 
            class="form-control <?= (form_error('visit_date')) ? 'is-invalid' : '' ?>"
            value="<?= isset($visitor['visit_date']) ? $visitor['visit_date'] : set_value('visit_date') ?>"
            min="<?= date('Y-m-d') ?>" readonly> <!-- Sets the minimum date to today -->
    </div>
    <div class="col-md-6 mb-2">
        <label for="visit_time">Time of Visit</label>
        <select name="visit_time" id="visit_time" class="form-control <?= (form_error('visit_time')) ? 'is-invalid' : '' ?>" disabled>
            <?php 
                $selected_time = isset($visitor['visit_time']) ? $visitor['visit_time'] : set_value('visit_time');
                $times = [];

                for ($i = 8 ; $i < 18; $i++) {
                    $hour24 = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00:00';
                    $hour12 = $i == 0 ? 12 : ($i > 12 ? $i - 12 : $i);
                    $period = $i < 12 ? 'AM' : 'PM';
                    $formatted_hour12 = str_pad($hour12, 2, '0', STR_PAD_LEFT) . ':00' . $period;
                    $times[$hour24] = $formatted_hour12;
                }

                foreach ($times as $hour24 => $hour12) {
                    $selected = $selected_time === $hour24 ? 'selected' : '';
                    echo "<option value=\"$hour24\" $selected>$hour12</option>";
                }
            ?>
        </select>
        <?php if (form_error('visit_time')): ?>
            <span class="text-danger"><?= form_error('visit_time') ?></span>
        <?php endif; ?>
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-6 mb-2">
        <label for="emergency_contact_person">Emergency Contact Person</label>
        <input readonly class="form-control" name="emergency_contact_person" id="emergency_contact_person" type="text" value="<?= isset($visitor['emergency_contact_person']) ? $visitor['emergency_contact_person'] : '' ?>">
    </div>

    <div class="col-sm-6 mb-2">
        <label for="emergency_contact_number">Emergency Contact Number</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">+63</span>
            </div>
            <input type="text" name="emergency_contact_number" class="form-control" id="emergency_contact_number" value="<?= $visitor['emergency_contact_number'] ?>" readonly>
        </div>
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-6 mb-2">
        <label for="decline_reason">Reason of Decline</label>
        <input class="form-control" name="decline_reason" id="decline_reason" type="text" value="<?= isset($visitor['decline_reason']) ? $visitor['decline_reason'] : '' ?>" readonly>
    </div>
</div>

<div class="d-flex mt-5">
    <button id="save-btn" class="btn btn-primary ml-auto">Close</button>
</div>
<?= form_close() ?>
