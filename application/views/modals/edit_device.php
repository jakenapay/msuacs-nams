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

<?= form_open('admin/configurations/devices/edit/'. $device->id, ['id' => 'editDeviceForm', 'enctype' => 'multipart/form-data']) ?>
<div class="row mb-2">
    <div class="col-sm-6 mb-2">
        <label for="device_id">Device ID</label>
        <input class="form-control <?= form_error('device_id') ? 'form-error' : '' ?>" name="device_id" id="device_id" type="number" value="<?= isset($device->device_id) ? $device->device_id : set_value('device_id') ?>">
    </div>
</div>

<div class="row mb-2">
    <div class="col-sm-6 mb-2">
        <label for="name">Device Name</label>
        <input class="form-control <?= form_error('name') ? 'form-error' : '' ?>" name="name" id="name" type="text" value="<?= isset($device->name) ? $device->name : set_value('name') ?>">
    </div>
    <div class="col-sm-6 mb-2">
        <label for="type">Device Type</label>
        <select class="form-control" id="type" name="type" required>
            <option value="">Select device type</option>
            <option value="Entry" <?= set_select('type', 'Entry', isset($device->type) && $device->type == 'Entry'); ?>>Entry</option>
            <option value="Exit" <?= set_select('type', 'Exit', isset($device->type) && $device->type == 'Exit'); ?>>Exit</option>
        </select> 
    </div>
</div>

<div class="row mb-2">
    <div class="col-sm-6 mb-2">
        <label for="ip">IP Address</label>
        <input class="form-control <?= form_error('ip') ? 'form-error' : '' ?>" name="ip" id="ip" type="text" value="<?= isset($device->ip) ? $device->ip : set_value('ip') ?>" required>
    </div>
    <div class="col-sm-6 mb-2">
        <label for="location_id">Location</label>
        <select class="form-control" id="location_id" name="location_id" required>
            <option value="">Select a location</option>
            <?php foreach ($locations as $location): ?>
            <option value="<?php echo $location->id; ?>" <?= set_select('location_id', $device->location_id, isset($device->location_id) && $device->location_id == $location->id); ?>><?php echo $location->name; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<div class="d-flex mt-5">
    <button id="save-btn" class="btn btn-primary ml-auto">Save changes</button>
</div>
<?= form_close() ?>

