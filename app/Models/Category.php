<?php

namespace App\Models;

use App\Models\User;
use App\Traits\UserTrait;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use UserTrait;

    const INCOME    = 1;
    const EXPENSE   = 2;

    protected $fillable = ['type','name', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoiceEntry()
    {
        return $this->hasMany(InvoiceEntry::class);
    }

    public function isExpense(): bool
    {
        return $this->type == static::EXPENSE;
    }

    public static function createWithoutEvents(array $data)
    {
        return static::withoutEvents(function() use ($data) {
            return self::create([
                'name'      => $data['name'],
                'type'      => $data['type'],
                'user_id'   => $data['user_id']
            ]);
        });
    }

    public static function getDefaultIncomeCategories()
    {
        return [
            __('global.default_categories.salary'),
            __('global.default_categories.revenue'),
            __('global.default_categories.withdraw'),
            __('global.default_categories.loans'),
            __('global.default_categories.investments'),
            __('global.default_categories.credit_on_card'),
            __('global.default_categories.bank_transfer'),
            __('global.default_categories.sales'),
            __('global.default_categories.others'),
        ];
    }

    public static function getDefaultExpenseCategories()
    {
        return [
            __('global.default_categories.house'),
            __('global.default_categories.subscriptions'),
            __('global.default_categories.personal_expenses'),
            __('global.default_categories.education'),
            __('global.default_categories.loans'),
            __('global.default_categories.electronics'),
            __('global.default_categories.recreation'),
            __('global.default_categories.food'),
            __('global.default_categories.health'),
            __('global.default_categories.payments'),
            __('global.default_categories.supermarket'),
            __('global.default_categories.investments'),
            __('global.default_categories.bank_transfer'),
            __('global.default_categories.transport'),
            __('global.default_categories.withdraw'),
            __('global.default_categories.clothes'),
            __('global.default_categories.travels'),
            __('global.default_categories.others')
        ];
    }
}
