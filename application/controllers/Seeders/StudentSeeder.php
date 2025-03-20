<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StudentSeeder extends CI_Controller {

    public function index()
    {
        // Load database
        $this->load->database();
        $this->load->model('StudentsModel');

        $faker = Faker\Factory::create();

        // Number of records to generate
        $number_of_records = 50;
        $visit_times = ['08:00:00', '09:00:00', '10:00:00', '11:00:00', '12:00:00', '13:00:00', '14:00:00', '15:00:00', '16:00:00'];
        $visit_durations = ['Less than an hour', '1 - 2 hours', '3 - 4 hours', 'Half Day', 'Full Day'];
        $positionTypes = ['Administrator', 'Principal', 'Teacher', 'Department Head', 'Counselor', 'Staff Member'];


        for ($i = 0; $i < $number_of_records; $i++) {
            $data = [
                'first_name' => $faker->firstName,
                'middle_name' => $faker->optional()->firstName,
                'last_name' => $faker->lastName,
                'id_number' => $faker->unique()->numerify('ID-#####'),
                'image' => $faker->imageUrl(640, 480, 'people', true, 'Faker'),
                'college' => $faker->company,
                'department' => $faker->randomElement(['Science', 'Arts', 'Engineering', 'Business', 'Medicine']),
                'program' => $faker->randomElement(['Bachelor', 'Master', 'PhD', 'Certificate']),
                'rfid' => $faker->unique()->numerify('########'),
                'created_at' => $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
                'updated_at' => $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s')
            ];

            $this->db->insert('students', $data);
        }

        echo "Generated $number_of_records records for students table.";
    }
}