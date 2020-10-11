<?php

namespace App\Services;

use DateTime;
use App\Models\Account;
use App\Models\AccountEntry;

class AccountEntryService
{
    protected $account_entry;

    public function __construct(AccountEntry $account_entry, Account $account)
    {
        $this->account          = $account;
        $this->account_entry    = $account_entry;
    }

    /**
     * Creates the entries to the account
     *
     * @param Account $account
     * @param array $data
     * @return AccountEntry
     */ 
    public function make(Account $account, array $data): AccountEntry
    {
        return $account->entries()->create($data);
    }

    public function update($id, array $data)
    {
        $account_entry = $this->findById($id);

        if (! $account_entry) {
            return false;
        }
        
        $account_entry->update($data);
        
        return $account_entry;
    }

    public function delete($id)
    {
        $account_entry = $this->findById($id);

        if (! $account_entry) {
            return false;
        }

        return $account_entry->delete();
    }

    public function findById($id)
    {
        return $this->account_entry->find($id);
    }

    /**
     * Returns account entries according to the given account id
     *
     * @param int $account_id
     * @param string $range_date
     * @return Illuminate\Database\Eloquent\Collection
     */  
    public function getEntriesByAccount($account_id, $range_date = null)
    {
        $from = date('Y-m-1');
        $to = date('Y-m-t');

        if ($range_date && isset($range_date['from']) && isset($range_date['to'])) {
            $from = $range_date['from'];
            $to = $range_date['to'];
        }

        return $this->account_entry
            ->where('account_id', $account_id)
            ->where('date', '>=', $from)
            ->where('date', '<=', $to)
            ->orderBy('date')
            ->get();
    }

    /**
     * Returns an array with the total value of the lasts six months according to the given category type
     * The array key represents the month number
     *
     * @param int $categoryType
     * @param string $date
     * @return array
     */ 
    public function getTotalOfSixMonthsByCategoryTypeAndDate($categoryType, $date): array
    {
        $new_date   = (new DateTime($date))->modify('-5 months');
        $result     = [];

        for ($i=0; $i < 6; $i++) {
            $month  = $new_date->format('m');
            $year   = $new_date->format('Y');

            $total = $this->account_entry::select('account_entries.id')
                ->join('categories', 'categories.id', '=', 'account_entries.category_id')
                ->where('categories.type', $categoryType)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->sum('value');

            $key = (int) $month;
            $result[] = $total / 100;

            $new_date = $new_date->modify("+1 month");
        }

        return $result;
    }

    /**
     * Returns an array with the total grouped by category for a given date
     *
     * @param int $categoryType
     * @param string $date
     * @return array
     */ 
    public function getTotalByCategoryForChart($categoryType, $date): array
    {
        $new_date   = new DateTime($date);
        $month      = $new_date->format('m');
        $year       = $new_date->format('Y');
        $result     = [];
        
        $total = $this->account_entry::selectRaw('categories.name, SUM(account_entries.value) as total')
            ->join('categories', 'categories.id', '=', 'account_entries.category_id')
            ->where('categories.type', $categoryType)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->groupBy('categories.name')
            ->get()
            ->toArray();

        foreach ($total as $value) {
            $result[] = [
                'value' => $value['total'] / 100,
                'label' => $value['name']
            ];
        }

        return $result;
    }

    /**
     * Returns the total values of entries by category type for a given date
     *
     * @param int $categoryType
     * @param string $date
     * @return float
     */ 
    public function getTotalMonthlyByCategory($categoryType, $date): float
    {
        $new_date   = new DateTime($date);
        $month      = $new_date->format('m');
        $year       = $new_date->format('Y');
        
        $total = $this->account_entry
            ->join('categories', 'categories.id', '=', 'account_entries.category_id')
            ->where('categories.type', $categoryType)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->sum('value');

        return $total / 100;
    }
}
