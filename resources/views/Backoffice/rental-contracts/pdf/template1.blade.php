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

        /* ── Header band ── */
        .header-band {
            background: #1a56db;
            color: #fff;
            padding: 14px 20px;
            margin-bottom: 15px;
        }
        .header-band-table { width: 100%; border-collapse: collapse; }
        .header-band-table td { vertical-align: middle; }
        .header-logo { width: 70px; }
        .header-logo img { max-height: 50px; max-width: 60px; }
        .header-center { text-align: center; }
        .header-title { font-size: 15px; font-weight: 700; letter-spacing: 1.5px; }
        .header-contract-no { font-size: 11px; opacity: 0.85; margin-top: 2px; }
        .header-right { text-align: right; font-size: 9px; line-height: 1.6; opacity: 0.9; }

        /* ── Agency info bar ── */
        .agency-bar {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 14px;
        }
        .agency-bar-table { width: 100%; border-collapse: collapse; }
        .agency-bar-table td { font-size: 10px; color: #1e40af; vertical-align: top; }
        .agency-bar-name { font-weight: 700; font-size: 12px; color: #1a56db; margin-bottom: 2px; }

        /* ── Section with left accent ── */
        .section-accent {
            border-left: 4px solid #1a56db;
            background: #f8faff;
            padding: 6px 12px;
            font-weight: 700;
            font-size: 12px;
            color: #1a56db;
            margin-bottom: 0;
            letter-spacing: 0.5px;
        }

        /* ── Card container ── */
        .card {
            border: 1px solid #dbeafe;
            border-top: none;
            margin-bottom: 12px;
        }

        /* ── Data rows ── */
        .data-row { width: 100%; border-collapse: collapse; }
        .data-row td {
            padding: 4px 12px;
            border-bottom: 1px solid #eff6ff;
            font-size: 11px;
            width: 25%;
        }
        .data-row tr:last-child td { border-bottom: none; }
        .dlabel { color: #64748b; font-size: 10px; }
        .dvalue { color: #1e293b; font-weight: 600; }

        /* ── Vehicle grid ── */
        .vehicle-grid { width: 100%; border-collapse: collapse; }
        .vehicle-grid td {
            padding: 6px 12px;
            width: 50%;
            border-bottom: 1px solid #eff6ff;
            vertical-align: top;
        }
        .vehicle-grid tr:last-child td { border-bottom: none; }

        /* ── Dates bar ── */
        .dates-bar {
            width: 100%;
            border-collapse: collapse;
            background: #1a56db;
            color: #fff;
            margin-bottom: 12px;
        }
        .dates-bar td {
            padding: 8px 14px;
            text-align: center;
            border-right: 1px solid rgba(255,255,255,0.2);
            font-size: 10px;
        }
        .dates-bar td:last-child { border-right: none; }
        .dates-bar .dt-label { font-size: 8px; text-transform: uppercase; letter-spacing: 1px; opacity: 0.8; }
        .dates-bar .dt-value { font-size: 12px; font-weight: 700; margin-top: 2px; }

        /* ── Bottom split ── */
        .bottom-split { width: 100%; border-collapse: collapse; margin-top: 12px; }
        .bottom-split > tbody > tr > td { vertical-align: top; width: 50%; }
        .bottom-split > tbody > tr > td:first-child { padding-right: 8px; }
        .bottom-split > tbody > tr > td:last-child { padding-left: 8px; }

        .legal-box {
            background: #f8faff;
            border: 1px solid #dbeafe;
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 10px;
        }
        .legal-box-title { font-weight: 700; color: #1a56db; margin-bottom: 6px; font-size: 11px; }
        .legal-box ul { padding-left: 14px; line-height: 1.6; }
        .legal-box li { margin-bottom: 3px; }

        .finance-box {
            border: 1px solid #dbeafe;
            border-radius: 6px;
            overflow: hidden;
        }
        .finance-box-title {
            background: #1a56db;
            color: #fff;
            padding: 6px 12px;
            font-weight: 700;
            font-size: 10px;
            letter-spacing: 0.5px;
        }
        .finance-row { width: 100%; border-collapse: collapse; }
        .finance-row td {
            padding: 5px 12px;
            border-bottom: 1px solid #eff6ff;
            font-size: 11px;
        }
        .finance-row tr:last-child td { border-bottom: none; }
        .fl { color: #64748b; font-weight: 600; }
        .fv { text-align: right; color: #1e293b; font-weight: 600; }
        .finance-total td {
            background: #1a56db;
            color: #fff;
            font-weight: 700;
            font-size: 13px;
            padding: 8px 12px;
        }

        .checkbox { display: inline-block; width: 11px; height: 11px; border: 1px solid #1a56db; margin-right: 2px; vertical-align: middle; }

        /* ── Signatures ── */
        .sig-area { width: 100%; margin-top: 25px; border-collapse: collapse; }
        .sig-area td { width: 50%; text-align: center; vertical-align: bottom; padding: 8px 20px; }
        .sig-img { max-width: 110px; max-height: 45px; margin-bottom: 4px; }
        .sig-underline { border-top: 1px solid #93a3c8; margin-top: 45px; padding-top: 5px; font-size: 10px; color: #64748b; display: inline-block; width: 170px; }

        /* ── Footer ── */
        .footer {
            margin-top: 15px;
            padding-top: 8px;
            border-top: 2px solid #1a56db;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
<div class="page">

    <!-- Blue Header Band -->
    <div class="header-band">
        <table class="header-band-table"><tr>
            <td class="header-logo">
                @if(isset($logo) && $logo)
                    <img src="{{ $logo }}" alt="Logo">
                @endif
            </td>
            <td class="header-center">
                <div class="header-title">CONTRAT DE LOCATION DE VEHICULE</div>
                <div class="header-contract-no">{{ $contract->contract_number }}</div>
            </td>
            <td class="header-right">
                @if($agency->phone ?? false)
                    Tel: {{ $agency->phone }}<br>
                @endif
                @if($agency->email ?? false)
                    {{ $agency->email }}<br>
                @endif
                @php $website = $agency->settings['website'] ?? null; @endphp
                @if($website)
                    {{ $website }}
                @endif
            </td>
        </tr></table>
    </div>

    <!-- Agency Info Bar -->
    <div class="agency-bar">
        <table class="agency-bar-table"><tr>
            <td style="width:50%;">
                <div class="agency-bar-name">{{ $agency->name ?? 'Agence' }}</div>
                @if($agency->address ?? false)
                    {{ $agency->address }}
                @endif
            </td>
            <td style="width:50%; text-align:right;">
                @if($agency->city ?? false) {{ $agency->city }} @endif
                @if($agency->country ?? false) - {{ $agency->country }} @endif
            </td>
        </tr></table>
    </div>

    <!-- Dates Bar -->
    <table class="dates-bar"><tr>
        <td>
            <div class="dt-label">Date de départ</div>
            <div class="dt-value">{{ \Carbon\Carbon::parse($contract->start_date)->format('d/m/Y') }} {{ $contract->start_time ?? '' }}</div>
        </td>
        <td>
            <div class="dt-label">Date de retour</div>
            <div class="dt-value">{{ \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') }} {{ $contract->end_time ?? '' }}</div>
        </td>
        <td>
            <div class="dt-label">Durée</div>
            <div class="dt-value">{{ $contract->planned_days ?? $contract->duration_days ?? 'N/A' }} jours</div>
        </td>
        <td>
            <div class="dt-label">Prolongation</div>
            <div class="dt-value">— jours</div>
        </td>
    </tr></table>

    <!-- VEHICULE -->
    <div class="section-accent">VEHICULE</div>
    <div class="card">
        <table class="vehicle-grid">
            <tr>
                <td>
                    <span class="dlabel">Marque:</span><br>
                    <span class="dvalue">
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
                    <span class="dlabel">Immatriculation:</span><br>
                    <span class="dvalue">{{ $vehicle->registration_number ?? 'N/A' }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="dlabel">Lieu de livraison:</span><br>
                    <span class="dvalue">{{ $contract->pickup_location ?? 'Agence' }}</span>
                </td>
                <td>
                    <span class="dlabel">Lieu de reprise:</span><br>
                    <span class="dvalue">{{ $contract->dropoff_location ?? 'Agence' }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="dlabel">État technique:</span><br>
                    <span class="dvalue">{{ $contract->observations ?? '—' }}</span>
                </td>
                <td>
                    <span class="dlabel">N° fiche contrôle:</span><br>
                    <span class="dvalue">{{ $contract->control_number ?? '—' }}</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- LOCATAIRE -->
    <div class="section-accent">LOCATAIRE</div>
    <div class="card">
        <table class="data-row">
            @if(isset($client) && $client)
            <tr>
                <td class="dlabel">Nom</td>
                <td class="dvalue">{{ $client->last_name ?? '' }}</td>
                <td class="dlabel">Prénom</td>
                <td class="dvalue">{{ $client->first_name ?? '' }}</td>
            </tr>
            <tr>
                <td class="dlabel">Né le</td>
                <td class="dvalue">{{ $client->birth_date ? \Carbon\Carbon::parse($client->birth_date)->format('d/m/Y') : '' }}</td>
                <td class="dlabel">Adresse</td>
                <td class="dvalue">{{ $client->address ?? '' }}</td>
            </tr>
            <tr>
                <td class="dlabel">Permis N°</td>
                <td class="dvalue">{{ $client->driving_license_number ?? '' }}</td>
                <td class="dlabel">Délivré le</td>
                <td class="dvalue">{{ $client->driving_license_issue_date ? \Carbon\Carbon::parse($client->driving_license_issue_date)->format('d/m/Y') : '' }}</td>
            </tr>
            <tr>
                <td class="dlabel">Pasport N°</td>
                <td class="dvalue">{{ $client->passport_number ?? '' }}</td>
                <td class="dlabel">Délivré le</td>
                <td class="dvalue">{{ $client->passport_issue_date ? \Carbon\Carbon::parse($client->passport_issue_date)->format('d/m/Y') : '' }}</td>
            </tr>
            <tr>
                <td class="dlabel">CIN N°</td>
                <td class="dvalue">{{ $client->cin_number ?? '' }}</td>
                <td class="dlabel">Valable jusqu'au</td>
                <td class="dvalue">{{ $client->cin_valid_until ? \Carbon\Carbon::parse($client->cin_valid_until)->format('d/m/Y') : '' }}</td>
            </tr>
            <tr>
                <td class="dlabel">Nationalité</td>
                <td class="dvalue">{{ $client->nationality ?? '' }}</td>
                <td class="dlabel">Téléphone</td>
                <td class="dvalue">{{ $client->phone ?? '' }}</td>
            </tr>
            @else
            <tr><td colspan="4" class="dvalue">Client non spécifié</td></tr>
            @endif
        </table>
    </div>

    <!-- 2ème CONDUCTEUR -->
    <div class="section-accent">2ème CONDUCTEUR</div>
    <div class="card">
        <table class="data-row">
            <tr>
                <td class="dlabel">Nom</td>
                <td class="dvalue">{{ $secondaryClient->last_name ?? '' }}</td>
                <td class="dlabel">Prénom</td>
                <td class="dvalue">{{ $secondaryClient->first_name ?? '' }}</td>
            </tr>
            <tr>
                <td class="dlabel">Né le</td>
                <td class="dvalue">{{ isset($secondaryClient) && $secondaryClient->birth_date ? \Carbon\Carbon::parse($secondaryClient->birth_date)->format('d/m/Y') : '' }}</td>
                <td class="dlabel">Adresse</td>
                <td class="dvalue">{{ $secondaryClient->address ?? '' }}</td>
            </tr>
            <tr>
                <td class="dlabel">Permis N°</td>
                <td class="dvalue">{{ $secondaryClient->driving_license_number ?? '' }}</td>
                <td class="dlabel">Délivré le</td>
                <td class="dvalue">{{ isset($secondaryClient) && $secondaryClient->driving_license_issue_date ? \Carbon\Carbon::parse($secondaryClient->driving_license_issue_date)->format('d/m/Y') : '' }}</td>
            </tr>
            <tr>
                <td class="dlabel">Pasport N°</td>
                <td class="dvalue">{{ $secondaryClient->passport_number ?? '' }}</td>
                <td class="dlabel">Délivré le</td>
                <td class="dvalue">{{ isset($secondaryClient) && $secondaryClient->passport_issue_date ? \Carbon\Carbon::parse($secondaryClient->passport_issue_date)->format('d/m/Y') : '' }}</td>
            </tr>
            <tr>
                <td class="dlabel">CIN N°</td>
                <td class="dvalue">{{ $secondaryClient->cin_number ?? '' }}</td>
                <td class="dlabel">Valable jusqu'au</td>
                <td class="dvalue">{{ isset($secondaryClient) && $secondaryClient->cin_valid_until ? \Carbon\Carbon::parse($secondaryClient->cin_valid_until)->format('d/m/Y') : '' }}</td>
            </tr>
            <tr>
                <td class="dlabel">Nationalité</td>
                <td class="dvalue">{{ $secondaryClient->nationality ?? '' }}</td>
                <td class="dlabel">Téléphone</td>
                <td class="dvalue">{{ $secondaryClient->phone ?? '' }}</td>
            </tr>
        </table>
    </div>

    <!-- Bottom: Legal + Finance -->
    <table class="bottom-split"><tr>
        <td>
            <div class="legal-box">
                <div class="legal-box-title">LE LOCATAIRE:</div>
                <ul>
                    <li>Je déclare en tout connaissance de cause que si le dite contrat subit des dégâts matériels, tous engendrés sont à ma charge.</li>
                    <li>J'ai lu et j'accepte les conditions stipulées ci contre et au verso de ce contrat.</li>
                    <li>Déclare avoir pris connaissance des clauses et conditions de location en vigueur stipulées au verso de ce contrat ainsi que la facturation.</li>
                </ul>
                <div style="margin-top: 10px;">
                    <span style="font-weight:700; color:#1a56db;">ASSURANCE:</span>
                    &nbsp;
                    <span class="checkbox"></span> Avec franchise
                    &nbsp;
                    <span class="checkbox"></span> Sans franchise
                </div>
            </div>
        </td>
        <td>
            <div class="finance-box">
                <div class="finance-box-title">DETAILS FINANCIERS</div>
                <table class="finance-row">
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
                </table>
                <table class="finance-row">
                    <tr class="finance-total">
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
        Document généré le {{ $generated_at }} par {{ $generated_by }} &bull; {{ $agency->name ?? config('app.name') }}
    </div>

</div>
</body>
</html>
