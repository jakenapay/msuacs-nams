<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class VisitorsPendingSeeder extends CI_Controller {

    public function index()
    {
        // Load database
        $this->load->database();
        $this->load->model('VisitorsPendingModel');

        $faker = Faker\Factory::create();

        // Number of records to generate
        $number_of_records = 50;
        $transaction_number = $this->generateTransactionNumber();
        $visit_times = ['08:00:00', '09:00:00', '10:00:00', '11:00:00', '12:00:00', '13:00:00', '14:00:00', '15:00:00', '16:00:00'];
        $visit_durations = ['Less than an hour', '1 - 2 hours', '3 - 4 hours', 'Half Day', 'Full Day'];
        $positionTypes = ['Administrator', 'Principal', 'Teacher', 'Department Head', 'Counselor', 'Staff Member'];
        // $this->db->select('first_name', 'last_name');
        // $query = $this->db->get('mytable');
        // $row = $query->row_array();


        for ($i = 0; $i < $number_of_records; $i++) {
            $data = [
                'first_name' => $faker->firstName,
                'middle_name' => $faker->optional()->firstName,
                'last_name' => $faker->lastName,
                'suffix' => $faker->optional()->randomElement(['Jr.', 'Sr.', 'II', 'III', 'IV']),
                'email' => $faker->unique()->safeEmail,
                'phone_number' => $faker->unique()->numerify('9#########'),
                'company' => $faker->optional()->company,
                'id_type' => $faker->randomElement(['Passport', 'Driver\'s License', 'National ID', 'Company ID']),
                'id_number' => $faker->unique()->numerify('ID-#########'),
                'id_front' => $faker->imageUrl(),
                'id_back' => $faker->imageUrl(),
                'visitor_image' => $faker->imageUrl(),
                'visit_purpose' => $faker->sentence(4),
                'visit_date' => $faker->dateTimeBetween('today', '+30 days')->format('Y-m-d'),
                'visit_time' => $faker->randomElement($visit_times),
                'visit_duration' => $faker->randomElement($visit_durations),
                'contact_position' => $faker->randomElement($positionTypes),
                'contact_person' => $faker->name,
                'accomodations' => $faker->optional()->sentence(),
                'parking_requirement' => $faker->randomElement(['No parking needed', 'Car parking', 'Motorcycle parking', 'Oversized vehicle parking']),
                'transaction_number' => $transaction_number,
                'status' => 1,
                'created_at' => $faker->dateTimeThisYear()->format('Y-m-d H:i:s'),
                'updated_at' => $faker->dateTimeThisYear()->format('Y-m-d H:i:s')
            ];

            $this->db->insert('visitors_pending', $data);
        }

        echo "Generated $number_of_records records for visitors_pending table.";
    }

    private function generateTransactionNumber() {
        $date = new DateTime();
        $year = $date->format('y');
        $month = $date->format('m');
        $day = $date->format('d');
        $hour = $date->format('H');
        $minute = $date->format('i');
        $second = $date->format('s');
        
        // Generate a random 8-digit number
        $random = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
                
        $transaction_number = $year . $month . $day . $hour . $minute . $second . $random;
        
        // Check if this number already exists in the database
        while ($this->VisitorsPendingModel->transactionNumberExists($transaction_number)) {
            // If it exists, generate a new random part and try again
            $random = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
            $transaction_number = $year . $month . $day . $hour . $minute . $second . $random;
        }
        
        return $transaction_number;
    }

}