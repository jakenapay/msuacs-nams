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

<?= form_open('admin/configurations/departments/add', ['id' => 'addDepartmentForm', 'enctype' => 'multipart/form-data']) ?>

<div class="row mb-2">
    <div class="col-sm-3 mb-2">
        <label for="code">Department Code</label>
        <input class="form-control <?= form_error('code') ? 'form-error' : '' ?>" name="code" id="code" type="text" value="<?= isset($department->code) ? $department->code : set_value('name')?>" required> 
    </div>
    <div class="col-sm-9 mb-2">
        <label for="name">Department Name</label>
        <input class="form-control <?= form_error('name') ? 'form-error' : '' ?>" name="name" id="name" type="text" value="<?= isset($department->name) ? $department->name : set_value('name')?>" required> 
    </div>
</div>

<div class="row mb-2">
    <div class="col-sm-12 mb-2">
        <label for="college_id">College</label>
        <select class="form-control <?= form_error('college_id') ? 'form-error' : '' ?>" name="college_id" id="college_id" required>
            <option value="">Select College</option>
            <?php foreach ($colleges as $college): ?>
                <option value="<?= $college->id ?>" <?= (isset($department->college_id) && $department->college_id == $college->id) ? 'selected' : '' ?>>
                    <?= $college->name ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<div class="d-flex mt-5">
    <button id="save-btn" class="btn btn-primary ml-auto">Save changes</button>
</div>
<?= form_close() ?>

