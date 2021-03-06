<?php

namespace App\Repositories\Core\Eloquent;

use App\Models\AccountEntry;
use App\Repositories\Core\BaseEloquentRepository;
use App\Repositories\Interfaces\AccountEntryRepositoryInterface;

class AccountEntryRepository extends BaseEloquentRepository implements AccountEntryRepositoryInterface
{
    protected $model = AccountEntry::class;

    /**
     * Returns account entries according to the given account id
     *
     * @param int $account_id
     * @param array $range_date
     * @return Illuminate\Database\Eloquent\Collection
     */  
    public function getEntriesByAccountId($account_id, array $range_date)
    {
        return $this->model::where('account_id', $account_id)
                ->where('date', '>=', $range_date['from'])
                ->where('date', '<=', $range_date['to'])
                ->orderBy('date')
                ->get();
    }

    /**
     * Returns the total value of the type of category for a given month and year
     *
     * @param int $categoryType
     * @param string $month
     * @param string $year
     * @return int
     */ 
    public function getTotalTypeOfCategory($categoryType, $month, $year): int
    {
        return $this->model::select('account_entries.id')
                ->join('categories', 'categories.id', '=', 'account_entries.category_id')
                ->where('categories.type', $categoryType)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->sum('value');;
    }

    /**
     * Returns an array with the total grouped by category for a given month, year and category type
     *
     * @param int $categoryType
     * @param string $date
     * @return array
     */ 
    public function getTotalByCategory($categoryType, $month, $year): array
    {       
        return $this->model::selectRaw('categories.name, SUM(account_entries.value) as total')
            ->join('categories', 'categories.id', '=', 'account_entries.category_id')
            ->where('categories.type', $categoryType)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->groupBy('categories.name')
            ->get()
            ->toArray();
    }

    /**
     * Returns the total values of entries by category type for a given date
     *
     * @param int $categoryType
     * @param array $filter
     * @return array
     */ 
    public function getTotalByCategoryTypeForRangeDate($categoryType, array $filter): array
    {       
        return $this->model::selectRaw('categories.name as category, categories.id, SUM(account_entries.value) / 100 as total, count(*) as quantity')
            ->join('categories', 'categories.id', '=', 'account_entries.category_id')
            ->where('categories.type', $categoryType)
            ->where('date', '>=', $filter['from'])
            ->where('date', '<=', $filter['to'])
            ->orderByDesc('total')
            ->groupBy('categories.name', 'categories.id')
            ->get()
            ->toArray();
    }

    /**
     * Returns the entries for the given category id and range date
     *
     * @param int $categoryType
     * @param array $filter
     * @return array
     */ 
    public function getEntriesByCategoryAndRangeDate($from, $to, $category_id): array
    {       
        return $this->model::with('account')
                ->with('category')
                ->where('category_id', $category_id)
                ->where('date', '>=', $from)
                ->where('date', '<=', $to)
                ->orderBy('date')
                ->get()
                ->toArray();
    }
}