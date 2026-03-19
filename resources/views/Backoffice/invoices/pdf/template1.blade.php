<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Facture #{{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #2d3436;
            background: #fff;
        }
        .page { padding: 30px 40px; }

        /* ── Header ── */
        .header-bar {
            background: #1a56db;
            color: #fff;
            padding: 20px 30px;
            margin: -30px -40px 25px -40px;
        }
        .header-table { width: 100%; }
        .header-table td { vertical-align: top; }
        .header-title { font-size: 28px; font-weight: 700; letter-spacing: 2px; }
        .header-subtitle { font-size: 12px; opacity: 0.85; margin-top: 4px; }
        .header-right { text-align: right; }
        .header-logo img { max-height: 65px; max-width: 140px; }

        /* ── Info boxes ── */
        .info-grid { width: 100%; margin-bottom: 20px; }
        .info-grid td { vertical-align: top; width: 50%; }
        .info-box {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 14px 16px;
            background: #f8fafc;
        }
        .info-box-title {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #1a56db;
            font-weight: 700;
            margin-bottom: 8px;
            border-bottom: 2px solid #1a56db;
            padding-bottom: 5px;
        }
        .info-line { margin-bottom: 3px; color: #4a5568; font-size: 11px; }
        .info-line strong { color: #2d3436; }

        /* ── Invoice meta ── */
        .meta-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .meta-table td {
            padding: 8px 14px;
            border: 1px solid #e2e8f0;
            font-size: 11px;
        }
        .meta-label { background: #f1f5f9; font-weight: 700; color: #475569; width: 140px; }
        .meta-value { color: #1e293b; }

        /* ── Status ── */
        .status-badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-paid { background: #dcfce7; color: #166534; }
        .status-sent { background: #dbeafe; color: #1e40af; }
        .status-draft { background: #f1f5f9; color: #475569; }
        .status-partially_paid { background: #fef3c7; color: #92400e; }
        .status-cancelled { background: #fecaca; color: #991b1b; }

        /* ── Items table ── */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table th {
            background: #1a56db;
            color: #fff;
            padding: 10px 12px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: left;
        }
        .items-table th:last-child,
        .items-table td:last-child { text-align: right; }
        .items-table th:nth-child(2),
        .items-table th:nth-child(3),
        .items-table td:nth-child(2),
        .items-table td:nth-child(3) { text-align: center; }
        .items-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }
        .items-table tbody tr:nth-child(even) td { background: #f8fafc; }

        /* ── Totals ── */
        .totals-wrapper { width: 100%; }
        .totals-wrapper td.spacer { width: 55%; }
        .totals-table { width: 100%; border-collapse: collapse; }
        .totals-table td {
            padding: 6px 12px;
            font-size: 11px;
        }
        .totals-table .label-col { text-align: right; color: #64748b; font-weight: 600; }
        .totals-table .value-col { text-align: right; width: 130px; }
        .totals-table .grand-total td {
            background: #1a56db;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            padding: 10px 12px;
        }
        .totals-table .paid-status td {
            font-size: 11px;
            padding-top: 8px;
        }

        /* ── Notes ── */
        .notes-box {
            background: #eff6ff;
            border-left: 4px solid #1a56db;
            padding: 12px 16px;
            margin: 20px 0;
            border-radius: 0 6px 6px 0;
        }
        .notes-title { font-weight: 700; color: #1a56db; margin-bottom: 4px; font-size: 10px; text-transform: uppercase; }

        /* ── Signature ── */
        .signature-grid { width: 100%; margin-top: 40px; }
        .signature-grid td { width: 50%; vertical-align: bottom; }
        .sig-box { text-align: center; }
        .sig-img { max-width: 130px; max-height: 55px; margin-bottom: 5px; }
        .sig-line { border-top: 1px solid #94a3b8; padding-top: 6px; font-size: 10px; color: #64748b; display: inline-block; width: 180px; }

        /* ── Footer ── */
        .footer {
            margin-top: 30px;
            padding-top: 12px;
            border-top: 2px solid #1a56db;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
<div class="page">

    <!-- Header -->
    <div class="header-bar">
        <table class="header-table"><tr>
            <td>
                <div class="header-title">FACTURE</div>
                <div class="header-subtitle">N° {{ $invoice->invoice_number }}</div>
            </td>
            <td class="header-right">
                @if(isset($logo) && $logo)
                    <div class="header-logo"><img src="{{ $logo }}" alt="Logo"></div>
                @endif
            </td>
        </tr></table>
    </div>

    <!-- Agency & Client Info -->
    <table class="info-grid"><tr>
        <td style="padding-right: 10px;">
            <div class="info-box">
                <div class="info-box-title">De</div>
                <div class="info-line"><strong>{{ $invoice->agency->name ?? ($agency->name ?? 'Agence') }}</strong></div>
                @if($invoice->agency->address ?? $agency->address ?? false)
                    <div class="info-line">{{ $invoice->agency->address ?? $agency->address }}</div>
                @endif
                @if($invoice->agency->phone ?? $agency->phone ?? false)
                    <div class="info-line">Tél: {{ $invoice->agency->phone ?? $agency->phone }}</div>
                @endif
                @if($invoice->agency->email ?? $agency->email ?? false)
                    <div class="info-line">{{ $invoice->agency->email ?? $agency->email }}</div>
                @endif
            </div>
        </td>
        <td style="padding-left: 10px;">
            <div class="info-box">
                <div class="info-box-title">Facturer à</div>
                @if($invoice->client)
                    <div class="info-line"><strong>{{ $invoice->client->first_name ?? '' }} {{ $invoice->client->last_name ?? '' }}</strong></div>
                    @if($invoice->client->email ?? false)
                        <div class="info-line">{{ $invoice->client->email }}</div>
                    @endif
                    @if($invoice->client->phone ?? false)
                        <div class="info-line">Tél: {{ $invoice->client->phone }}</div>
                    @endif
                    @if($invoice->client->address ?? false)
                        <div class="info-line">{{ $invoice->client->address }}</div>
                    @endif
                @elseif($invoice->company_name)
                    <div class="info-line"><strong>{{ $invoice->company_name }}</strong></div>
                @else
                    <div class="info-line">Client non spécifié</div>
                @endif
            </div>
        </td>
    </tr></table>

    <!-- Invoice Meta -->
    <table class="meta-table">
        <tr>
            <td class="meta-label">Date d'émission</td>
            <td class="meta-value">{{ $invoice->formatted_issue_date ?? $invoice->created_at->format('d/m/Y') }}</td>
            <td class="meta-label">Date d'échéance</td>
            <td class="meta-value">{{ $invoice->formatted_due_date ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Statut</td>
            <td class="meta-value">
                <span class="status-badge status-{{ $invoice->status }}">{{ $invoice->status_text ?? $invoice->status }}</span>
            </td>
            <td class="meta-label">Contrat</td>
            <td class="meta-value">{{ $invoice->rentalContract->contract_number ?? '—' }}</td>
        </tr>
    </table>

    <!-- Items -->
    <table class="items-table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Qté</th>
                <th>Prix unitaire</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoice->items as $item)
                <tr>
                    <td>{{ $item->description ?? 'Description' }}</td>
                    <td>{{ $item->quantity ?? 1 }}</td>
                    <td>{{ number_format($item->unit_price ?? $item->unit_price_ttc ?? 0, 2, ',', ' ') }} MAD</td>
                    <td>{{ number_format($item->total ?? $item->total_ttc ?? 0, 2, ',', ' ') }} MAD</td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align:center; color:#94a3b8;">Aucun détail disponible</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- Totals -->
    <table class="totals-wrapper"><tr>
        <td class="spacer"></td>
        <td>
            <table class="totals-table">
                <tr>
                    <td class="label-col">Sous-total HT</td>
                    <td class="value-col">{{ number_format($invoice->total_ht ?? $invoice->subtotal ?? 0, 2, ',', ' ') }} MAD</td>
                </tr>
                @if(($invoice->discount ?? 0) > 0)
                <tr>
                    <td class="label-col">Remise</td>
                    <td class="value-col">- {{ number_format($invoice->discount, 2, ',', ' ') }} MAD</td>
                </tr>
                @endif
                <tr>
                    <td class="label-col">TVA ({{ $invoice->vat_rate ?? $invoice->tax_rate ?? 20 }}%)</td>
                    <td class="value-col">{{ number_format($invoice->total_vat ?? $invoice->tax_amount ?? 0, 2, ',', ' ') }} MAD</td>
                </tr>
                <tr class="grand-total">
                    <td class="label-col" style="color:#fff;">TOTAL TTC</td>
                    <td class="value-col">{{ number_format($invoice->total_ttc ?? 0, 2, ',', ' ') }} MAD</td>
                </tr>
                @if($invoice->status == 'paid')
                <tr class="paid-status"><td colspan="2" style="text-align:right; color:#166534;">✓ Intégralement payée</td></tr>
                @elseif($invoice->status == 'partially_paid')
                <tr class="paid-status"><td colspan="2" style="text-align:right; color:#92400e;">Payé: {{ number_format($invoice->paid_amount ?? 0, 2, ',', ' ') }} MAD</td></tr>
                @endif
            </table>
        </td>
    </tr></table>

    @if($invoice->notes)
    <div class="notes-box">
        <div class="notes-title">Notes</div>
        <div>{{ $invoice->notes }}</div>
    </div>
    @endif

    <!-- Signatures -->
    <table class="signature-grid"><tr>
        <td><div class="sig-box"><div class="sig-line">Signature du client</div></div></td>
        <td>
            <div class="sig-box">
                @if(isset($signature) && $signature)
                    <img src="{{ $signature }}" alt="Signature" class="sig-img"><br>
                @endif
                <div class="sig-line">Cachet & signature de l'agence</div>
            </div>
        </td>
    </tr></table>

    <!-- Footer -->
    <div class="footer">
        Document généré le {{ $generated_at }} par {{ $generated_by }} &bull; {{ $invoice->agency->name ?? ($agency->name ?? config('app.name')) }}
    </div>

</div>
</body>
</html>
