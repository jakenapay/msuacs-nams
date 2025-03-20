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

<?= form_open('admin/configurations/offices/edit/' . $office->id, ['id' => 'editOfficeForm', 'enctype' => 'multipart/form-data']) ?>
<div class="row mb-2">
    <div class="col-sm-6 mb-2">
        <input class="form-control" name="id" id="id" type="number" value="<?= isset($office->id) ? $office->id : '' ?>" hidden>
    </div>
    <div class="col-sm-12 mb-2">
        <label for="type">Office Name</label>
        <input class="form-control <?= form_error('name') ? 'form-error' : '' ?>" name="name" id="name" type="text" value="<?= isset($office->name) ? $office->name : set_value('name')?>" required> 
    </div>
</div>

<div class="d-flex mt-5">
    <button id="save-btn" class="btn btn-primary ml-auto">Save changes</button>
</div>
<?= form_close() ?>

