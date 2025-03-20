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
<div class="row">
    <div class="col-sm-12">
        <div class="d-flex justify-content-between">
            <p class="m-2"><span class="small">Created at:</span> 
                <span class="badge badge-success text-white">
                    <?= (new DateTime($admin->created_at))->format('M j, Y g:iA') ?>
                </span>
            </p>
            <p class="m-2"><span class="small">Last Updated:</span> 
                <span class="badge badge-primary text-white">
                    <?= (new DateTime($admin->updated_at))->format('M j, Y g:iA') ?>
                </span>
            </p>
        </div>
    </div>
</div>
<?= form_open('admin/admin_management/accounts/edit/' . $admin->id, ['id' => 'editAdminForm', 'enctype' => 'multipart/form-data']) ?>

<div class="row mb-2">
    <div class="col-sm-6 mb-2">
        <input class="form-control" name="id" id="id" type="number" value="<?= isset($admin->id) ? $admin->id : '' ?>" hidden>
    </div>
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
            <button type="button" id="edit-reset-button" class="btn btn-sm btn-danger">Reset</button>
        </div>
    </div>
</div>

<div class="row mb-2">
    <div class="col-sm-6 mb-2">
        <label for="username">User Name</label>
        <input class="form-control" name="username" id="username" type="text" value="<?= isset($admin->username) ? $admin->username : set_value('username') ?>">
    </div>
    <div class="col-sm-6 mb-2">
        <label for="email">Email</label>
        <input class="form-control" name="email" id="email" type="text" value="<?= isset($admin->email) ? $admin->email : set_value('email') ?>">
    </div>
</div>

<div class="row mb-2">
    <div class="col-sm-12 mb-2">
        <label for="roles_id">Permissions</label>
        <select class="form-control rounded <?= form_error('roles_id') ? 'form-error' : '' ?>" name="roles_id[]" id="roles_id" multiple required>
            <?php foreach ($roles as $role): ?>
                <option class="rounded p-1 border" value="<?= $role->id ?>" 
                    <?= in_array($role->id, array_column($admin_roles, 'role_id')) ? 'selected' : '' ?>>
                    <?= $role->role_name ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>


<div class="row mb-2">
    <div class="col-sm-12 mb-2">
        <label for="password">Password (Leave empty to keep current password)</label>
        <input class="form-control <?= form_error('password') ? 'form-error' : '' ?>" name="password" id="password" type="password" placeholder="Leave empty to keep current password">
    </div>
    <div class="col-sm-12 mb-2">
        <label for="confirm_password">Confirm Password</label>
        <input class="form-control <?= form_error('confirm_password') ? 'form-error' : '' ?>" name="confirm_password" id="confirm_password" type="password" value="<?= set_value('confirm_password') ?>">
    </div>
</div>

<div class="d-flex mt-5">
    <button id="save-btn" class="btn btn-primary ml-auto">Save changes</button>
</div>
<?= form_close() ?>

