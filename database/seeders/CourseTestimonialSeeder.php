<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseTestimonial;
use Illuminate\Database\Seeder;

class CourseTestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $testimonialsByTrack = [
            'sap-erp' => [
                [
                    'student_name' => 'Sumit Singh',
                    'student_title_or_company' => 'SAP FICO Consultant @ Accenture',
                    'rating' => 5.0,
                    'review_text' => 'It is due to the practical configuration training provided here that I got placed at Accenture. The trainers focus on real system business workflows: General Ledger architecture, Asset Accounting, and foreign currency revaluation rather than mere theoretical slides.',
                    'avatar_path' => null,
                    'is_verified' => true,
                    'is_published' => true,
                ],
                [
                    'student_name' => 'Ganesh K.',
                    'student_title_or_company' => 'SAP Functional Consultant @ Deloitte',
                    'rating' => 5.0,
                    'review_text' => 'SAP Training with Real-Time Projects. Their SAP MM and FICO training is designed for real-world projects and direct system practice. The sandbox system availability 24/7 made all the difference in cracking client technical rounds.',
                    'avatar_path' => null,
                    'is_verified' => true,
                    'is_published' => true,
                ],
                [
                    'student_name' => 'Deeksha Sharma',
                    'student_title_or_company' => 'SAP Financial Analyst @ Wipro',
                    'rating' => 5.0,
                    'review_text' => 'I found ZebLearn the best place for career growth. The trainers are senior architects who explain Month-End closing activities, OBYC automatic postings, and integration with SD/MM. The resume assistance helped me get multiple interview calls.',
                    'avatar_path' => null,
                    'is_verified' => true,
                    'is_published' => true,
                ],
                [
                    'student_name' => 'Sneha Patil',
                    'student_title_or_company' => 'SAP MM Specialist @ Capgemini',
                    'rating' => 4.9,
                    'review_text' => 'Detailed feedback on SAP MM training clearing complex procurement scenarios, Split Valuation, and Release Strategies. The hands-on purchase order and inventory clearing practice was directly relevant to my daily consulting deliverables.',
                    'avatar_path' => null,
                    'is_verified' => true,
                    'is_published' => true,
                ],
                [
                    'student_name' => 'Rohan Joshi',
                    'student_title_or_company' => 'SAP S/4HANA Associate @ TCS',
                    'rating' => 5.0,
                    'review_text' => 'Learning automatic account determination with real debit/credit posting simulations cleared every concept I struggled with in self-study. Transitioning into S/4HANA was smooth thanks to the faculty guidance.',
                    'avatar_path' => null,
                    'is_verified' => true,
                    'is_published' => true,
                ],
                [
                    'student_name' => 'Amitabh Saxena',
                    'student_title_or_company' => 'SAP EWM Consultant @ Infosys',
                    'rating' => 5.0,
                    'review_text' => 'The decentralized EWM warehouse structure, storage bins, POSC, and LOSC process flows were taught with live enterprise scenarios. Secured a 45% salary hike post certification.',
                    'avatar_path' => null,
                    'is_verified' => true,
                    'is_published' => true,
                ],
            ],
            'cloud-devops' => [
                [
                    'student_name' => 'Vikram Malhotra',
                    'student_title_or_company' => 'Cloud Solutions Architect @ HCLTech',
                    'rating' => 5.0,
                    'review_text' => 'The AWS course hands-on labs on VPC peering, multi-region failover with Route 53, and auto-scaling production architectures were top tier. Cleared the Solutions Architect Associate exam on my first attempt.',
                    'avatar_path' => null,
                    'is_verified' => true,
                    'is_published' => true,
                ],
                [
                    'student_name' => 'Pooja Iyer',
                    'student_title_or_company' => 'DevOps Engineer @ Cognizant',
                    'rating' => 4.9,
                    'review_text' => 'Excellent deep dive into Kubernetes, CI/CD pipelines with GitHub Actions, and Terraform infrastructure as code. The mock interviews were instrumental in landing my current role.',
                    'avatar_path' => null,
                    'is_verified' => true,
                    'is_published' => true,
                ],
            ],
            'data-ai' => [
                [
                    'student_name' => 'Ayesha Khan',
                    'student_title_or_company' => 'Data Analyst @ Mu Sigma',
                    'rating' => 5.0,
                    'review_text' => "I joined ZebLearn's program to upgrade my skills. The Python, SQL, Power BI, and Machine Learning sessions were practical and beginner-friendly. I got placed as a Data Analyst with continuous mentor doubt solving.",
                    'avatar_path' => null,
                    'is_verified' => true,
                    'is_published' => true,
                ],
                [
                    'student_name' => 'Rohit Gupta',
                    'student_title_or_company' => 'Data Scientist @ Fractal Analytics',
                    'rating' => 5.0,
                    'review_text' => 'The course provided hands-on projects, real datasets, and expert mentorship. Their career guidance and interview preparation helped me transition into a top tech role with confidence.',
                    'avatar_path' => null,
                    'is_verified' => true,
                    'is_published' => true,
                ],
            ],
            'fullstack-programming' => [
                [
                    'student_name' => 'Nikhil Verma',
                    'student_title_or_company' => 'Full Stack Engineer @ Tech Mahindra',
                    'rating' => 5.0,
                    'review_text' => 'Spring Boot microservices, React state management, and real database optimizations were taught step-by-step. The live capstone project simulated an enterprise fintech app from end to end.',
                    'avatar_path' => null,
                    'is_verified' => true,
                    'is_published' => true,
                ],
            ],
        ];

        $courses = Course::all();

        foreach ($courses as $course) {
            $cat = $course->category ?? 'sap-erp';
            $reviews = $testimonialsByTrack[$cat] ?? $testimonialsByTrack['sap-erp'];

            foreach ($reviews as $idx => $reviewData) {
                CourseTestimonial::firstOrCreate(
                    [
                        'course_id' => $course->id,
                        'student_name' => $reviewData['student_name'],
                    ],
                    array_merge($reviewData, [
                        'sort_order' => ($idx + 1) * 10,
                    ])
                );
            }
        }
    }
}
