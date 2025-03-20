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

<?= form_open('', ['id' => 'editAdminForm', 'enctype' => 'multipart/form-data']) ?>

<div class="row mb-2">
    <div class="col-sm-12 mb-2 text-center">
        <p>User Image</p>
    </div>
    <div class="col-sm-12 mb-2">
        <div class="video-wrapper d-flex justify-content-center">
            <video id="edit-video" class="<?= form_error('image') ? 'form-error' : '' ?>" autoplay style="display: none;"></video>
        </div>
        <div id="edit-upload_image_preview" style="display: none; text-align: center; margin-y: 10px">
            <img id="edit-upload_preview" class="rounded" width="250" height="250">
        </div>
        <div class="d-flex justify-content-center">
            <canvas id="edit-canvas" style="display: none;"></canvas>
            <img id="currentImage" class="rounded" src="<?= isset($admin->image) ? base_url($admin->image) : '' ?>" alt="admin Image" style="max-width: 250px; max-height: 250px;">
            <input type="file" name="image" id="edit-imageInput" style="display: none;" accept="image/*">
        </div>
        <div class="capture-buttons d-flex justify-content-center mt-2">
            <button type="button" id="edit-capture-button" class="btn btn-sm btn-primary" style="display: none;">Take Snapshot</button>
            <input type="file" class="form-control-file" name="uploadInput" id="edit-uploadImage" style="display: none;" accept="image/*">
            <label for="edit-uploadImage" title="Upload Image" id="edit-uploadBtn" class="btn btn-sm btn-primary col-form-label ml-2" style="display: none;">
                <span class="icon text-white-600">
                    <i class="fas fa-upload d-sm-none"></i></span>
                <span class="text d-none d-sm-block">Upload Image</span>
            </label>
            <button type="button" id="edit-reset-button" class="btn btn-sm btn-danger d-none">Reset</button>
        </div>
    </div>
</div>

<div class="row mb-2">
    <div class="col-sm-6 mb-2">
        <label for="username">User Name</label>
        <input class="form-control bg-white" name="username" id="username" type="text" value="<?= isset($admin->username) ? $admin->username : set_value('username') ?>" readonly>
    </div>
    <div class="col-sm-6 mb-2">
        <label for="email">Email</label>
        <input class="form-control bg-white" name="email" id="email" type="text" value="<?= isset($admin->email) ? $admin->email : set_value('email') ?>" readonly>
    </div>
</div>

<div class="row mb-2">
    <div class="col-sm-12 mb-2">
        <label for="roles_id">Permissions</label>
        <select class="form-control rounded bg-white <?= form_error('roles_id') ? 'form-error' : '' ?>" name="roles_id[]" id="roles_id" multiple disabled required readonly>
            <?php foreach ($roles as $role): ?>
                <option class="rounded p-1 border" value="<?= $role->id ?>" 
                    <?= in_array($role->id, array_column($admin_roles, 'role_id')) ? 'selected' : '' ?>>
                    <?= $role->role_name ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
<?= form_close() ?>

