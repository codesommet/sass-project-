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
        .page { padding: 20px 25px; }

        /* ── Accent top ── */
        .accent-top { height: 5px; background: linear-gradient(90deg, #0f766e, #14b8a6, #5eead4); margin-bottom: 0; }

        /* ── Header ── */
        .header { padding: 16px 20px; border-bottom: 2px solid #0f766e; margin-bottom: 14px; }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { vertical-align: middle; }
        .header-logo { width: 80px; }
        .header-logo img { max-height: 55px; max-width: 70px; }
        .header-info { padding-left: 12px; }
        .header-agency-name { font-size: 16px; font-weight: 700; color: #0f766e; }
        .header-agency-details { font-size: 9px; color: #64748b; line-height: 1.5; margin-top: 2px; }
        .header-contract { text-align: right; }
        .header-contract-label { font-size: 22px; font-weight: 700; color: #0f766e; letter-spacing: 2px; }
        .header-contract-no { font-size: 11px; color: #64748b; margin-top: 1px; }

        /* ── Info chips row ── */
        .chips-row {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .chips-row td {
            background: #f0fdfa;
            border: 1px solid #99f6e4;
            padding: 8px 10px;
            text-align: center;
            vertical-align: top;
        }
        .chip-label { font-size: 8px; text-transform: uppercase; letter-spacing: 1.2px; color: #0f766e; font-weight: 700; }
        .chip-value { font-size: 11px; font-weight: 700; color: #1e293b; margin-top: 2px; }

        /* ── Section header ── */
        .section-header {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        .section-header td {
            padding: 7px 14px;
            background: #0f766e;
            color: #fff;
            font-weight: 700;
            font-size: 11px;
            letter-spacing: 0.8px;
        }
        .section-header .sh-right {
            text-align: right;
            font-weight: 400;
            font-size: 9px;
            opacity: 0.8;
        }

        /* ── Striped data table ── */
        .striped { width: 100%; border-collapse: collapse; border: 1px solid #e2e8f0; border-top: none; margin-bottom: 12px; }
        .striped td {
            padding: 5px 14px;
            font-size: 11px;
            border-bottom: 1px solid #f0fdfa;
            vertical-align: top;
        }
        .striped tr:nth-child(odd) td { background: #fafffe; }
        .striped tr:nth-child(even) td { background: #f0fdfa; }
        .striped tr:last-child td { border-bottom: none; }
        .sl { color: #0f766e; font-weight: 600; width: 22%; font-size: 10px; }
        .sv { color: #1e293b; width: 28%; }

        /* ── Vehicle card ── */
        .vehicle-card {
            border: 2px solid #0f766e;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 12px;
        }
        .vehicle-card-header {
            background: #0f766e;
            color: #fff;
            padding: 7px 14px;
            font-weight: 700;
            font-size: 11px;
            letter-spacing: 0.8px;
        }
        .vehicle-card-body { padding: 0; }
        .vehicle-card-body table { width: 100%; border-collapse: collapse; }
        .vehicle-card-body td {
            padding: 6px 14px;
            font-size: 11px;
            border-bottom: 1px solid #ecfdf5;
            width: 50%;
        }
        .vehicle-card-body tr:last-child td { border-bottom: none; }
        .vehicle-card-body tr:nth-child(even) td { background: #f0fdfa; }
        .vl { color: #0f766e; font-weight: 600; font-size: 10px; }

        /* ── Bottom area ── */
        .bottom-area { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .bottom-area > tbody > tr > td { vertical-align: top; width: 50%; }
        .bottom-area > tbody > tr > td:first-child { padding-right: 6px; }
        .bottom-area > tbody > tr > td:last-child { padding-left: 6px; }

        /* ── Legal section ── */
        .legal-section {
            border: 1px solid #99f6e4;
            border-radius: 8px;
            overflow: hidden;
        }
        .legal-section-title {
            background: #f0fdfa;
            padding: 6px 12px;
            font-weight: 700;
            font-size: 11px;
            color: #0f766e;
            border-bottom: 1px solid #99f6e4;
        }
        .legal-section-body {
            padding: 8px 12px;
            font-size: 10px;
            line-height: 1.6;
        }
        .legal-section-body ul { padding-left: 14px; }
        .legal-section-body li { margin-bottom: 3px; }

        .assurance-line {
            margin-top: 8px;
            padding-top: 6px;
            border-top: 1px dashed #99f6e4;
            font-size: 10px;
        }
        .checkbox { display: inline-block; width: 11px; height: 11px; border: 1px solid #0f766e; margin-right: 2px; vertical-align: middle; }

        /* ── Finance card ── */
        .finance-card {
            border: 2px solid #0f766e;
            border-radius: 8px;
            overflow: hidden;
        }
        .finance-card-title {
            background: #0f766e;
            color: #fff;
            padding: 6px 12px;
            font-weight: 700;
            font-size: 10px;
            letter-spacing: 0.5px;
        }
        .ftable { width: 100%; border-collapse: collapse; }
        .ftable td { padding: 5px 12px; font-size: 11px; border-bottom: 1px solid #ecfdf5; }
        .ftable tr:last-child td { border-bottom: none; }
        .ftable tr:nth-child(even) td { background: #f0fdfa; }
        .ftable .fl { color: #475569; font-weight: 600; }
        .ftable .fv { text-align: right; color: #1e293b; font-weight: 600; }
        .ftotal td {
            background: #0f766e !important;
            color: #fff !important;
            font-weight: 700;
            font-size: 13px;
            padding: 8px 12px;
        }

        /* ── Signatures ── */
        .sig-area { width: 100%; margin-top: 25px; border-collapse: collapse; }
        .sig-area td { width: 50%; text-align: center; vertical-align: bottom; padding: 8px 20px; }
        .sig-img { max-width: 110px; max-height: 45px; margin-bottom: 4px; }
        .sig-underline { border-top: 1px solid #86c7bc; margin-top: 45px; padding-top: 5px; font-size: 10px; color: #64748b; display: inline-block; width: 170px; }

        /* ── Footer ── */
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
        }
        .footer-accent { display: inline-block; width: 40px; height: 3px; background: linear-gradient(90deg, #0f766e, #14b8a6); border-radius: 2px; margin-bottom: 5px; }
    </style>
</head>
<body>
<div class="page">
    <div class="accent-top"></div>

    <!-- Header -->
    <div class="header">
        <table class="header-table"><tr>
            <td class="header-logo">
                @if(isset($logo) && $logo)
                    <img src="{{ $logo }}" alt="Logo">
                @else
                    <div style="width:50px; height:50px; background:#0f766e; border-radius:8px; color:#fff; text-align:center; line-height:50px; font-weight:800; font-size:16px;">{{ strtoupper(substr($agency->name ?? 'A', 0, 2)) }}</div>
                @endif
            </td>
            <td class="header-info">
                <div class="header-agency-name">{{ $agency->name ?? 'Agence' }}</div>
                <div class="header-agency-details">
                    @if($agency->address ?? false) {{ $agency->address }} @endif
                    @if($agency->phone ?? false) &bull; Tel: {{ $agency->phone }} @endif
                    @if($agency->email ?? false) &bull; {{ $agency->email }} @endif
                    @php $website = $agency->settings['website'] ?? null; @endphp
                    @if($website) &bull; {{ $website }} @endif
                </div>
            </td>
            <td class="header-contract">
                <div class="header-contract-label">CONTRAT</div>
                <div class="header-contract-no">{{ $contract->contract_number }}</div>
            </td>
        </tr></table>
    </div>

    <!-- Date/Duration Chips -->
    <table class="chips-row"><tr>
        <td>
            <div class="chip-label">Départ</div>
            <div class="chip-value">{{ \Carbon\Carbon::parse($contract->start_date)->format('d/m/Y') }} {{ $contract->start_time ?? '' }}</div>
        </td>
        <td>
            <div class="chip-label">Retour</div>
            <div class="chip-value">{{ \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') }} {{ $contract->end_time ?? '' }}</div>
        </td>
        <td>
            <div class="chip-label">Durée</div>
            <div class="chip-value">{{ $contract->planned_days ?? $contract->duration_days ?? 'N/A' }} jours</div>
        </td>
        <td>
            <div class="chip-label">Livraison</div>
            <div class="chip-value">{{ $contract->pickup_location ?? 'Agence' }}</div>
        </td>
        <td>
            <div class="chip-label">Reprise</div>
            <div class="chip-value">{{ $contract->dropoff_location ?? 'Agence' }}</div>
        </td>
    </tr></table>

    <!-- Vehicle Card -->
    <div class="vehicle-card">
        <div class="vehicle-card-header">VEHICULE</div>
        <div class="vehicle-card-body">
            <table>
                <tr>
                    <td>
                        <span class="vl">Marque:</span>
                        @if(isset($vehicle) && $vehicle)
                            @php
                                $brandName = $vehicle->model->brand->name ?? '';
                                $modelName = $vehicle->model->name ?? '';
                            @endphp
                            {{ trim($brandName . ' - ' . $modelName) }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        <span class="vl">Immatriculation:</span>
                        {{ $vehicle->registration_number ?? 'N/A' }}
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="vl">État technique:</span>
                        {{ $contract->observations ?? '—' }}
                    </td>
                    <td>
                        <span class="vl">N° fiche contrôle:</span>
                        {{ $contract->control_number ?? '—' }}
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- LOCATAIRE -->
    <table class="section-header"><tr>
        <td>LOCATAIRE (Conducteur principal)</td>
        <td class="sh-right">Informations d'identité</td>
    </tr></table>
    <table class="striped">
        @if(isset($client) && $client)
        <tr>
            <td class="sl">Nom</td>
            <td class="sv">{{ $client->last_name ?? '' }}</td>
            <td class="sl">Prénom</td>
            <td class="sv">{{ $client->first_name ?? '' }}</td>
        </tr>
        <tr>
            <td class="sl">Né le</td>
            <td class="sv">{{ $client->birth_date ? \Carbon\Carbon::parse($client->birth_date)->format('d/m/Y') : '' }}</td>
            <td class="sl">Adresse</td>
            <td class="sv">{{ $client->address ?? '' }}</td>
        </tr>
        <tr>
            <td class="sl">Permis N°</td>
            <td class="sv">{{ $client->driving_license_number ?? '' }}</td>
            <td class="sl">Délivré le</td>
            <td class="sv">{{ $client->driving_license_issue_date ? \Carbon\Carbon::parse($client->driving_license_issue_date)->format('d/m/Y') : '' }}</td>
        </tr>
        <tr>
            <td class="sl">Pasport N°</td>
            <td class="sv">{{ $client->passport_number ?? '' }}</td>
            <td class="sl">Délivré le</td>
            <td class="sv">{{ $client->passport_issue_date ? \Carbon\Carbon::parse($client->passport_issue_date)->format('d/m/Y') : '' }}</td>
        </tr>
        <tr>
            <td class="sl">CIN N°</td>
            <td class="sv">{{ $client->cin_number ?? '' }}</td>
            <td class="sl">Valable jusqu'au</td>
            <td class="sv">{{ $client->cin_valid_until ? \Carbon\Carbon::parse($client->cin_valid_until)->format('d/m/Y') : '' }}</td>
        </tr>
        <tr>
            <td class="sl">Nationalité</td>
            <td class="sv">{{ $client->nationality ?? '' }}</td>
            <td class="sl">Téléphone</td>
            <td class="sv">{{ $client->phone ?? '' }}</td>
        </tr>
        @else
        <tr><td colspan="4" class="sv">Client non spécifié</td></tr>
        @endif
    </table>

    <!-- 2ème CONDUCTEUR -->
    <table class="section-header"><tr>
        <td>2ème CONDUCTEUR</td>
        <td class="sh-right">Conducteur supplémentaire</td>
    </tr></table>
    <table class="striped">
        <tr>
            <td class="sl">Nom</td>
            <td class="sv">{{ $secondaryClient->last_name ?? '' }}</td>
            <td class="sl">Prénom</td>
            <td class="sv">{{ $secondaryClient->first_name ?? '' }}</td>
        </tr>
        <tr>
            <td class="sl">Né le</td>
            <td class="sv">{{ isset($secondaryClient) && $secondaryClient->birth_date ? \Carbon\Carbon::parse($secondaryClient->birth_date)->format('d/m/Y') : '' }}</td>
            <td class="sl">Adresse</td>
            <td class="sv">{{ $secondaryClient->address ?? '' }}</td>
        </tr>
        <tr>
            <td class="sl">Permis N°</td>
            <td class="sv">{{ $secondaryClient->driving_license_number ?? '' }}</td>
            <td class="sl">Délivré le</td>
            <td class="sv">{{ isset($secondaryClient) && $secondaryClient->driving_license_issue_date ? \Carbon\Carbon::parse($secondaryClient->driving_license_issue_date)->format('d/m/Y') : '' }}</td>
        </tr>
        <tr>
            <td class="sl">Pasport N°</td>
            <td class="sv">{{ $secondaryClient->passport_number ?? '' }}</td>
            <td class="sl">Délivré le</td>
            <td class="sv">{{ isset($secondaryClient) && $secondaryClient->passport_issue_date ? \Carbon\Carbon::parse($secondaryClient->passport_issue_date)->format('d/m/Y') : '' }}</td>
        </tr>
        <tr>
            <td class="sl">CIN N°</td>
            <td class="sv">{{ $secondaryClient->cin_number ?? '' }}</td>
            <td class="sl">Valable jusqu'au</td>
            <td class="sv">{{ isset($secondaryClient) && $secondaryClient->cin_valid_until ? \Carbon\Carbon::parse($secondaryClient->cin_valid_until)->format('d/m/Y') : '' }}</td>
        </tr>
        <tr>
            <td class="sl">Nationalité</td>
            <td class="sv">{{ $secondaryClient->nationality ?? '' }}</td>
            <td class="sl">Téléphone</td>
            <td class="sv">{{ $secondaryClient->phone ?? '' }}</td>
        </tr>
    </table>

    <!-- Bottom: Legal + Finance -->
    <table class="bottom-area"><tr>
        <td>
            <div class="legal-section">
                <div class="legal-section-title">LE LOCATAIRE:</div>
                <div class="legal-section-body">
                    <ul>
                        <li>Je déclare en tout connaissance de cause que si le dite contrat subit des dégâts matériels, tous engendrés sont à ma charge.</li>
                        <li>J'ai lu et j'accepte les conditions stipulées ci contre et au verso de ce contrat.</li>
                        <li>Déclare avoir pris connaissance des clauses et conditions de location en vigueur stipulées au verso de ce contrat ainsi que la facturation.</li>
                    </ul>
                    <div class="assurance-line">
                        <strong style="color:#0f766e;">ASSURANCE:</strong>
                        &nbsp;Franchise:
                        <span class="checkbox"></span> Avec
                        &nbsp;
                        <span class="checkbox"></span> Sans
                    </div>
                </div>
            </div>
        </td>
        <td>
            <div class="finance-card">
                <div class="finance-card-title">DETAILS FINANCIERS</div>
                <table class="ftable">
                    <tr>
                        <td class="fl">Tarif / Jour</td>
                        <td class="fv">{{ number_format($contract->daily_rate ?? 0, 2, ',', ' ') }} Dhs</td>
                    </tr>
                    <tr>
                        <td class="fl">Nombre de jours</td>
                        <td class="fv">{{ $contract->planned_days ?? $contract->duration_days ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="fl">Sous-total</td>
                        <td class="fv">{{ number_format(($contract->daily_rate ?? 0) * ($contract->planned_days ?? 0), 2, ',', ' ') }} Dhs</td>
                    </tr>
                    @if(($contract->discount_amount ?? 0) > 0)
                    <tr>
                        <td class="fl">Remise</td>
                        <td class="fv">- {{ number_format($contract->discount_amount, 2, ',', ' ') }} Dhs</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="fl">Caution</td>
                        <td class="fv">{{ ($contract->deposit_amount ?? 0) > 0 ? number_format($contract->deposit_amount, 2, ',', ' ') . ' Dhs' : '—' }}</td>
                    </tr>
                    <tr>
                        <td class="fl">Franchise</td>
                        <td class="fv">Dhs &nbsp;&nbsp;&nbsp; Taux %</td>
                    </tr>
                    <tr class="ftotal">
                        <td>TOTAL</td>
                        <td style="text-align:right;">{{ number_format($contract->total_amount ?? 0, 2, ',', ' ') }} Dhs</td>
                    </tr>
                </table>
            </div>
        </td>
    </tr></table>

    <!-- Signatures -->
    <table class="sig-area"><tr>
        <td>
            <div class="sig-underline">Signature du locataire</div>
            <div style="font-size:8px; margin-top:3px; color:#94a3b8;">{{ $client->first_name ?? '' }} {{ $client->last_name ?? '' }}</div>
        </td>
        <td>
            @if(isset($signature) && $signature)
                <img src="{{ $signature }}" alt="Cachet" class="sig-img"><br>
            @endif
            <div class="sig-underline">Cachet & signature de l'agence</div>
            <div style="font-size:8px; margin-top:3px; color:#94a3b8;">{{ $agency->name ?? '' }}</div>
        </td>
    </tr></table>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-accent"></div><br>
        Document généré le {{ $generated_at }} par {{ $generated_by }} &bull; {{ $agency->name ?? config('app.name') }}
    </div>

</div>
</body>
</html>
