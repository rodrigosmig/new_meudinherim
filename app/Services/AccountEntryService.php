<?php

namespace App\Services;

use DateTime;
use App\Models\Account;
use App\Models\AccountEntry;
use App\Repositories\Interfaces\AccountEntryRepositoryInterface;

class AccountEntryService
{
    protected $repository;
    protected $tagService;

    public function __construct(AccountEntryRepositoryInterface $repository, TagService $tagService)
    {
        $this->repository = $repository;        
        $this->tagService = $tagService;
    }

    /**
     * Creates the entries to the account
     *
     * @param Account $account
     * @param array $data
     * @return AccountEntry
     */ 
    public function create($account_id, array $data)
    {
        $data['account_id'] = $account_id;

        $entry = $this->repository->create($data);

        if (isset($data["tags"]) && $data["tags"]) {
            $this->tagService->createAccountEntryTag($entry, $data["tags"]);
        }

        return $entry;
    }

    public function update(AccountEntry $entry, array $data)
    {
        if (isset($data["tags"])) {
            $this->tagService->createInvoiceEntryTags($entry, $data["tags"]);
        }

        return $this->repository->update($entry, $data);;
    }

    public function delete(AccountEntry $entry)
    {
        return $this->repository->delete($entry);
    }

    public function findById($id)
    {
        return $this->repository->findById($id);
    }

    /**
     * Returns account entries according to the given account id
     *
     * @param int $account_id
     * @param array $range_date
     * @return Illuminate\Database\Eloquent\Collection
     */  
    public function getEntriesByAccountId($account_id, array $filter = [], $per_page = 10)
    {
        $range_date = [
            'from'  => date('Y-m-01'),
            'to'    => date('Y-m-t')
        ];

        if ($filter && isset($filter['from']) && isset($filter['to'])) {
            $range_date['from'] = $filter['from'];
            $range_date['to']   = $filter['to'];
        }

        return $this->repository->getEntriesByAccountId($account_id, $range_date, $per_page);
    }

    /**
     * Returns an array with the total value of the lasts six months according to the given category type
     * The array key represents the month number
     *
     * @param int $categoryType
     * @param string $date
     * @return array
     */ 
    public function getTotalOfSixMonthsByCategoryTypeAndDate($categoryType, $date, $to_dashboard = false): array
    {
        $new_date   = (new DateTime($date))->modify('-5 months');
        $result     = [];

        for ($i=0; $i < 6; $i++) {
            $month  = $new_date->format('m');
            $year   = $new_date->format('Y');

            $total = $this->repository->getTotalTypeOfCategory($categoryType, $month, $year, $to_dashboard);

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
    public function getTotalByCategoryForChart($categoryType, $date, $to_dashboard = false): array
    {
        $new_date   = new DateTime($date);
        $month      = $new_date->format('m');
        $year       = $new_date->format('Y');
        $result     = [];
        
        $categories = $this->repository->getTotalByCategory($categoryType, $month, $year, $to_dashboard);

        foreach ($categories as $category) {
            $result[] = [
                'value' => $category['total'] / 100,
                'label' => $category['name']
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
    public function getTotalMonthlyByCategory($categoryType, $date, $to_dashboard = false): float
    {
        $new_date   = new DateTime($date);
        $month      = $new_date->format('m');
        $year       = $new_date->format('Y');
        
        $total = $this->repository->getTotalTypeOfCategory($categoryType, $month, $year, $to_dashboard);

        return $total / 100;
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
        if (isset($filter["tags"]) && !empty($filter["tags"])) {
            $tags = $this->tagService->getTags($filter["tags"])->toArray();
            $filter["tags"] = array_map(function($tags) {
                return $tags["id"];
            }, $tags);
        }

        return $this->repository->getTotalByCategoryTypeForRangeDate($categoryType, $filter);
    }

    /**
     * Returns the entries for the given category id and range date
     *
     * @param int $categoryType
     * @param array $filter
     * @return Illuminate\Database\Eloquent\Collection
     */ 
    public function getEntriesByCategoryAndRangeDate($from, $to, $category_id, $account_id)
    {
        return $this->repository->getEntriesByCategoryAndRangeDate($from, $to, $category_id, $account_id);
    }

    /**
     * Transfers an amount between bank accounts
     *
     * @param Account $from_account
     * @param Account $to_account
     * @param array $data
     * @return void
     */  
    public function accountTransfer(Account $from_account, Account $to_account, array $data): void
    {
        $newData = $this->prepareDataForEntry($data);

        $accountService = app(AccountService::class);

        $this->create($from_account->id, $newData['source']);
        $this->create($to_account->id, $newData['destination']);

        $accountService->updateBalance($from_account, $data['date']);
        $accountService->updateBalance($to_account, $data['date']);
    }

    private function prepareDataForEntry($data): array
    {
        $newData['source'] = [
            "account_id" => $data['source_account_id'],
            "category_id" => $data['source_category_id'],
            "date" => $data['date'],
            "description" => $data['description'],
            "value" => $data['value'],
        ];

        $newData['destination'] = [
            "account_id" => $data['destination_account_id'],
            "category_id" => $data['destination_category_id'],
            "date" => $data['date'],
            "description" => $data['description'],
            "value" => $data['value'],
        ];

        return $newData;
    }
}
