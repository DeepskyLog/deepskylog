<?php

namespace Database\Seeders;

use App\Models\CometObservationsOld;
use App\Models\ObservationsOld;
use App\Models\SketchOfTheWeek;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class SketchOfTheWeekSeeder extends Seeder
{
    // Negative is for comet sketches
    protected static array $sketchOfTheWeek = [
        168825 => 20240825, 192512 => 20240818, 192580 => 20240811,
        141266 => 20240804, 171858 => 20240728,
        142065 => 20240721, 192264 => 20240714, -2799 => 20240707, 192174 => 20240630, 192156 => 20240623,
        10991 => 20240616, 128545 => 20240609, 187357 => 20240602, 13615 => 20240526, 63370 => 20240520,
        171793 => 20240512, 44239 => 20240505, 174949 => 20240421, 182005 => 20240310, 185360 => 20240303,
        127775 => 20240226, 3184 => 20240218, 175078 => 20240211, 175079 => 20240204, 157706 => 20240128,
        136494 => 20240121, 171788 => 20240114, 158065 => 20240107, 72891 => 20231231, 173323 => 20231224,
        173303 => 20231217, 42508 => 20231210, 128564 => 20231203, 23440 => 20231126, 189465 => 20231119,
        167716 => 20231112, 189350 => 20231105, 119453 => 20231029, 78486 => 20231022, 77559 => 20231015,
        171772 => 20231008, 78913 => 20231001, 151722 => 20220620, 166750 => 20220515, 118633 => 20220501,
        167764 => 20220424, 149773 => 20220403, 16296 => 20220320, 142086 => 20220220, 114347 => 20220206,
        75267 => 20220130, 148069 => 20220123, 169610 => 20220109, 63237 => 20220102, 157837 => 20211219,
        139193 => 20211212, 144499 => 20211031, 55576 => 20211018, 143528 => 20210926, 143326 => 20210914,
        76131 => 20210829, 106689 => 20210823, 153706 => 20210815, 78829 => 20210725, 33904 => 20210718,
        106976 => 20210712, 20860 => 20210604, 151611 => 20210530, 145471 => 20210523, 138338 => 20210516,
        24141 => 20210509, 18190 => 20210418, 138098 => 20210411, 123820 => 20210404, 148466 => 20210328,
        103222 => 20210314, 102682 => 20210307, 45963 => 20210228, 110768 => 20210221, 142785 => 20210207,
        58783 => 20210131, 79995 => 20210124, 80551 => 20210117, 136497 => 20210110, 78424 => 20210103,
        15553 => 20201227, 127495 => 20201221, 144611 => 20201213, 143138 => 20201129, 133722 => 20201122,
        128535 => 20201115, 134450 => 20201108, 112519 => 20201018, 122401 => 20201005, 28349 => 20200927,
        78814 => 20200921, 78413 => 20200912, 119419 => 20200906, 132084 => 20200830, 151718 => 20200824,
        126739 => 20200808, 144404 => 20200718, -2518 => 20200712, 65450 => 20200705, 138231 => 20200628,
        25537 => 20200621, 138346 => 20200614, 77578 => 20200607, 105408 => 20200531, 125329 => 20200524,
        59953 => 20200517, 128317 => 20200510, 80695 => 20200503, 76250 => 20200426, 138589 => 20200419,
        137915 => 20200414, 146045 => 20200406, 104705 => 20200330, 127862 => 20200323, 124593 => 20200316,
        74983 => 20200216, 144375 => 20200209, 143080 => 20200202, 143847 => 20200126, 111850 => 20200112,
        110617 => 20200104,  110481 => 20191216, 133295 => 20191208, 142556 => 20191130,
        65527 => 20191124, 133691 => 20191117, 41999 => 20191110, 142478 => 20191027, 14137 => 20191023,
        130399 => 20191004, 13719 => 20190922, 109955 => 20190915, 141703 => 20190910, 139509 => 20190901,
        85880 => 20190824, 108619 => 20190818, 27403 => 20190804, 53559 => 20190726, 133049 => 20190719,
        120483 => 20190713, 73945 => 20190708, 138551 => 20190630, 130949 => 20190623, 81234 => 20190617,
        127074 => 20190610, 60971 => 20190603, 128447 => 20190520, 24294 => 20190514, 118470 => 20190426,
        43606 => 20190416, 105430 => 20190405, 33566 => 20190331, 44400 => 20190322, 18189 => 20190317,
        18212 => 20190309, 69765 => 20190301, 117784 => 20190223, 16202 => 20190217, 28893 => 20190210,
        80536 => 20190201, 23230 => 20190125, 134885 => 20190118, 101164 => 20190111, 75116 => 20190104,
        133080 => 20181229, 110756 => 20181222, -2309 => 20181216, 28356 => 20181211, 127729 => 20181202,
        124348 => 20181124, 107574 => 20181111, 84819 => 20181102, 73949 => 20181026, 20795 => 20181019,
        74413 => 20181007, 110468 => 20180928, 131229 => 20180921, 67741 => 20180906, 107396 => 20180831,
        77554 => 20180811, 12800 => 20180803, 74966 => 20180727, 128644 => 20180720, 60143 => 20180713,
        13429 => 20180706, 117769 => 20180630, 105520 => 20180624, 20818 => 20180616, 36213 => 20180608,
        128250 => 20180601, 57444 => 20180525, 76812 => 20180518, 71575 => 20180511, 52357 => 20180506,
        106682 => 20180427, 16520 => 20180420, 45460 => 20180413, 56245 => 20180406, 125141 => 20180330,
        21631 => 20180324, 124573 => 20180316, 18224 => 20180309, 76212 => 20180302, 113537 => 20180223,
        28952 => 20180216, 10516 => 20180209, 14059 => 20180202, 100314 => 20180126, 14720 => 20180119];

    public function run(): void
    {
        // Loop over all elements in the sketchOfTheWeek array
        foreach (SketchOfTheWeekSeeder::$sketchOfTheWeek as $observationId => $date) {
            // Read year, month and day from $date
            $year = substr($date, 0, 4);
            $month = substr($date, 4, 2);
            $day = substr($date, 6, 2);

            // Put the year, month and day in a date object
            $date = Carbon::create($year, $month, $day, 0, 0, 0, 'UTC');

            if ($observationId < 0) {
                $username = CometObservationsOld::find(-$observationId);
            } else {
                // Get the corresponding username
                $username = ObservationsOld::find($observationId);
            }
            if ($username) {
                // Get the corresponding user id
                $userId = User::where('username', html_entity_decode($username->observerid))->first()->id;

                SketchOfTheWeek::create([
                    'observation_id' => $observationId,
                    'user_id' => $userId,
                    'date' => $date,
                ]);
            }
        }
    }
}
