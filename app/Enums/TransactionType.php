<?php

namespace App\Enums;

enum TransactionType: string
{
    case Credit = 'credit';
    case Debit = 'debit';
    case Refund = 'refund';
    case Hold = 'hold';
    case SubscriptionDebit = 'subscription_debit';
    case MuhurthamRequestDebit = 'muhurtham_request_debit';
    case CompatibilityRequestDebit = 'compatibility_request_debit';
    case PdfDownloadDebit = 'pdf_download_debit';
    case PromotionalCredit = 'promotional_credit';
}
