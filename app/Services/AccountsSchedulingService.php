<?php

namespace App\Services;

use DateTime;
use App\Models\Parcel;
use App\Models\Account;
use App\Services\CardService;
use App\Models\AccountsScheduling;
use App\Repositories\Interfaces\ParcelRepositoryInterface;
use App\Repositories\Interfaces\InvoiceRepositoryInterface;
use App\Repositories\Interfaces\AccountEntryRepositoryInterface;
use App\Repositories\Interfaces\AccountsSchedulingRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

class AccountsSchedulingService
{
    protected $repository;
    protected $parcelRepository;
    protected $tagService;

    public function __construct(AccountsSchedulingRepositoryInterface $repository, ParcelRepositoryInterface $parcelRepository, TagService $tagService)
    {
        $this->repository       = $repository;
        $this->parcelRepository = $parcelRepository;
        $this->tagService       = $tagService;
    }

    public function create(array $data)
    {
        $data['monthly'] = isset($data['monthly']) && $data['monthly'] ? true : false;
        $data['installment'] = isset($data['installment']) && $data['installment'] ? true : false;

        if ($data['installment']) {
            return $this->createAccountSchedulingParcels($data);
        } 
        
        if (app()->runningInConsole()) {
            unset($data['installment']);
        }

        $account_scheduling = $this->repository->create($data);
        
        if (isset($data["tags"]) && $data["tags"]) {
            $this->tagService->createAccountSchedulingTag($account_scheduling, $data["tags"]);
        }

        return $account_scheduling;
    }

    /**
     * Create the parcels
     *
     * @param array $data
     * @return array
     */ 
    public function createAccountSchedulingParcels(array $data): array
    {
        $data['has_parcels'] = true;

        $account_scheduling = $this->repository->create($data);

        if (isset($data["tags"]) && $data["tags"]) {
            $this->tagService->createAccountSchedulingTag($account_scheduling, $data["tags"]);
        }

        $parcels        = [];
        $total_parcels  = $data['installments_number'];

        $parcel_data = [
            'total_parcels' => $total_parcels,
            'parcel_value'  => round($account_scheduling->value / $data['installments_number'], 2),
            'category_id'   => $account_scheduling->category->id,
        ];

        $date = new DateTime($account_scheduling->due_date);

        for ($parceling = 1; $parceling <= $total_parcels; $parceling++) {
            $parcel_data['due_date']            = $date->format('Y-m-d');
            $parcel_data['description']     = $account_scheduling->description . " {$parceling}/{$total_parcels}" ;           
            $parcel_data['parcel_number']   = $parceling;

            $parcel = $this->repository->createParcels($account_scheduling, $parcel_data);

            if ($account_scheduling->tags) {
                $this->tagService->createParcelTags($parcel, $account_scheduling->tags);
            }

            $parcels[] = $parcel;

            $date = $date->modify('+1 month');
        }

        return $parcels;
    }

    public function update(AccountsScheduling $account_scheduling, $data)
    {
        $data['monthly'] = isset($data['monthly']) && $data['monthly'] ? true : false;

        if (isset($data["tags"])) {
            $this->tagService->createInvoiceEntryTags($account_scheduling, $data["tags"]);
        }
        
        return $this->repository->update($account_scheduling, $data);
    }

    public function delete(AccountsScheduling $account_scheduling)
    {
        if ($account_scheduling->hasParcels()) {
            $this->repository->deleteParcels($account_scheduling);
        }

        $account_scheduling->tags()->sync([]);

        return $this->repository->delete($account_scheduling);
    }

    public function findById($id): ?AccountsScheduling
    {
        return $this->repository->findById($id);
    }

    public function findParcel($parcel_id, $account_scheduling_id): ?Parcel
    {
        return $this->parcelRepository->findParcelsOfAccountsScheduling($account_scheduling_id, $parcel_id);
    }

    /**
     * Get accounts payable/receivable by category type
     *
     * @param string $categoryType
     * @param array $filter
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getAccountsSchedulingsByType($categoryType, array $filter = [])
    {
        $data = [
            'from'  => date('Y-m-01'),
            'to'    => date('Y-m-t')
        ];

        if ($filter && isset($filter['from']) && isset($filter['to'])) {
            $data['from'] = $filter['from'];
            $data['to']   = $filter['to'];
        }

        if (isset($filter['status']) && in_array($filter['status'], ['open', 'paid'])) {
            $data['status'] = $filter['status'];
        }

        $accounts_schedulings = $this->repository->getAccountsSchedulingsByType($categoryType, $data);
       
        $parcels = $this->parcelRepository->getParcelsOfAccountsScheduling($categoryType, $data);

        $all = $accounts_schedulings->concat($parcels);

        return $all->sortBy('due_date');
    }

    /**
     * Makes the bill payment
     *
     * @param Account $account
     * @param Account $account
     * @param array $data
     * @return void
     */
    public function payment(Account $account, $account_scheduling, array $data): void
    {
        $account_scheduling->paid_date = $data['paid_date'];
        $account_scheduling->paid = true;

        $value = $data['value'] ?? $account_scheduling->value;
        
        $entryData = [
            'date'          => $account_scheduling->paid_date,
            'description'   => $account_scheduling->description,
            'value'         => $value,
            'category_id'   => $account_scheduling->category_id,
            'account_id'    => $account->id
        ];

        $accountEntryRepository = app(AccountEntryRepositoryInterface::class);
        
        $entry = $accountEntryRepository->create($entryData);

        if (! $account_scheduling->isParcel()) {
            $entry->accountScheduling()->associate($account_scheduling);
            

            if ($account_scheduling->invoice) {
                $this->updateInvoice($account_scheduling);
            }

            if ($account_scheduling->monthly) {
                $this->repository->createMonthlyPayment($account_scheduling);
            }

            $account_scheduling->value = $value;
            $this->repository->save($account_scheduling);
        } else {            
            $entry->parcel()->associate($account_scheduling);
            $this->parcelRepository->save($account_scheduling);
        }

        $accountEntryRepository->save($entry);
        $this->updateAccountBalance($account, $entry->date);
    }

    /**
     * Cancels the payment and delete the account entry
     *
     * @param mixed $account_scheduling
     * @return bool
     */
    public function cancelPayment($account_scheduling)
    {
        $account        = $account_scheduling->accountEntry->account;
        $payment_date   = $account_scheduling->paid_date;

        if ($account_scheduling->monthly) {
            $this->deleteNextMonthlyAccountScheduling($account_scheduling);
        }

        $this->repository->deleteAccountEntry($account_scheduling);

        $new_data = [
            'paid_date' => null,
            'paid' => false
        ];

        if ($account_scheduling->isParcel()) {
            $this->parcelRepository->update($account_scheduling, $new_data);
        } else {
            $this->repository->update($account_scheduling, $new_data);
        }

        if ($account_scheduling->invoice) {
            $this->updateInvoice($account_scheduling, false);
        } 

        $this->updateAccountBalance($account, $payment_date);
    }

    private function updateAccountBalance($account, $date): void
    {
        $service = app(AccountService::class);
        $service->updateBalance($account, $date);
    }

    private function updateInvoice(AccountsScheduling $account_scheduling, $payment = true): void
    {
        $invoiceRepository = app(InvoiceRepositoryInterface::class);

        $invoiceRepository->update($account_scheduling->invoice, [
            'paid' => $payment ? true : false
        ]);

        $service = app(CardService::class);

        $service->updateCardBalance($account_scheduling->invoice->card);
    }

    /**
     * 
     * @param Illuminate\Database\Eloquent\Collection $items
     * @return array
     */
    public function getTotalForReportByCategoryType($items): array
    {
        $total_paid = 0;
        $total_open = 0;

        foreach ($items as $item) {
            if ($item->isPaid()) {
                $total_paid += $item->value;
            } else {
                $total_open += $item->value;
            }
        }

        return [
            'open' => $total_open,
            'paid' => $total_paid
        ];
    }

    /**
     * Returns the total amount of items
     * 
     * @param Illuminate\Database\Eloquent\Collection $items
     * @return float
     */
    public function getItemsTotalAmount($items): float
    {
        $total = 0;

        foreach ($items as $item) {
            $total += $item->value;
        }

        return $total;
    }

    private function deleteNextMonthlyAccountScheduling($account_scheduling) 
    {
        $next_account_scheduling = $this->repository->getNextAccountScheduling($account_scheduling);

        if ($next_account_scheduling) {
            $next_account_scheduling->delete();
        }
    }
}
