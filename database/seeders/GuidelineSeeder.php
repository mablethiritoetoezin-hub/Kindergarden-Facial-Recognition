<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guideline;
use App\Models\User;

class GuidelineSeeder extends Seeder
{
    public function run()
    {
        Guideline::create([
            'key' => 'facial_recognition',
            'title' => 'Guidelines for Using Facial Recognition in Compliance with Privacy Laws',
            'description' => 'The use of facial recognition technology in our application is designed to enhance security and streamline photo management. Below are the guidelines to ensure compliance with privacy laws and protect the privacy of all users, including students, parents, and staff.',
            'content' => [
                [
                    'title' => 'Consent',
                    'description' => 'Obtain explicit, written consent from parents or guardians before using facial recognition for any child\'s photos. Consent forms must clearly explain how the technology is used and stored.'
                ],
                [
                    'title' => 'Data Minimization',
                    'description' => 'Only collect and process facial data necessary for identifying children in photos for classroom management purposes. Avoid storing unnecessary biometric data.'
                ],
                [
                    'title' => 'Secure Storage',
                    'description' => 'All facial recognition data must be encrypted and stored securely in compliance with data protection regulations (e.g., GDPR or COPPA). Access is restricted to authorized administrators only.'
                ],
                [
                    'title' => 'Transparency',
                    'description' => 'Inform all users (parents, teachers, and administrators) about the use of facial recognition, including its purpose, data handling procedures, and their rights to opt out.'
                ],
                [
                    'title' => 'Opt-Out Option',
                    'description' => 'Provide an easy mechanism for parents to opt out of facial recognition for their children. Photos of opted-out children will be manually tagged instead.'
                ],
                [
                    'title' => 'Data Retention',
                    'description' => 'Facial recognition data will be deleted immediately after its purpose (e.g., photo identification) is fulfilled or upon request from a parent or guardian.'
                ],
                [
                    'title' => 'Third-Party Compliance',
                    'description' => 'Ensure any third-party facial recognition services comply with privacy laws and do not share data with unauthorized entities.'
                ],
                [
                    'title' => 'Regular Audits',
                    'description' => 'Conduct regular audits of facial recognition usage and data storage to ensure ongoing compliance with privacy laws and school policies.'
                ]
            ]
        ]);
    }
}