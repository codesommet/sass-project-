<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\RentalContract;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ContractInvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $agency = Agency::first();

        if (!$agency) {
            $this->command?->error('No agency found. Run AgencyUserSeeder first.');
            return;
        }

        $agencyId = $agency->id;
        $user = User::where('agency_id', $agencyId)->first() ?? User::first();

        /*
        |--------------------------------------------------------------------------
        | Vehicle Brands & Models
        |--------------------------------------------------------------------------
        */
        $brands = [
            'Dacia' => [
                ['name' => 'Logan', 'doors' => 4, 'seats' => 5, 'transmission' => 'manual', 'fuel_type' => 'diesel', 'category' => 'sedan'],
                ['name' => 'Sandero', 'doors' => 4, 'seats' => 5, 'transmission' => 'manual', 'fuel_type' => 'diesel', 'category' => 'hatchback'],
                ['name' => 'Duster', 'doors' => 4, 'seats' => 5, 'transmission' => 'automatic', 'fuel_type' => 'diesel', 'category' => 'suv'],
            ],
            'Renault' => [
                ['name' => 'Clio', 'doors' => 4, 'seats' => 5, 'transmission' => 'manual', 'fuel_type' => 'petrol', 'category' => 'hatchback'],
                ['name' => 'Megane', 'doors' => 4, 'seats' => 5, 'transmission' => 'automatic', 'fuel_type' => 'diesel', 'category' => 'sedan'],
            ],
            'Hyundai' => [
                ['name' => 'i10', 'doors' => 4, 'seats' => 5, 'transmission' => 'manual', 'fuel_type' => 'petrol', 'category' => 'hatchback'],
                ['name' => 'Tucson', 'doors' => 4, 'seats' => 5, 'transmission' => 'automatic', 'fuel_type' => 'diesel', 'category' => 'suv'],
            ],
        ];

        $vehicles = [];

        foreach ($brands as $brandName => $models) {
            $brand = VehicleBrand::firstOrCreate(
                ['agency_id' => $agencyId, 'name' => $brandName],
            );

            foreach ($models as $modelData) {
                $model = VehicleModel::firstOrCreate(
                    ['agency_id' => $agencyId, 'vehicle_brand_id' => $brand->id, 'name' => $modelData['name']],
                    $modelData,
                );

                $vehicles[] = Vehicle::firstOrCreate(
                    ['agency_id' => $agencyId, 'registration_number' => $this->fakeRegistration($brandName, $modelData['name'])],
                    [
                        'agency_id' => $agencyId,
                        'vehicle_model_id' => $model->id,
                        'registration_city' => 'Casablanca',
                        'year' => rand(2020, 2025),
                        'color' => collect(['Blanc', 'Noir', 'Gris', 'Rouge', 'Bleu'])->random(),
                        'current_mileage' => rand(5000, 80000),
                        'status' => 'available',
                        'daily_rate' => collect([250, 300, 350, 400, 500, 600])->random(),
                        'deposit_amount' => collect([2000, 3000, 5000])->random(),
                        'has_gps' => (bool) rand(0, 1),
                        'has_air_conditioning' => true,
                        'has_bluetooth' => (bool) rand(0, 1),
                        'has_baby_seat' => false,
                        'has_camera_recul' => (bool) rand(0, 1),
                        'has_regulateur_vitesse' => (bool) rand(0, 1),
                        'has_siege_chauffant' => false,
                        'fuel_policy' => 'full_to_full',
                    ],
                );
            }
        }

        $this->command?->info('Created ' . count($vehicles) . ' vehicles with brands & models.');

        /*
        |--------------------------------------------------------------------------
        | Clients
        |--------------------------------------------------------------------------
        */
        $clientsData = [
            ['first_name' => 'Ahmed', 'last_name' => 'Bennani', 'email' => 'ahmed.bennani@example.com', 'phone' => '0661234567', 'city' => 'Casablanca', 'cin_number' => 'BK123456'],
            ['first_name' => 'Fatima', 'last_name' => 'Zahra', 'email' => 'fatima.zahra@example.com', 'phone' => '0672345678', 'city' => 'Rabat', 'cin_number' => 'R234567'],
            ['first_name' => 'Mohamed', 'last_name' => 'El Amrani', 'email' => 'mohamed.elamrani@example.com', 'phone' => '0653456789', 'city' => 'Marrakech', 'cin_number' => 'M345678'],
            ['first_name' => 'Sara', 'last_name' => 'Idrissi', 'email' => 'sara.idrissi@example.com', 'phone' => '0694567890', 'city' => 'Tanger', 'cin_number' => 'T456789'],
            ['first_name' => 'Youssef', 'last_name' => 'Alaoui', 'email' => 'youssef.alaoui@example.com', 'phone' => '0665678901', 'city' => 'Fès', 'cin_number' => 'F567890'],
        ];

        $clients = [];
        foreach ($clientsData as $cd) {
            $clients[] = Client::firstOrCreate(
                ['agency_id' => $agencyId, 'cin_number' => $cd['cin_number']],
                array_merge($cd, [
                    'agency_id' => $agencyId,
                    'country' => 'Maroc',
                    'nationality' => 'Marocaine',
                    'address' => 'Rue ' . rand(1, 100) . ', ' . $cd['city'],
                    'birth_date' => Carbon::now()->subYears(rand(25, 50))->format('Y-m-d'),
                    'cin_valid_until' => Carbon::now()->addYears(rand(2, 8))->format('Y-m-d'),
                    'driving_license_number' => 'DL-' . strtoupper(substr($cd['last_name'], 0, 3)) . rand(1000, 9999),
                    'driving_license_issue_date' => Carbon::now()->subYears(rand(3, 15))->format('Y-m-d'),
                    'status' => 'active',
                    'rating_average' => rand(30, 50) / 10,
                    'rating_count' => rand(1, 20),
                ]),
            );
        }

        $this->command?->info('Created ' . count($clients) . ' clients.');

        /*
        |--------------------------------------------------------------------------
        | Rental Contracts
        |--------------------------------------------------------------------------
        */
        $contractsConfig = [
            // Completed contracts
            ['vehicle_idx' => 0, 'client_idx' => 0, 'days_ago_start' => 30, 'days' => 5, 'status' => 'completed', 'acceptance_status' => 'accepted'],
            ['vehicle_idx' => 1, 'client_idx' => 1, 'days_ago_start' => 25, 'days' => 3, 'status' => 'completed', 'acceptance_status' => 'accepted'],
            ['vehicle_idx' => 2, 'client_idx' => 2, 'days_ago_start' => 20, 'days' => 7, 'status' => 'completed', 'acceptance_status' => 'accepted'],
            // Active (in_progress) contracts
            ['vehicle_idx' => 3, 'client_idx' => 3, 'days_ago_start' => 2, 'days' => 5, 'status' => 'in_progress', 'acceptance_status' => 'accepted'],
            ['vehicle_idx' => 4, 'client_idx' => 4, 'days_ago_start' => 1, 'days' => 3, 'status' => 'in_progress', 'acceptance_status' => 'accepted'],
            // Pending contract
            ['vehicle_idx' => 5, 'client_idx' => 0, 'days_ago_start' => -2, 'days' => 4, 'status' => 'pending', 'acceptance_status' => 'pending'],
            // Cancelled contract
            ['vehicle_idx' => 6, 'client_idx' => 1, 'days_ago_start' => 15, 'days' => 3, 'status' => 'cancelled', 'acceptance_status' => 'rejected'],
        ];

        $contracts = [];
        $contractNum = 1;

        foreach ($contractsConfig as $cc) {
            $vehicle = $vehicles[$cc['vehicle_idx']];
            $client = $clients[$cc['client_idx']];
            $startDate = Carbon::now()->subDays($cc['days_ago_start']);
            $endDate = (clone $startDate)->addDays($cc['days'] - 1);
            $plannedDays = $cc['days'];
            $dailyRate = $vehicle->daily_rate;
            $discountAmount = $contractNum % 3 === 0 ? 100 : 0;
            $totalAmount = ($dailyRate * $plannedDays) - $discountAmount;

            $contractNumber = 'CTR-' . $startDate->format('Ym') . '-' . str_pad($contractNum, 4, '0', STR_PAD_LEFT);

            $contract = RentalContract::firstOrCreate(
                ['agency_id' => $agencyId, 'contract_number' => $contractNumber],
                [
                    'agency_id' => $agencyId,
                    'contract_number' => $contractNumber,
                    'vehicle_id' => $vehicle->id,
                    'start_date' => $startDate->format('Y-m-d'),
                    'start_time' => '09:00',
                    'end_date' => $endDate->format('Y-m-d'),
                    'end_time' => '18:00',
                    'start_at' => $startDate->setTime(9, 0),
                    'end_at' => $endDate->setTime(18, 0),
                    'pickup_location' => 'Agence ' . $vehicle->registration_city,
                    'dropoff_location' => 'Agence ' . $vehicle->registration_city,
                    'planned_days' => $plannedDays,
                    'daily_rate' => $dailyRate,
                    'discount_amount' => $discountAmount,
                    'total_amount' => $totalAmount,
                    'deposit_amount' => $vehicle->deposit_amount,
                    'status' => $cc['status'],
                    'acceptance_status' => $cc['acceptance_status'],
                    'source' => 'backoffice',
                    'created_by' => $user?->id,
                    'updated_by' => $user?->id,
                ],
            );

            // Add primary client to pivot table
            if (!$contract->clients()->where('client_id', $client->id)->exists()) {
                $contract->clients()->attach($client->id, ['role' => 'primary', 'order' => 1]);
            }

            $contracts[] = $contract;
            $contractNum++;
        }

        $this->command?->info('Created ' . count($contracts) . ' rental contracts.');

        /*
        |--------------------------------------------------------------------------
        | Invoices & Invoice Items
        |--------------------------------------------------------------------------
        */
        $vatRate = 20;
        $invoiceNum = 1;

        // Create invoices for completed and active contracts
        foreach ($contracts as $contract) {
            if (!in_array($contract->status, ['completed', 'in_progress'])) {
                continue;
            }

            $invoiceNumber = 'INV-' . Carbon::parse($contract->start_date)->format('Ym') . '-' . str_pad($invoiceNum, 4, '0', STR_PAD_LEFT);

            $totalTtc = $contract->total_amount;
            $totalHt = round($totalTtc / (1 + $vatRate / 100), 2);
            $totalVat = round($totalTtc - $totalHt, 2);

            $status = $contract->status === 'completed' ? 'paid' : 'sent';

            $invoice = Invoice::firstOrCreate(
                ['agency_id' => $agencyId, 'invoice_number' => $invoiceNumber],
                [
                    'agency_id' => $agencyId,
                    'invoice_number' => $invoiceNumber,
                    'invoice_date' => $contract->start_date,
                    'rental_contract_id' => $contract->id,
                    'client_id' => $contract->clients()->wherePivot('role', 'primary')->first()?->id,
                    'vat_rate' => $vatRate,
                    'total_ht' => $totalHt,
                    'total_vat' => $totalVat,
                    'total_ttc' => $totalTtc,
                    'status' => $status,
                    'currency' => 'MAD',
                    'notes' => 'Facture générée automatiquement par le seeder.',
                ],
            );

            // Invoice Item: rental line
            InvoiceItem::firstOrCreate(
                ['invoice_id' => $invoice->id, 'description' => 'Location véhicule - ' . $contract->planned_days . ' jours'],
                [
                    'invoice_id' => $invoice->id,
                    'description' => 'Location véhicule - ' . $contract->planned_days . ' jours',
                    'days_count' => $contract->planned_days,
                    'unit_price_ttc' => $contract->daily_rate,
                    'quantity' => $contract->planned_days,
                    'total_ttc' => $contract->daily_rate * $contract->planned_days,
                    'total_ht' => round(($contract->daily_rate * $contract->planned_days) / (1 + $vatRate / 100), 2),
                    'vat_rate' => $vatRate,
                ],
            );

            // Add discount line if applicable
            if ($contract->discount_amount > 0) {
                InvoiceItem::firstOrCreate(
                    ['invoice_id' => $invoice->id, 'description' => 'Remise'],
                    [
                        'invoice_id' => $invoice->id,
                        'description' => 'Remise',
                        'days_count' => null,
                        'unit_price_ttc' => -$contract->discount_amount,
                        'quantity' => 1,
                        'total_ttc' => -$contract->discount_amount,
                        'total_ht' => round(-$contract->discount_amount / (1 + $vatRate / 100), 2),
                        'vat_rate' => $vatRate,
                    ],
                );
            }

            $invoiceNum++;
        }

        $this->command?->info('Created invoices with items for completed/active contracts.');
        $this->command?->info('✅ Contract & Invoice seeder completed successfully!');
    }

    private function fakeRegistration(string $brand, string $model): string
    {
        $num = rand(10000, 99999);
        $letter = chr(rand(65, 90));
        $city = rand(1, 80);
        return $num . '-' . $letter . '-' . $city;
    }
}
