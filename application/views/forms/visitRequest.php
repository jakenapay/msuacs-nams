<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Work Sans', sans-serif;
        }

        #containerbox {
            box-shadow: rgba(0, 0, 0, 0.1) 0px 10px 15px -3px, rgba(0, 0, 0, 0.05) 0px 4px 6px -2px;
        }
    </style>
</head>

<body class="bg-light mx-5 d-flex justify-content-center align-items-center" style="height: 100vh;">
    <div id="containerbox" class="container border bg-white rounded p-4 mx-5 mt-5">
        <h2 class="mb-4 text-center">Visitor Request Form</h2>
        <br>

        <?php if ($this->session->flashdata('success')) { ?>
            <p id="information-message" class="text-center bg-success text-light py-1 rounded">
                <?= $this->session->flashdata('success') ?>
            </p>
        <?php } elseif ($this->session->flashdata('error')) { ?>
            <p id="information-message" class="text-center bg-danger text-light py-1 rounded">
                <?= $this->session->flashdata('error') ?>
            </p>
        <?php } ?>

        <?php echo form_open('visitors_pending/pending'); ?>

        <div class="row">
            <!-- First Name -->
            <div class="col-md-6 mb-3">
                <label for="first_name" class="form-label">First Name</label>
                <?php echo form_input([
                    'name' => 'first_name',
                    'id' => 'first_name',
                    'class' => 'form-control',
                    'value' => set_value('first_name'),
                    'required' => 'required',
                    'pattern' => '[A-Za-z ]{2,50}',
                    'title' => 'Only letters and spaces are allowed'
                ]); ?>
                <?php echo form_error('first_name', '<div class="text-danger">', '</div>'); ?>
            </div>

            <!-- Last Name -->
            <div class="col-md-6 mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <?php echo form_input([
                    'name' => 'last_name',
                    'id' => 'last_name',
                    'class' => 'form-control',
                    'value' => set_value('last_name'),
                    'required' => 'required',
                    'pattern' => '[A-Za-z ]+',
                    'title' => 'Only letters and spaces are allowed'
                ]); ?>
                <?php echo form_error('last_name', '<div class="text-danger">', '</div>'); ?>
            </div>


        </div>

        <div class="row">
            <!-- Phone Number -->
            <div class="col-md-6 mb-3">
                <label for="phone_number" class="form-label">Phone Number</label>
                <?php echo form_input([
                    'name' => 'phone_number',
                    'id' => 'phone_number',
                    'class' => 'form-control',
                    'value' => set_value('phone_number'),
                    'required' => 'required',
                    'pattern' => '^(09|\+639)[0-9]{9}$',  // Regex for Philippine phone numbers
                    'title' => 'Enter a valid Philippine phone number (e.g., 09123456789)',
                ]); ?>
                <?php echo form_error('phone_number', '<div class="text-danger">', '</div>'); ?>
            </div>

            <!-- Visit Date -->
            <div class="col-md-6 mb-3">
                <label for="visit_date" class="form-label">Visit Date</label>
                <?php echo form_input([
                    'name' => 'visit_date',
                    'id' => 'visit_date',
                    'class' => 'form-control',
                    'type' => 'date',
                    'value' => set_value('visit_date'),
                    'required' => 'required'
                ]); ?>
                <?php echo form_error('visit_date', '<div class="text-danger">', '</div>'); ?>
            </div>
        </div>
        <div class="row">
            <!-- Visit Time -->
            <div class="col-md-6 mb-3">
                <label for="visit_time" class="form-label">Visit Time</label>
                <select name="visit_time" id="visit_time"
                    class="form-control <?= (form_error('visit_time')) ? 'is-invalid' : '' ?>" required>
                    <option value="">Select Visit Time</option>
                    <option value="8:00 AM" <?= set_select('visit_time', '8:00 AM'); ?>>8:00 AM</option>
                    <option value="9:00 AM" <?= set_select('visit_time', '9:00 AM'); ?>>9:00 AM</option>
                    <option value="10:00 AM" <?= set_select('visit_time', '10:00 AM'); ?>>10:00 AM</option>
                    <option value="11:00 AM" <?= set_select('visit_time', '11:00 AM'); ?>>11:00 AM</option>
                    <option value="1:00 PM" <?= set_select('visit_time', '1:00 PM'); ?>>1:00 PM</option>
                    <option value="2:00 PM" <?= set_select('visit_time', '2:00 PM'); ?>>2:00 PM</option>
                    <option value="3:00 PM" <?= set_select('visit_time', '3:00 PM'); ?>>3:00 PM</option>
                    <option value="4:00 PM" <?= set_select('visit_time', '4:00 PM'); ?>>4:00 PM</option>
                </select>
                <div class="invalid-feedback"><?php echo form_error('visit_time'); ?></div>
            </div>

            <!-- Visit Purpose -->
            <div class="col-md-6 mb-3">
                <label for="visit_purpose" class="form-label">Visit Purpose</label>
                <?php
                $options = [
                    '' => 'Select Purpose',
                    'Parent and Teacher Meeting' => 'Parent and Teacher Meeting',
                    'Student Enrollment' => 'Student Enrollment',
                    'School Tour' => 'School Tour',
                    'Event Participation' => 'Event Participation',
                    'Guest Lecture' => 'Guest Lecture',
                    'Job Interview' => 'Job Interview',
                    'Maintenance Work' => 'Maintenance Work',
                    'Delivery' => 'Delivery',
                    'Other' => 'Other' // Just an option, no extra input needed
                ];

                echo form_dropdown('visit_purpose', $options, set_value('visit_purpose'), [
                    'id' => 'visit_purpose',
                    'name' => 'visit_purpose',
                    'class' => 'form-control',
                    'required' => 'required'
                ]);
                ?>
                <?php echo form_error('visit_purpose', '<div class="text-danger">', '</div>'); ?>
            </div>

        </div>

        <div class=""><br>
            <hr><br>
        </div>

        <div class="row">
            <!-- Emergency Contact Person -->
            <div class="col-md-6 mb-3">
                <label for="emergency_contact_person" class="form-label">Emergency Contact Person</label>
                <?php echo form_input([
                    'name' => 'emergency_contact_person',
                    'id' => 'emergency_contact_person',
                    'class' => 'form-control',
                    'value' => set_value('emergency_contact_person'),
                    'required' => 'required',
                    'pattern' => '[A-Za-z ]+',
                    'title' => 'Only letters and spaces are allowed'
                ]); ?>
                <?php echo form_error('emergency_contact_person', '<div class="text-danger">', '</div>'); ?>
            </div>

            <!-- Emergency Contact Number -->
            <div class="col-md-6 mb-3">
                <label for="emergency_contact_number" class="form-label">Emergency Contact Number</label>
                <?php echo form_input([
                    'name' => 'emergency_contact_number',
                    'id' => 'emergency_contact_number',
                    'class' => 'form-control ' . (form_error('emergency_contact_number') ? 'is-invalid' : ''),
                    'value' => set_value('emergency_contact_number'),
                    'required' => 'required',
                    'pattern' => '^(09|\+639)[0-9]{9}$',  // Regex for Philippine phone numbers
                    'title' => 'Enter a valid Philippine phone number (e.g., 09123456789)',
                ]); ?>
                <div class="invalid-feedback"><?php echo form_error('emergency_contact_number'); ?></div>
            </div>
        </div>
        <br>

        <!-- Submit Button -->
        <div class="mb-1 text-end">
            <?php echo form_submit('submit', 'Submit', ['class' => 'btn btn-success px-4']); ?>
        </div>

        <?php echo form_close(); ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>