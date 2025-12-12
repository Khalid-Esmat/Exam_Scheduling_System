<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseSeeder extends Seeder
{
    public function run()
    {
        DB::table('courses')->insert([
            /*
            |--------------------------------------------------------------------------
            | LEVEL 1
            |--------------------------------------------------------------------------
            */
            ['course_name'=>'رياضة ١','course_code'=>'MA111','level'=>1,'credit_hours'=>3,'semester'=>1],
            ['course_name'=>'تاريخ الحوسبة','course_code'=>'HU151','level'=>1,'credit_hours'=>3,'semester'=>1],
            ['course_name'=>'لغة إنجليزية ١','course_code'=>'HU111','level'=>1,'credit_hours'=>2,'semester'=>1],
            ['course_name'=>'أساسيات علوم الحاسب','course_code'=>'CS121','level'=>1,'credit_hours'=>3,'semester'=>1],
            ['course_name'=>'إلكترونيات','course_code'=>'EE101','level'=>1,'credit_hours'=>3,'semester'=>1],
            ['course_name'=>'فيزياء','course_code'=>'PH201','level'=>1,'credit_hours'=>3,'semester'=>1],
            ['course_name'=>'التفكير الإبداعي ومهارات الاتصال','course_code'=>'HU113','level'=>1,'credit_hours'=>1,'semester'=>1],

            /*
            |--------------------------------------------------------------------------
            | LEVEL 2
            |--------------------------------------------------------------------------
            */
            ['course_name'=>'البرمجة الشيئية','course_code'=>'CS241','level'=>2,'credit_hours'=>3,'semester'=>1],
            ['course_name'=>'الاحتمالات والإحصاء ٢','course_code'=>'MA222','level'=>2,'credit_hours'=>3,'semester'=>1],
            ['course_name'=>'لغة إنجليزية ٢','course_code'=>'HU112','level'=>2,'credit_hours'=>2,'semester'=>1],
            ['course_name'=>'إدارة الأعمال','course_code'=>'HU231','level'=>2,'credit_hours'=>1,'semester'=>1],
            ['course_name'=>'التصميم المنطقي','course_code'=>'EE201','level'=>2,'credit_hours'=>3,'semester'=>1],
            ['course_name'=>'بحوث العمليات','course_code'=>'MA231','level'=>2,'credit_hours'=>3,'semester'=>1],
            ['course_name'=>'قواعد البيانات','course_code'=>'IS211','level'=>2,'credit_hours'=>3,'semester'=>1],

            /*
            |--------------------------------------------------------------------------
            | LEVEL 3 (CS)
            |--------------------------------------------------------------------------
            */
            ['course_name'=>'نظم التشغيل ١','course_code'=>'CS321','level'=>3,'credit_hours'=>3,'semester'=>1],
            ['course_name'=>'تحليل وتصميم الخوارزميات','course_code'=>'CS311','level'=>3,'credit_hours'=>3,'semester'=>1],
            ['course_name'=>'هياكل البيانات ٢','course_code'=>'CS312','level'=>3,'credit_hours'=>3,'semester'=>1],
            ['course_name'=>'بنية وتنظيم الحاسبات','course_code'=>'CS322','level'=>3,'credit_hours'=>3,'semester'=>1],
            ['course_name'=>'هندسة البرمجيات','course_code'=>'CS391','level'=>3,'credit_hours'=>3,'semester'=>1],
            ['course_name'=>'الرسم بالحاسب ١','course_code'=>'CS351','level'=>3,'credit_hours'=>3,'semester'=>1],

            /*
            |--------------------------------------------------------------------------
            | LEVEL 3 (IS)
            |--------------------------------------------------------------------------
            */
            ['course_name'=>'نظم إدارة قواعد البيانات','course_code'=>'IS311','level'=>3,'credit_hours'=>3,'semester'=>1],
            ['course_name'=>'مستودعات البيانات','course_code'=>'IS312','level'=>3,'credit_hours'=>3,'semester'=>1],
            ['course_name'=>'أساسيات نظم المعلومات','course_code'=>'IS301','level'=>3,'credit_hours'=>3,'semester'=>1],
            ['course_name'=>'تحليل وتصميم نظم المعلومات','course_code'=>'IS331','level'=>3,'credit_hours'=>3,'semester'=>1],

            /*
            |--------------------------------------------------------------------------
            | LEVEL 4 (CS)
            |--------------------------------------------------------------------------
            */
            ['course_name'=>'الرؤية بالحاسب','course_code'=>'CS453','level'=>4,'credit_hours'=>3,'semester'=>1],
            ['course_name'=>'تحليل البيانات الضخمة','course_code'=>'CS431','level'=>4,'credit_hours'=>3,'semester'=>1],
            ['course_name'=>'أمن البرمجيات','course_code'=>'CS471','level'=>4,'credit_hours'=>3,'semester'=>1],
            ['course_name'=>'نظرية الحاسبات','course_code'=>'CS401','level'=>4,'credit_hours'=>3,'semester'=>1],

            /*
            |--------------------------------------------------------------------------
            | LEVEL 4 (IS)
            |--------------------------------------------------------------------------
            */
            ['course_name'=>'هندسة خدمة التوجه','course_code'=>'IS412','level'=>4,'credit_hours'=>3,'semester'=>1],
            ['course_name'=>'إدارة ونمذجة البيانات الكبيرة','course_code'=>'IS431','level'=>4,'credit_hours'=>3,'semester'=>1],
            ['course_name'=>'تعلم الآلة','course_code'=>'AI413','level'=>4,'credit_hours'=>3,'semester'=>1],
            ['course_name'=>'التجارة الإلكترونية','course_code'=>'IS447','level'=>4,'credit_hours'=>3,'semester'=>1],
            ['course_name'=>'التنقيب في البيانات','course_code'=>'IS411','level'=>4,'credit_hours'=>3,'semester'=>1],
        ]);
    }
}
