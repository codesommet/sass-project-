<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Contrat #{{ $contract->contract_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #1a1a1a;
            background: #fff;
        }
        .page { padding: 20px 30px; }

        /* ── Outer border ── */
        .outer-border {
            border: 2px solid #2c3e50;
            padding: 0;
        }

        /* ── Title ── */
        .doc-title {
            text-align: center;
            font-size: 16px;
            font-weight: 700;
            padding: 12px 10px;
            border-bottom: 2px solid #2c3e50;
            letter-spacing: 1px;
        }

        /* ── Agency header ── */
        .agency-header {
            border-bottom: 2px solid #2c3e50;
        }
        .agency-header-table { width: 100%; border-collapse: collapse; }
        .agency-header-table td { padding: 12px 15px; vertical-align: middle; }
        .agency-logo { width: 100px; text-align: center; border-right: 1px solid #ccc; }
        .agency-logo img { max-height: 60px; max-width: 80px; }
        .agency-info {
            text-align: center;
            font-size: 11px;
            line-height: 1.6;
        }
        .agency-name { font-size: 14px; font-weight: 700; }

        /* ── Section header ── */
        .section-title {
            background: #ecf0f1;
            font-weight: 700;
            font-size: 12px;
            padding: 6px 10px;
            border-bottom: 1px solid #2c3e50;
            border-top: 1px solid #2c3e50;
        }

        /* ── Data table ── */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table td {
            padding: 5px 10px;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
            vertical-align: top;
        }
        .data-table td:first-child { width: 50%; }
        .data-table tr:last-child td { border-bottom: none; }
        .data-label { color: #555; }
        .data-value { color: #1a1a1a; }

        /* ── Bottom sections ── */
        .bottom-section { border-top: 2px solid #2c3e50; }
        .bottom-table { width: 100%; border-collapse: collapse; }
        .bottom-table > tbody > tr > td {
            vertical-align: top;
            padding: 10px 12px;
            width: 50%;
            font-size: 10px;
        }
        .bottom-table > tbody > tr > td:first-child {
            border-right: 1px solid #2c3e50;
        }

        .legal-title { font-weight: 700; font-size: 11px; margin-bottom: 8px; }
        .legal-list { padding-left: 15px; line-height: 1.6; }
        .legal-list li { margin-bottom: 4px; }

        .insurance-title { font-weight: 700; font-size: 12px; }
        .insurance-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .insurance-table td {
            border: 1px solid #bbb;
            padding: 5px 8px;
            font-size: 11px;
        }
        .insurance-table .label-cell { font-weight: 700; background: #f5f5f5; }

        .checkbox { display: inline-block; width: 12px; height: 12px; border: 1px solid #333; margin-right: 3px; vertical-align: middle; text-align: center; font-size: 9px; line-height: 12px; }

        /* ── Signature section ── */
        .sig-section { border-top: 2px solid #2c3e50; padding: 15px; }
        .sig-table { width: 100%; border-collapse: collapse; }
        .sig-table td { width: 50%; text-align: center; vertical-align: bottom; padding: 10px 20px; }
        .sig-label { font-size: 10px; color: #555; font-weight: 600; margin-bottom: 5px; }
        .sig-line { border-top: 1px solid #999; margin-top: 50px; padding-top: 5px; font-size: 10px; color: #666; }
        .sig-img { max-width: 120px; max-height: 50px; margin-bottom: 5px; }

        /* ── Footer ── */
        .footer {
            border-top: 1px solid #ccc;
            padding: 8px 10px;
            text-align: center;
            font-size: 8px;
            color: #999;
        }
    </style>
</head>
<body>
<div class="page">
    <div class="outer-border">

        <!-- Title -->
        <div class="doc-title">
            CONTRAT DE LOCATION DE VEHICULE ({{ $contract->contract_number }})
        </div>

        <!-- Agency Header -->
        <div class="agency-header">
            <table class="agency-header-table"><tr>
                <td class="agency-logo">
                    @if(isset($logo) && $logo)
                        <img src="{{ $logo }}" alt="Logo">
                    @endif
                </td>
                <td class="agency-info">
                    <div class="agency-name">{{ $agency->name ?? 'Agence' }}</div>
                    @if($agency->address ?? false)
                        Adresse: {{ $agency->address }}<br>
                    @endif
                    @if($agency->phone ?? false)
                        Tel: {{ $agency->phone }}
                    @endif
                    @if($agency->email ?? false)
                        | Email: {{ $agency->email }}
                    @endif
                    @php
                        $website = $agency->settings['website'] ?? null;
                    @endphp
                    @if($website)
                        | Site web: {{ $website }}
                    @endif
                </td>
            </tr></table>
        </div>

        <!-- VEHICULE Section -->
        <div class="section-title">VEHICULE</div>
        <table class="data-table">
            <tr>
                <td>
                    <span class="data-label">Marque:</span>
                    <span class="data-value">
                        @if(isset($vehicle) && $vehicle)
                            @php
                                $brandName = $vehicle->model->brand->name ?? '';
                                $modelName = $vehicle->model->name ?? '';
                            @endphp
                            {{ trim($brandName . ' - ' . $modelName) }}
                        @else
                            N/A
                        @endif
                    </span>
                </td>
                <td>
                    <span class="data-label">Immatriculation:</span>
                    <span class="data-value">{{ $vehicle->registration_number ?? 'N/A' }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="data-label">État technique:</span>
                    <span class="data-value">{{ $contract->observations ?? '' }}</span>
                </td>
                <td>
                    <span class="data-label">N° fiche contrôle:</span>
                    <span class="data-value">{{ $contract->control_number ?? '' }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="data-label">Lieu de livraison:</span>
                    <span class="data-value">{{ $contract->pickup_location ?? 'Agence' }}</span>
                </td>
                <td>
                    <span class="data-label">Lieu de reprise:</span>
                    <span class="data-value">{{ $contract->dropoff_location ?? 'Agence' }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="data-label">Date et heure de départ:</span>
                    <span class="data-value">{{ \Carbon\Carbon::parse($contract->start_date)->format('d/m/Y') }} {{ $contract->start_time ?? '' }}</span>
                </td>
                <td>
                    <span class="data-label">Date et heure de retour:</span>
                    <span class="data-value">{{ \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') }} {{ $contract->end_time ?? '' }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="data-label">Durée de location:</span>
                    <span class="data-value">{{ $contract->planned_days ?? $contract->duration_days ?? 'N/A' }} jours</span>
                </td>
                <td>
                    <span class="data-label">Prolongation:</span>
                    <span class="data-value"> jours</span>
                </td>
            </tr>
        </table>

        <!-- LOCATAIRE Section -->
        <div class="section-title">LOCATAIRE</div>
        <table class="data-table">
            @if(isset($client) && $client)
            <tr>
                <td>
                    <span class="data-label">Nom:</span>
                    <span class="data-value">{{ $client->last_name ?? '' }}</span>
                </td>
                <td>
                    <span class="data-label">Prénom:</span>
                    <span class="data-value">{{ $client->first_name ?? '' }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="data-label">Né le:</span>
                    <span class="data-value">{{ $client->birth_date ? \Carbon\Carbon::parse($client->birth_date)->format('Y-m-d') : '' }}</span>
                </td>
                <td>
                    <span class="data-label">Adresse:</span>
                    <span class="data-value">{{ $client->address ?? '' }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="data-label">Permis conduire N°:</span>
                    <span class="data-value">{{ $client->driving_license_number ?? '' }}</span>
                </td>
                <td>
                    <span class="data-label">Délivré le:</span>
                    <span class="data-value">{{ $client->driving_license_issue_date ? \Carbon\Carbon::parse($client->driving_license_issue_date)->format('Y-m-d') : '' }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="data-label">Pasport N°:</span>
                    <span class="data-value">{{ $client->passport_number ?? '' }}</span>
                </td>
                <td>
                    <span class="data-label">Délivré le:</span>
                    <span class="data-value">{{ $client->passport_issue_date ? \Carbon\Carbon::parse($client->passport_issue_date)->format('Y-m-d') : '' }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="data-label">CIN N°:</span>
                    <span class="data-value">{{ $client->cin_number ?? '' }}</span>
                </td>
                <td>
                    <span class="data-label">Valable jusqu'au:</span>
                    <span class="data-value">{{ $client->cin_valid_until ? \Carbon\Carbon::parse($client->cin_valid_until)->format('Y-m-d') : '' }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="data-label">Nationalité:</span>
                    <span class="data-value">{{ $client->nationality ?? '' }}</span>
                </td>
                <td>
                    <span class="data-label">Téléphone:</span>
                    <span class="data-value">{{ $client->phone ?? '' }}</span>
                </td>
            </tr>
            @else
            <tr>
                <td colspan="2"><span class="data-value">Client non spécifié</span></td>
            </tr>
            @endif
        </table>

        <!-- 2ème CONDUCTEUR Section -->
        <div class="section-title">2ème CONDUCTEUR</div>
        <table class="data-table">
            <tr>
                <td>
                    <span class="data-label">Nom:</span>
                    <span class="data-value">{{ $secondaryClient->last_name ?? '' }}</span>
                </td>
                <td>
                    <span class="data-label">Prénom:</span>
                    <span class="data-value">{{ $secondaryClient->first_name ?? '' }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="data-label">Né le:</span>
                    <span class="data-value">{{ isset($secondaryClient) && $secondaryClient->birth_date ? \Carbon\Carbon::parse($secondaryClient->birth_date)->format('Y-m-d') : '' }}</span>
                </td>
                <td>
                    <span class="data-label">Adresse:</span>
                    <span class="data-value">{{ $secondaryClient->address ?? '' }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="data-label">Permis conduire N°:</span>
                    <span class="data-value">{{ $secondaryClient->driving_license_number ?? '' }}</span>
                </td>
                <td>
                    <span class="data-label">Délivré le:</span>
                    <span class="data-value">{{ isset($secondaryClient) && $secondaryClient->driving_license_issue_date ? \Carbon\Carbon::parse($secondaryClient->driving_license_issue_date)->format('Y-m-d') : '' }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="data-label">Pasport N°:</span>
                    <span class="data-value">{{ $secondaryClient->passport_number ?? '' }}</span>
                </td>
                <td>
                    <span class="data-label">Délivré le:</span>
                    <span class="data-value">{{ isset($secondaryClient) && $secondaryClient->passport_issue_date ? \Carbon\Carbon::parse($secondaryClient->passport_issue_date)->format('Y-m-d') : '' }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="data-label">CIN N°:</span>
                    <span class="data-value">{{ $secondaryClient->cin_number ?? '' }}</span>
                </td>
                <td>
                    <span class="data-label">Valable jusqu'au:</span>
                    <span class="data-value">{{ isset($secondaryClient) && $secondaryClient->cin_valid_until ? \Carbon\Carbon::parse($secondaryClient->cin_valid_until)->format('Y-m-d') : '' }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="data-label">Nationalité:</span>
                    <span class="data-value">{{ $secondaryClient->nationality ?? '' }}</span>
                </td>
                <td>
                    <span class="data-label">Téléphone:</span>
                    <span class="data-value">{{ $secondaryClient->phone ?? '' }}</span>
                </td>
            </tr>
        </table>

        <!-- Bottom: Legal + Insurance -->
        <div class="bottom-section">
            <table class="bottom-table"><tr>
                <td>
                    <div class="legal-title">LE LOCATAIRE:</div>
                    <ul class="legal-list">
                        <li>Je déclare en tout connaissance de cause que si le dite contrat subit des dégâts matériels, tous engendrés sont à ma charge.</li>
                        <li>J'ai lu et j'accepte les conditions stipulées ci contre et au verso de ce contrat.</li>
                        <li>Déclare avoir pris connaissance des clauses et conditions de location en vigueur stipulées au verso de ce contrat ainsi que la facturation.</li>
                    </ul>
                </td>
                <td>
                    <div style="margin-bottom: 8px;">
                        <span class="insurance-title">ASSURANCE:</span>
                        &nbsp;&nbsp;&nbsp;
                        <span>Franchise:</span>
                        <span class="checkbox"></span> Avec
                        &nbsp;
                        <span class="checkbox"></span> Sans
                    </div>
                    <table class="insurance-table">
                        <tr>
                            <td class="label-cell">FRANCHISE:</td>
                            <td>Dhs</td>
                            <td>Taux %</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Caution:</td>
                            <td colspan="2">{{ ($contract->deposit_amount ?? 0) > 0 ? number_format($contract->deposit_amount, 2, ',', ' ') . ' Dhs' : '' }}</td>
                        </tr>
                    </table>

                    <!-- Financial summary -->
                    <table class="insurance-table" style="margin-top: 10px;">
                        <tr>
                            <td class="label-cell">Tarif / Jour:</td>
                            <td colspan="2">{{ number_format($contract->daily_rate ?? 0, 2, ',', ' ') }} Dhs</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Nb Jours:</td>
                            <td colspan="2">{{ $contract->planned_days ?? $contract->duration_days ?? '' }}</td>
                        </tr>
                        @if(($contract->discount_amount ?? 0) > 0)
                        <tr>
                            <td class="label-cell">Remise:</td>
                            <td colspan="2">{{ number_format($contract->discount_amount, 2, ',', ' ') }} Dhs</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="label-cell" style="font-size: 12px;">TOTAL:</td>
                            <td colspan="2" style="font-weight: 700; font-size: 12px;">{{ number_format($contract->total_amount ?? 0, 2, ',', ' ') }} Dhs</td>
                        </tr>
                    </table>
                </td>
            </tr></table>
        </div>

        <!-- Signatures -->
        <div class="sig-section">
            <table class="sig-table"><tr>
                <td>
                    <div class="sig-label">Signature du locataire</div>
                    <div class="sig-line">
                        {{ $client->first_name ?? '' }} {{ $client->last_name ?? '' }}
                    </div>
                </td>
                <td>
                    @if(isset($signature) && $signature)
                        <img src="{{ $signature }}" alt="Cachet" class="sig-img"><br>
                    @endif
                    <div class="sig-label">Cachet & signature de l'agence</div>
                    <div class="sig-line">
                        {{ $agency->name ?? '' }}
                    </div>
                </td>
            </tr></table>
        </div>

        <!-- Footer -->
        <div class="footer">
            Document généré le {{ $generated_at }} par {{ $generated_by }} &bull; {{ $agency->name ?? config('app.name') }}
        </div>

    </div>
</div>
</body>
</html>
