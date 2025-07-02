<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $guarded = [];

    protected $casts = [
        'direction' => 'string',
    ];

    /**
     * Polymorphic relationship - Invoice can belong to Customer, Franchise, etc.
     */
    public function invoiceable()
    {
        return $this->morphTo();
    }

    /**
     * Legacy customer relationship (for backward compatibility)
     * @deprecated Use invoiceable() instead
     */
  

    /**
     * Legacy franchise relationship (for backward compatibility)
     * @deprecated Use invoiceable() instead
     */
    public function franchise()
    {
        return $this->belongsTo(Franchise::class, 'franchise_id');
    }

    /**
     * Helper method to get customer if invoice is for a customer
     */
    public function getCustomer()
    {
        return $this->invoiceable_type === Customer::class ? $this->invoiceable : null;
    }

    /**
     * Helper method to get franchise if invoice is for a franchise
     */
    public function getFranchise()
    {
        return $this->invoiceable_type === Franchise::class ? $this->invoiceable : null;
    }

    /**
     * Check if invoice belongs to a customer
     */
    public function isForCustomer(): bool
    {
        return $this->invoiceable_type === Customer::class;
    }

    /**
     * Check if invoice belongs to a franchise
     */
    public function isForFranchise(): bool
    {
        return $this->invoiceable_type === Franchise::class;
    }

    /**
     * Get the display name for the invoiceable entity
     */
    public function getInvoiceableDisplayName(): string
    {
        if ($this->invoiceable) {
            if ($this->isForCustomer()) {
                return $this->invoiceable->name ?? 'Unknown Customer';
            } elseif ($this->isForFranchise()) {
                return $this->invoiceable->business_name ?? 'Unknown Franchise';
            }
        }
        return 'Unknown Entity';
    }

    public function isPayable(): bool
    {
        return $this->direction === 'payable';
    }

    public function isReceivable(): bool
    {
        return $this->direction === 'receivable';
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
