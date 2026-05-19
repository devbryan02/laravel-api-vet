<?php

namespace App\Features\Stats\Queries;

use App\Features\Pet\Models\Pet;
use App\Features\Vaccine\Models\Vaccine;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardStatsQuery
{
    public function handle(): array
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $in30Days = Carbon::today()->addDays(30);

        return [
            'totalPets' => Pet::count(),
            'totalOwners' => DB::table('users')
                ->join('user_role', 'users.id', '=', 'user_role.user_id')
                ->join('roles', 'roles.id', '=', 'user_role.role_id')
                ->where('roles.name', 'OWNER')
                ->count(),
            'vaccinatedThisMonth' => Vaccine::whereBetween('aplication_date', [$startOfMonth, $today])
                ->distinct('pet_id')
                ->count('pet_id'),
            'overdueVaccines' => $this->countLatestVaccinesBefore($today),
            'upcomingIn30Days' => $this->countLatestVaccinesBetween($today, $in30Days),
            'unvaccinatedCount' => Pet::doesntHave('vaccines')->count(),
        ];
    }

    private function countLatestVaccinesBefore(Carbon $date): int
    {
        return Vaccine::whereIn('id', $this->latestVaccineIds())
            ->where('next_vaccine_date', '<', $date)
            ->distinct('pet_id')
            ->count('pet_id');
    }

    private function countLatestVaccinesBetween(Carbon $startDate, Carbon $endDate): int
    {
        return Vaccine::whereIn('id', $this->latestVaccineIds())
            ->whereBetween('next_vaccine_date', [$startDate, $endDate])
            ->distinct('pet_id')
            ->count('pet_id');
    }

    private function latestVaccineIds(): Builder
    {
        return DB::table('vaccines')
            ->select(DB::raw('MAX(id)'))
            ->whereNotNull('next_vaccine_date')
            ->groupBy('pet_id');
    }
}
