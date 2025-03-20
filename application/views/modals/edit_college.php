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

<?= form_open('admin/configurations/colleges/edit/' . $college->id, ['id' => 'editCollegeForm', 'enctype' => 'multipart/form-data']) ?>
<div class="row mb-2">
    <input class="form-control" name="id" id="id" type="number" value="<?= isset($college->id) ? $college->id : '' ?>" hidden>
    <div class="col-sm-3 mb-2">
        <label for="college_code">College Code</label>
        <input class="form-control <?= form_error('college_code') ? 'form-error' : '' ?>" name="college_code" id="college_code" type="text" value="<?= isset($college->college_code) ? $college->college_code : set_value('college_code')?>" style="text-transform:uppercase" required>
    </div>
    <div class="col-sm-9 mb-2">
        <label for="type">College Name</label>
        <input class="form-control <?= form_error('name') ? 'form-error' : '' ?>" name="name" id="name" type="text" value="<?= isset($college->name) ? $college->name : set_value('name')?>" required> 
    </div>
</div>

<div class="d-flex mt-5">
    <button id="save-btn" class="btn btn-primary ml-auto">Save changes</button>
</div>
<?= form_close() ?>

