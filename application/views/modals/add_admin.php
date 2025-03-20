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

<?= form_open('admin/admin_management/accounts/add', ['id' => 'addAdminForm', 'enctype' => 'multipart/form-data']) ?>

<div class="row mb-2">
    <div class="col-sm-12 text-center">
        <p>User Image</p>
        
    </div>
    <div class="col-sm-12 mb-2">
        <div class="video-wrapper d-flex justify-content-center">
            <video id="video" class="<?= form_error('image') ? 'form-error' : '' ?>" autoplay></video>
        </div>
        <div id="upload_image_preview" style="display: none; text-align: center; margin-y: 10px">
            <img id="upload_preview" class="rounded m-3" width="250" height="250">
        </div>
        <canvas id="canvas" style="display: none;"></canvas>
        <input type="file" name="image" id="imageInput" style="display: none;" accept="image/*">
        <div class="capture-buttons d-flex justify-content-center">
            <button type="button" id="capture-button" class="btn btn-sm btn-primary">Take Snapshot</button>
            <input type="file" class="form-control-file" name="uploadInput" id="uploadImage" style="display: none;" accept="image/*">
            <label for="uploadImage" title="Upload Image" id="uploadBtn" class="btn btn-sm btn-primary col-form-label ml-2">
            <span class="icon text-white-600">
            <i class="fas fa-upload d-sm-none"></i></span>
            <span class="text d-none d-sm-block">Upload Image</span></label>
            <button type="button" id="reset-button" class="btn btn-sm btn-danger" style="display: none;">Reset</button>
        </div>
    </div>
</div>

<div class="row mb-2">
    <div class="col-sm-12 mb-2">
        <label for="username">User Name</label>
        <input class="form-control <?= form_error('username') ? 'form-error' : '' ?>" name="username" id="username" type="text" value="<?= set_value('first_name') ?>">
    </div>
    <div class="col-sm-12 mb-2">
        <label for="email">Email</label>
        <input class="form-control <?= form_error('email') ? 'form-error' : '' ?>" name="email" id="email" type="text" value="<?= set_value('email') ?>">
    </div>
</div>

<div class="row mb-2">
    <div class="col-sm-12 mb-2">
        <label for="roles_id">Permissions (Select Multiple)</label>
        <select class="form-control <?= form_error('roles_id') ? 'form-error' : '' ?>" name="roles_id[]" id="roles_id" multiple required>
            <?php foreach ($roles as $role): ?>
                <option class="rounded m-1 p-1 border" value="<?= $role->id ?>">
                    <?= $role->role_name ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>


<div class="row mb-2">
    <div class="col-sm-12 mb-2">
        <label for="password">Password</label>
        <input class="form-control <?= form_error('password') ? 'form-error' : '' ?>" name="password" id="password" type="password">
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

