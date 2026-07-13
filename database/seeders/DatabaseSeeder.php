<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\DoctorHoliday;
use App\Models\Bed;
use App\Models\OperationTheater;
use App\Models\SurgicalSupply;
use App\Models\SystemSetting;
use App\Models\Department;
use App\Models\Service;
use App\Models\Testimonial;
use App\Models\Notice;
use App\Models\Facility;
use App\Models\Award;
use App\Models\WorkingHour;
use App\Models\NewsCategory;
use App\Models\News;
use App\Models\FaqCategory;
use App\Models\Faq;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $passwordHash = Hash::make('password');

        // --- SEED PUBLIC GUEST DATA TABLES FIRST ---

        // 1. Departments
        $cardioDep = Department::create([
            'name' => 'Cardiology',
            'slug' => 'cardiology',
            'description' => 'Comprehensive cardiac care including bypass surgery, heart failure management, and diagnostics.',
            'icon' => 'HeartPulse',
            'is_active' => true,
        ]);

        $neuroDep = Department::create([
            'name' => 'Neurology',
            'slug' => 'neurology',
            'description' => 'Expert neurological diagnoses, neurosurgery, stroke unit, and rehabilitation.',
            'icon' => 'Brain',
            'is_active' => true,
        ]);

        $pedsDep = Department::create([
            'name' => 'Pediatrics',
            'slug' => 'pediatrics',
            'description' => 'Compassionate care for infants, children, and adolescents including immunizations.',
            'icon' => 'Baby',
            'is_active' => true,
        ]);

        $genDep = Department::create([
            'name' => 'General Medicine',
            'slug' => 'general-medicine',
            'description' => 'Primary family medicine, preventative healthcare, and everyday outpatient consultations.',
            'icon' => 'Stethoscope',
            'is_active' => true,
        ]);

        // 2. Services
        Service::create([
            'name' => 'Coronary Bypass Surgery',
            'slug' => 'coronary-bypass',
            'description' => 'Advanced bypass surgery performed by world-class cardiothoracic surgeons.',
            'icon' => 'Heart',
            'department_id' => $cardioDep->id,
            'is_active' => true,
        ]);

        Service::create([
            'name' => 'Electroencephalogram (EEG)',
            'slug' => 'eeg-test',
            'description' => 'Accurate neurological brain activity recording and analysis.',
            'icon' => 'Activity',
            'department_id' => $neuroDep->id,
            'is_active' => true,
        ]);

        Service::create([
            'name' => 'Pediatric Immunization',
            'slug' => 'pediatric-immunization',
            'description' => 'Essential vaccines and developmental checkups for children.',
            'icon' => 'Shield',
            'department_id' => $pedsDep->id,
            'is_active' => true,
        ]);

        Service::create([
            'name' => 'Annual Health Checkup',
            'slug' => 'annual-checkup',
            'description' => 'Full diagnostic body panels, physical exams, and consultant summaries.',
            'icon' => 'FileText',
            'department_id' => $genDep->id,
            'is_active' => true,
        ]);

        // 3. Testimonials
        Testimonial::create([
            'patient_name' => 'Charles Babbage',
            'patient_title' => 'Cardiac Patient',
            'comment' => 'The bypass surgery here saved my life. Dr. Elizabeth is truly gifted and the nurses were so compassionate.',
            'rating' => 5,
            'is_approved' => true,
        ]);

        Testimonial::create([
            'patient_name' => 'Grace Hopper',
            'patient_title' => 'General Patient',
            'comment' => 'Scheduling a checkup was incredibly quick. The facility is extremely clean and state-of-the-art.',
            'rating' => 5,
            'is_approved' => true,
        ]);

        Testimonial::create([
            'patient_name' => 'Richard Feynman',
            'patient_title' => 'Neurology Outpatient',
            'comment' => 'Dr. Robert is a brilliant neurologist. Explained the EEG findings very clearly.',
            'rating' => 4,
            'is_approved' => true,
        ]);

        // 4. Notices
        Notice::create([
            'title' => 'New Cardiology Ward Opened',
            'content' => 'We are proud to announce the grand opening of our expanded 3rd floor Cardiac wing with 5 new Private Deluxe rooms.',
            'is_active' => true,
        ]);

        Notice::create([
            'title' => 'COVID-19 Booster Rosters',
            'content' => 'Booster vaccines are available at our Outpatient General clinic Monday through Friday without bookings.',
            'is_active' => true,
        ]);

        // 5. Facilities
        Facility::create([
            'name' => 'State-of-the-Art OTs',
            'description' => 'Advanced laminar airflow operating theaters with robotic systems and heart-lung machines.',
            'icon' => 'Cpu',
            'is_active' => true,
        ]);

        Facility::create([
            'name' => '24/7 Trauma Ambulance',
            'description' => 'Fully stocked intensive care ambulances with expert paramedics dispatched instantly.',
            'icon' => 'Truck',
            'is_active' => true,
        ]);

        // 6. Awards
        Award::create([
            'title' => 'National Clinical Excellence Award',
            'organization' => 'Ministry of Health Services',
            'year' => 2025,
            'is_active' => true,
        ]);

        Award::create([
            'title' => 'Most Disinfected & Cleanest Facility',
            'organization' => 'Hospital Hygiene Standards Board',
            'year' => 2024,
            'is_active' => true,
        ]);

        // 7. Working Hours
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        foreach ($days as $day) {
            WorkingHour::create([
                'day' => $day,
                'hours' => $day === 'Sunday' ? 'Closed (Emergency Only)' : '08:00 AM - 08:00 PM',
                'is_closed' => $day === 'Sunday',
            ]);
        }

        // 8. News / Blogs
        $newsCat = NewsCategory::create([
            'name' => 'Preventative Health',
            'slug' => 'preventative-health',
        ]);

        News::create([
            'title' => '10 Tips to Keep Your Heart Healthy',
            'slug' => '10-tips-healthy-heart',
            'content' => 'Maintaining a healthy cardiovascular system is vital for longevity. Exercises, a balanced fiber diet, and regular blood pressure screening can significantly reduce risks of cardiac diseases...',
            'image_url' => 'https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg',
            'category_id' => $newsCat->id,
            'is_published' => true,
        ]);

        // 9. FAQs
        $faqCat = FaqCategory::create([
            'name' => 'Admissions & Insurance',
            'slug' => 'admissions-insurance',
            'is_active' => true,
        ]);

        Faq::create([
            'question' => 'How do I claim insurance for admissions?',
            'answer' => 'Present your insurance policy card at the reception counter during admission. Our cashier desk will directly coordinate with your corporate provider for cashless clearances.',
            'category_id' => $faqCat->id,
            'order' => 1,
            'is_active' => true,
        ]);


        // --- SEED SYSTEM USERS AND ROLES ---

        // Super Admin
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@hms.com',
            'phone' => '1234567890',
            'password_hash' => $passwordHash,
            'role' => 'super_admin',
            'is_active' => true,
        ]);
        UserProfile::create([
            'user_id' => $superAdmin->id,
            'gender' => 'male',
            'blood_group' => 'A+',
            'address' => 'HMS HQ Street',
        ]);

        // Hospital Admin
        $hospitalAdmin = User::create([
            'name' => 'Hospital Admin',
            'email' => 'hospital@hms.com',
            'phone' => '1234567891',
            'password_hash' => $passwordHash,
            'role' => 'hospital_admin',
            'is_active' => true,
        ]);
        UserProfile::create([
            'user_id' => $hospitalAdmin->id,
            'gender' => 'female',
            'blood_group' => 'O+',
        ]);

        // Doctors (Linked to newly created Departments)
        $doctorsData = [
            [
                'name' => 'Dr. Elizabeth Blackwell',
                'email' => 'doctor1@hms.com',
                'phone' => '1112223331',
                'specialization' => 'Cardiology',
                'fee' => 150.00,
                'sfee' => 1500.00,
                'exp' => 12,
                'room' => 'Chamber A-101',
                'department_id' => $cardioDep->id,
                'is_featured' => true,
            ],
            [
                'name' => 'Dr. Robert Koch',
                'email' => 'doctor2@hms.com',
                'phone' => '1112223332',
                'specialization' => 'Neurology',
                'fee' => 180.00,
                'sfee' => 2000.00,
                'exp' => 15,
                'room' => 'Chamber B-205',
                'department_id' => $neuroDep->id,
                'is_featured' => true,
            ],
            [
                'name' => 'Dr. Virginia Apgar',
                'email' => 'doctor3@hms.com',
                'phone' => '1112223333',
                'specialization' => 'Pediatrics',
                'fee' => 120.00,
                'sfee' => 800.00,
                'exp' => 8,
                'room' => 'Chamber C-104',
                'department_id' => $pedsDep->id,
                'is_featured' => true,
            ]
        ];

        foreach ($doctorsData as $i => $data) {
            $docUser = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password_hash' => $passwordHash,
                'role' => 'doctor',
                'is_active' => true,
            ]);

            UserProfile::create([
                'user_id' => $docUser->id,
                'gender' => $i === 0 || $i === 2 ? 'female' : 'male',
                'blood_group' => 'AB+',
            ]);

            Doctor::create([
                'user_id' => $docUser->id,
                'specialization' => $data['specialization'],
                'consultation_fee' => $data['fee'],
                'surgery_fee' => $data['sfee'],
                'experience_years' => $data['exp'],
                'qualifications' => ['MD', 'FACS', 'Ph.D'],
                'chamber_location' => $data['room'],
                'department_id' => $data['department_id'],
                'is_featured' => $data['is_featured'],
            ]);

            // Doctor Schedule (Saturdays to Thursdays)
            $scheduleDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'sunday'];
            foreach ($scheduleDays as $day) {
                DoctorSchedule::create([
                    'doctor_id' => $docUser->id,
                    'day_of_week' => $day,
                    'start_time' => '09:00:00',
                    'end_time' => '13:00:00',
                    'slot_duration' => 15,
                    'max_patients' => 15,
                    'is_available' => true,
                    'chamber_location' => $data['room'],
                ]);
            }

            // Doctor Holidays
            DoctorHoliday::create([
                'doctor_id' => $docUser->id,
                'holiday_date' => date('Y-m-d', strtotime('+3 days')),
                'reason' => 'Medical Conference',
            ]);
        }

        // Nurses
        $nurseNames = ['Florence Nightingale', 'Clara Barton'];
        foreach ($nurseNames as $i => $name) {
            $nurseUser = User::create([
                'name' => $name,
                'email' => "nurse" . ($i + 1) . "@hms.com",
                'phone' => '222333444' . $i,
                'password_hash' => $passwordHash,
                'role' => 'nurse',
                'is_active' => true,
            ]);
            UserProfile::create([
                'user_id' => $nurseUser->id,
                'gender' => 'female',
            ]);
        }

        // Receptionist
        $receptionist = User::create([
            'name' => 'Jane Desk',
            'email' => 'receptionist@hms.com',
            'phone' => '3334445550',
            'password_hash' => $passwordHash,
            'role' => 'receptionist',
            'is_active' => true,
        ]);
        UserProfile::create([
            'user_id' => $receptionist->id,
            'gender' => 'female',
        ]);

        // Cashier
        $cashier = User::create([
            'name' => 'John Ledger',
            'email' => 'cashier@hms.com',
            'phone' => '4445556660',
            'password_hash' => $passwordHash,
            'role' => 'cashier',
            'is_active' => true,
        ]);
        UserProfile::create([
            'user_id' => $cashier->id,
            'gender' => 'male',
        ]);

        // Patients
        $patientsData = [
            ['name' => 'Ada Lovelace', 'email' => 'patient1@hms.com', 'phone' => '5556667771', 'pid' => 'PAT-0001', 'dob' => '1995-12-10', 'bg' => 'A-', 'history' => ['Allergy: Penicillin']],
            ['name' => 'Alan Turing', 'email' => 'patient2@hms.com', 'phone' => '5556667772', 'pid' => 'PAT-0002', 'dob' => '1990-06-23', 'bg' => 'O+', 'history' => ['Asthma', 'Hypertension']]
        ];

        foreach ($patientsData as $data) {
            $patientUser = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password_hash' => $passwordHash,
                'role' => 'patient',
                'patient_id' => $data['pid'],
                'is_active' => true,
            ]);
            UserProfile::create([
                'user_id' => $patientUser->id,
                'date_of_birth' => $data['dob'],
                'gender' => $data['name'] == 'Ada Lovelace' ? 'female' : 'male',
                'blood_group' => $data['bg'],
                'address' => 'Baker Street 221B, London',
                'emergency_contact_name' => 'Charles Babbage',
                'emergency_contact_phone' => '9998887771',
                'medical_history' => $data['history'],
            ]);
        }

        // Beds
        $bedTypes = [
            ['General', 'G-101', 50.00],
            ['General', 'G-102', 50.00],
            ['Private', 'P-201', 150.00],
            ['Private', 'P-202', 150.00],
            ['Deluxe', 'D-301', 300.00],
            ['ICU', 'ICU-401', 800.00],
            ['ICU', 'ICU-402', 800.00],
            ['HDU', 'HDU-501', 500.00],
        ];

        foreach ($bedTypes as $bed) {
            Bed::create([
                'bed_number' => $bed[1],
                'ward_name' => $bed[0] . ' Ward',
                'bed_type' => $bed[0],
                'status' => 'available',
                'daily_charge' => $bed[2],
                'features' => ['adjustable_backrest', 'oxygen_ports'],
            ]);
        }

        // Operation Theaters
        $ots = [
            ['OT-01', 'General Operating Theater 1', 'general', 1, 200.00, 100.00],
            ['OT-02', 'Cardiac Specialist OT', 'cardiac', 1, 500.00, 250.00],
            ['OT-03', 'Neuro & Trauma OT', 'neuro', 1, 600.00, 300.00],
        ];

        foreach ($ots as $ot) {
            OperationTheater::create([
                'ot_number' => $ot[0],
                'ot_name' => $ot[1],
                'ot_type' => $ot[2],
                'capacity' => $ot[3],
                'is_active' => true,
                'has_ventilator' => true,
                'has_heart_lung_machine' => $ot[2] === 'cardiac',
                'has_c_arm' => true,
                'has_microscope' => $ot[2] === 'neuro',
                'has_laparoscopic_tower' => true,
                'floor' => '3rd Floor',
                'room_number' => '30' . rand(1, 9),
                'base_charge' => $ot[4],
                'per_hour_charge' => $ot[5],
                'status' => 'available',
            ]);
        }

        // Surgical Supplies
        $supplies = [
            ['SUP-01', 'Nylon Suture 3-0', 'suture', 'box', 12, 45, 10, 100, 15, 30.00, 45.00],
            ['SUP-02', 'Sterile Gauze Pads 4x4', 'dressing', 'pack', 50, 120, 20, 200, 30, 5.00, 8.50],
            ['SUP-03', 'Surgical Gloves Size 7.5', 'glove', 'box', 100, 80, 15, 150, 25, 25.00, 40.00],
            ['SUP-04', 'N95 Respirator Masks', 'mask', 'box', 20, 15, 10, 80, 12, 18.00, 28.00],
            ['SUP-05', 'Disposable Surgical Gowns', 'gown', 'pack', 10, 35, 5, 50, 10, 50.00, 75.00],
            ['SUP-06', 'Foley Catheter 16 Fr', 'catheter', 'piece', 1, 9, 5, 50, 10, 8.00, 15.00],
            ['SUP-07', 'Endotracheal Tube 7.5mm', 'tube', 'piece', 1, 22, 5, 50, 10, 12.00, 22.00],
            ['SUP-08', 'Titanium Hip Implant', 'implant', 'piece', 1, 4, 2, 10, 3, 1200.00, 2500.00],
        ];

        foreach ($supplies as $supply) {
            SurgicalSupply::create([
                'supply_code' => $supply[0],
                'supply_name' => $supply[1],
                'category' => $supply[2],
                'unit' => $supply[3],
                'quantity_per_unit' => $supply[4],
                'current_stock' => $supply[5],
                'minimum_stock' => $supply[6],
                'maximum_stock' => $supply[7],
                'reorder_level' => $supply[8],
                'purchase_price' => $supply[9],
                'selling_price' => $supply[10],
                'last_purchase_price' => $supply[9],
                'supplier_name' => 'MedLife Diagnostics & Supplies Ltd.',
                'supplier_contact' => '+14445550192',
                'expiry_date' => date('Y-m-d', strtotime('+365 days')),
                'storage_location' => 'Main Wing Storage',
                'shelf_number' => 'Shelf A' . rand(1, 5),
                'is_active' => true,
            ]);
        }

        // --- SEED SYSTEM SETTINGS ---
        $settings = [
            ['hospital_name', 'St. Jude General Hospital', 'Official name of the hospital'],
            ['currency', 'USD', 'Primary system currency'],
            ['otp_expiry_minutes', '5', 'Duration of OTP validity'],
            ['jwt_expiry_days', '7', 'Token expiration duration'],
            ['tax_rate', '5.0', 'Percentage of tax applied on billing'],
            ['hero_title', 'Providing World-Class Medical Care', 'Main hero header on public homepage'],
            ['hero_subtitle', 'State-of-the-art facilities, certified medical specialists, and compassionate inpatient nursing rounds.', 'Sub-headline on public homepage'],
            ['hero_image', 'https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg', 'Hero illustration image URL'],
            ['emergency_phone', '+1 (555) 999-9111', 'Emergency hotline phone contact'],
            ['emergency_hours', '24 Hours / 365 Days', 'Working hours of the trauma wing'],
            ['hospital_established', '2010', 'Year of hospital foundation'],
            ['hospital_description', 'St. Jude General Hospital is a premier medical provider with leading specialists in Cardiology, Neurology, and Pediatrics.', 'Extended company overview'],
            ['mission', 'To deliver clinical excellence with compassion, integrity, and state-of-the-art modern therapeutics.', 'Mission statement'],
            ['vision', 'To be the most trusted and advanced medical system globally.', 'Vision statement'],
            ['core_values', 'Compassion, Integrity, Excellence, Innovation, Patient-First approach.', 'Core organization values'],
            ['google_map_embed_url', 'https://maps.google.com', 'Embed Google Maps coordinate link'],
            ['terms_and_conditions', 'All consultations, surgeries, and bed admissions are subject to hospital rules and guidelines. Emergency clearance takes top precedence...', 'Official terms of services'],
            ['privacy_policy', 'We strictly protect patient confidentiality under HIPAA regulations. Medical data logs are encrypted securely.', 'Official patient privacy guidelines'],
            ['facebook_url', 'https://facebook.com', 'Social Link Facebook'],
            ['youtube_url', 'https://youtube.com', 'Social Link YouTube'],
            ['linkedin_url', 'https://linkedin.com', 'Social Link LinkedIn'],
            ['instagram_url', 'https://instagram.com', 'Social Link Instagram'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting[0]],
                ['value' => $setting[1], 'description' => $setting[2]]
            );
        }
    }
}
