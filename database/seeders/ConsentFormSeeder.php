<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ConsentForm;

class ConsentFormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ConsentForm::create([
            'title' => 'Photo and Video Consent',
            'content' => <<<EOT
PHOTO AND VIDEO CONSENT FORM

Dear Parent/Guardian,

As part of our kindergarten program, we occasionally take photographs and videos of children during various educational activities, events, and daily learning experiences. These images help us:

• Document your child's learning journey and development  
• Share updates with families about classroom activities  
• Create learning portfolios and memory books  
• Communicate with parents through our app about daily activities  
• Celebrate achievements and special moments  

By giving consent, you are agreeing that:

1. School staff may photograph/video your child during normal school activities  
2. These images may be shared with you and other parents of children in the same classroom through our secure parent communication app  
3. Images will be used solely for educational and communication purposes  
4. We will never share your child's photos with unauthorized third parties  
5. We will not use your child's images for commercial purposes  
6. You may withdraw this consent at any time by contacting the school administration  

We take your child's privacy seriously and will handle all images with care and respect.

If you have any questions about this consent form, please contact the school office.

Thank you for your consideration.
EOT,
            'is_active' => true,
        ]);

        ConsentForm::create([
            'title' => 'General School Activities Consent',
            'content' => <<<EOT
GENERAL ACTIVITIES CONSENT FORM

Dear Parent/Guardian,

This consent form covers your child's participation in regular kindergarten activities including:

• Indoor and outdoor play activities  
• Arts and crafts projects  
• Music and movement activities  
• Educational games and learning activities  
• Group activities and social interaction  
• Supervised playground time  
• Library visits and reading activities  

By providing consent, you acknowledge that:

1. These activities are part of our standard educational program  
2. Activities are supervised by qualified staff members  
3. Safety precautions are always taken  
4. You will be notified of any incidents or concerns  
5. Your child will participate according to their individual needs and abilities  

Please contact us if you have any specific concerns about your child's participation in school activities.
EOT,
            'is_active' => true,
        ]);
    }
}
