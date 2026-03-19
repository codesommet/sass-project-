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
            color: #1a1a2e;
            background: #fff;
        }
        .page { padding: 0; }

        /* ── Top accent ── */
        .accent-bar { height: 6px; background: linear-gradient(90deg, #0f766e, #14b8a6, #5eead4); }

        .content { padding: 30px 40px; }

        /* ── Header ── */
        .header-table { width: 100%; margin-bottom: 30px; }
        .header-table td { vertical-align: top; }
        .logo-cell { width: 50%; }
        .logo-cell img { max-height: 60px; max-width: 130px; }
        .invoice-title-cell { text-align: right; width: 50%; }
        .invoice-label {
            font-size: 32px;
            font-weight: 700;
            color: #0f766e;
            letter-spacing: 3px;
        }
        .invoice-number {
            font-size: 13px;
            color: #64748b;
            margin-top: 2px;
        }

        /* ── Divider ── */
        .divider { height: 1px; background: #e2e8f0; margin: 0 0 20px 0; }

        /* ── Two-col info ── */
        .info-table { width: 100%; margin-bottom: 22px; }
        .info-table > tbody > tr > td { vertical-align: top; width: 50%; }
        .info-block-title {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #0f766e;
            font-weight: 700;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 2px solid #0f766e;
            display: inline-block;
        }
        .info-text { color: #475569; font-size: 11px; margin-bottom: 2px; }
        .info-text strong { color: #1e293b; }

        /* ── Detail cards ── */
        .detail-bar {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 22px;
            background: #f0fdfa;
            border-radius: 6px;
        }
        .detail-bar td {
            padding: 10px 16px;
            border-right: 1px solid #ccfbf1;
            font-size: 11px;
        }
        .detail-bar td:last-child { border-right: none; }
        .detail-label { font-size: 9px; text-transform: uppercase; color: #0f766e; font-weight: 700; letter-spacing: 0.5px; }
        .detail-value { color: #1e293b; font-weight: 600; margin-top: 2px; }

        /* ── Status ── */
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .status-paid { background: #dcfce7; color: #166534; }
        .status-sent { background: #dbeafe; color: #1e40af; }
        .status-draft { background: #f1f5f9; color: #475569; }
        .status-partially_paid { background: #fef3c7; color: #92400e; }
        .status-cancelled { background: #fecaca; color: #991b1b; }

        /* ── Items table ── */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 22px; }
        .items-table thead th {
            background: #0f766e;
            color: #fff;
            padding: 10px 14px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: left;
        }
        .items-table thead th:nth-child(2),
        .items-table thead th:nth-child(3) { text-align: center; }
        .items-table thead th:last-child { text-align: right; }
        .items-table td {
            padding: 10px 14px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }
        .items-table td:nth-child(2),
        .items-table td:nth-child(3) { text-align: center; }
        .items-table td:last-child { text-align: right; }
        .items-table tbody tr:nth-child(even) td { background: #f8fafc; }

        /* ── Totals ── */
        .totals-outer { width: 100%; }
        .totals-outer td.spacer { width: 55%; }
        .totals-box {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
        }
        .totals-box td { padding: 7px 14px; font-size: 11px; }
        .totals-box .t-label { text-align: right; color: #64748b; font-weight: 600; background: #f8fafc; }
        .totals-box .t-value { text-align: right; width: 130px; color: #1e293b; }
        .totals-box .t-grand td {
            background: #0f766e;
            color: #fff;
            font-weight: 700;
            font-size: 14px;
            padding: 12px 14px;
        }
        .totals-box .t-status td {
            font-size: 10px;
            padding-top: 6px;
            background: #f0fdfa;
        }

        /* ── Notes ── */
        .notes-section {
            background: #f0fdfa;
            border: 1px solid #99f6e4;
            padding: 14px 18px;
            border-radius: 6px;
            margin: 18px 0;
        }
        .notes-heading { font-weight: 700; font-size: 10px; text-transform: uppercase; color: #0f766e; margin-bottom: 4px; }

        /* ── Signatures ── */
        .sig-table { width: 100%; margin-top: 40px; }
        .sig-table td { width: 50%; vertical-align: bottom; text-align: center; }
        .sig-image { max-width: 120px; max-height: 50px; margin-bottom: 5px; }
        .sig-underline {
            display: inline-block;
            width: 170px;
            border-top: 1px solid #94a3b8;
            padding-top: 6px;
            font-size: 10px;
            color: #64748b;
        }

        /* ── Footer ── */
        .footer-bar {
            margin-top: 25px;
            padding: 12px 0;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
        }
        .footer-accent {
            display: inline-block;
            width: 40px;
            height: 3px;
            background: #0f766e;
            border-radius: 2px;
            margin-bottom: 6px;
        }
    </style>
</head>
<body>
<div class="page">
    <div class="accent-bar"></div>
    <div class="content">

        <!-- Header -->
        <table class="header-table"><tr>
            <td class="logo-cell">
                @if(isset($logo) && $logo)
                    <img src="{{ $logo }}" alt="Logo">
                @else
                    <span style="font-size:18px; font-weight:700; color:#0f766e;">{{ $invoice->agency->name ?? ($agency->name ?? '') }}</span>
                @endif
            </td>
            <td class="invoice-title-cell">
                <div class="invoice-label">FACTURE</div>
                <div class="invoice-number">{{ $invoice->invoice_number }}</div>
            </td>
        </tr></table>

        <div class="divider"></div>

        <!-- From / To -->
        <table class="info-table"><tr>
            <td style="padding-right: 15px;">
                <div class="info-block-title">Émetteur</div>
                <div class="info-text"><strong>{{ $invoice->agency->name ?? ($agency->name ?? 'Agence') }}</strong></div>
                @if($invoice->agency->address ?? $agency->address ?? false)
                    <div class="info-text">{{ $invoice->agency->address ?? $agency->address }}</div>
                @endif
                @if($invoice->agency->phone ?? $agency->phone ?? false)
                    <div class="info-text">Tél: {{ $invoice->agency->phone ?? $agency->phone }}</div>
                @endif
                @if($invoice->agency->email ?? $agency->email ?? false)
                    <div class="info-text">{{ $invoice->agency->email ?? $agency->email }}</div>
                @endif
            </td>
            <td style="padding-left: 15px;">
                <div class="info-block-title">Client</div>
                @if($invoice->client)
                    <div class="info-text"><strong>{{ $invoice->client->first_name ?? '' }} {{ $invoice->client->last_name ?? '' }}</strong></div>
                    @if($invoice->client->email ?? false)
                        <div class="info-text">{{ $invoice->client->email }}</div>
                    @endif
                    @if($invoice->client->phone ?? false)
                        <div class="info-text">Tél: {{ $invoice->client->phone }}</div>
                    @endif
                    @if($invoice->client->address ?? false)
                        <div class="info-text">{{ $invoice->client->address }}</div>
                    @endif
                @elseif($invoice->company_name)
                    <div class="info-text"><strong>{{ $invoice->company_name }}</strong></div>
                @else
                    <div class="info-text">Client non spécifié</div>
                @endif
            </td>
        </tr></table>

        <!-- Detail Bar -->
        <table class="detail-bar">
            <tr>
                <td>
                    <div class="detail-label">Date d'émission</div>
                    <div class="detail-value">{{ $invoice->formatted_issue_date ?? $invoice->created_at->format('d/m/Y') }}</div>
                </td>
                <td>
                    <div class="detail-label">Échéance</div>
                    <div class="detail-value">{{ $invoice->formatted_due_date ?? 'N/A' }}</div>
                </td>
                <td>
                    <div class="detail-label">Contrat</div>
                    <div class="detail-value">{{ $invoice->rentalContract->contract_number ?? '—' }}</div>
                </td>
                <td>
                    <div class="detail-label">Statut</div>
                    <div class="detail-value">
                        <span class="status-badge status-{{ $invoice->status }}">{{ $invoice->status_text ?? $invoice->status }}</span>
                    </div>
                </td>
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
        <table class="totals-outer"><tr>
            <td class="spacer"></td>
            <td>
                <table class="totals-box">
                    <tr>
                        <td class="t-label">Sous-total HT</td>
                        <td class="t-value">{{ number_format($invoice->total_ht ?? $invoice->subtotal ?? 0, 2, ',', ' ') }} MAD</td>
                    </tr>
                    @if(($invoice->discount ?? 0) > 0)
                    <tr>
                        <td class="t-label">Remise</td>
                        <td class="t-value">- {{ number_format($invoice->discount, 2, ',', ' ') }} MAD</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="t-label">TVA ({{ $invoice->vat_rate ?? $invoice->tax_rate ?? 20 }}%)</td>
                        <td class="t-value">{{ number_format($invoice->total_vat ?? $invoice->tax_amount ?? 0, 2, ',', ' ') }} MAD</td>
                    </tr>
                    <tr class="t-grand">
                        <td class="t-label" style="color:#fff; background:#0f766e;">TOTAL TTC</td>
                        <td class="t-value" style="background:#0f766e;">{{ number_format($invoice->total_ttc ?? 0, 2, ',', ' ') }} MAD</td>
                    </tr>
                    @if($invoice->status == 'paid')
                    <tr class="t-status"><td colspan="2" style="text-align:right; color:#166534;">✓ Intégralement payée</td></tr>
                    @elseif($invoice->status == 'partially_paid')
                    <tr class="t-status"><td colspan="2" style="text-align:right; color:#92400e;">Payé: {{ number_format($invoice->paid_amount ?? 0, 2, ',', ' ') }} MAD</td></tr>
                    @endif
                </table>
            </td>
        </tr></table>

        @if($invoice->notes)
        <div class="notes-section">
            <div class="notes-heading">Notes</div>
            <div>{{ $invoice->notes }}</div>
        </div>
        @endif

        <!-- Signatures -->
        <table class="sig-table"><tr>
            <td><div class="sig-underline">Signature du client</div></td>
            <td>
                @if(isset($signature) && $signature)
                    <img src="{{ $signature }}" alt="Signature" class="sig-image"><br>
                @endif
                <div class="sig-underline">Cachet & signature de l'agence</div>
            </td>
        </tr></table>

        <!-- Footer -->
        <div class="footer-bar">
            <div class="footer-accent"></div><br>
            Document généré le {{ $generated_at }} par {{ $generated_by }} &bull; {{ $invoice->agency->name ?? ($agency->name ?? config('app.name')) }}
        </div>

    </div>
</div>
</body>
</html>
