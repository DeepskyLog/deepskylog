<?php

namespace App\Console\Commands;

use App\Models\CometObservationsOld;
use App\Models\Instrument;
use App\Models\ObservationsOld;
use Exception;
use Illuminate\Console\Command;

class updateObservationsCommand extends Command
{
    protected $signature = 'update:observations';

    protected $description = 'Updates the instrument table with the number of observations from the old version of DeepskyLog.';

    public function handle(): void
    {
        $this->info('Updating Instruments table...');

        // Get all instruments
        $instruments = Instrument::all();

        // Check if the user with the given username already exists in the new database
        // If not, create a new user with the given username
        foreach ($instruments as $instrument) {
            try {
                $observations = ObservationsOld::where('instrumentid', $instrument->id)->count();
                $cometObservations = CometObservationsOld::where('instrumentid', $instrument->id)->count();

                $observations = $observations + $cometObservations;
            } catch (Exception $e) {
                $observations = 0;
            }

            $instrument->observations = $observations;
            $instrument->save();
        }
    }
}
