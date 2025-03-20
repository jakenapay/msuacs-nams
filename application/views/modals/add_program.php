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

<?= form_open('admin/configurations/programs/add', ['id' => 'addProgramForm', 'enctype' => 'multipart/form-data']) ?>

<div class="row mb-2">
    <div class="col-sm-3 mb-2">
        <label for="code">Program Code</label>
        <input class="form-control <?= form_error('code') ? 'form-error' : '' ?>" name="code" id="code" type="text" value="<?= set_value('code')?>" required> 
    </div>
    <div class="col-sm-9 mb-2">
        <label for="name">Program Name</label>
        <input class="form-control <?= form_error('name') ? 'form-error' : '' ?>" name="name" id="name" type="text" value="<?= set_value('name')?>" required> 
    </div>
</div>

<div class="row mb-2">
    <div class="col-sm-6 mb-2">
        <label for="college_id">College</label>
        <select class="form-control <?= form_error('college_id') ? 'form-error' : '' ?>" name="college_id" id="college_id" required>
            <option value="">Select College</option>
            <?php foreach ($colleges as $college): ?>
                <option value="<?= $college->id ?>"><?= $college->name ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-sm-6 mb-2">
        <label for="department_id">Department</label>
        <select class="form-control <?= form_error('department_id') ? 'form-error' : '' ?>" name="department_id" id="department_id" required>
            <option value="">Select Department</option>
        </select>
    </div>
</div>

<div class="d-flex mt-5">
    <button id="save-btn" class="btn btn-primary ml-auto">Save changes</button>
</div>
<?= form_close() ?>

