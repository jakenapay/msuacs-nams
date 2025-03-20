<?= form_open_multipart('admin/user_management/visiting_officer/excel_import', ['id' => 'excelImportForm', 'class' => 'excelImport']); ?>
<div class="container">
    <p><label>Select Excel File</label>
    <input type="file" name="uploadFile" required accept=".xls, .xlsx, .csv"/></p>
</div>
<div class="container">
    <p><strong>Note:</strong> The excel or csv file should follow this title and column format:</p>
    <img src="<?= base_url('assets/images/SampleExcelOthers.png') ?>" class="img-fluid rounded mb-5" alt="Excel sample format">
</div>
<div class="text-right">
    <button type="submit" id="edit-btn" class="btn btn-primary">Upload</button>
</div>
<?= form_close(); ?>