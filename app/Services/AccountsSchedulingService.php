<?php

namespace App\Services;

use DateTime;
use App\Models\User;
use App\Models\Account;
use App\Services\CardService;
use App\Models\AccountsScheduling;
use App\Exceptions\AccountIsNotPaidException;
use App\Repositories\Interfaces\AccountEntryRepositoryInterface;
use App\Repositories\Interfaces\AccountsSchedulingRepositoryInterface;
use App\Repositories\Interfaces\InvoiceRepositoryInterface;

class AccountsSchedulingService
{
    protected $repository;

    public function __construct(AccountsSchedulingRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data)
    {
        $data['monthly'] = isset($data['monthly']) ? true : false;

        if (isset($data['installment']) && isset($data['installments_number']) && $data['installments_number'] > 1) {
            return $this->createInstallments($data);
        } 

        return $this->repository->create($data);
    }

    /**
     * Create the installments
     *
     * @param array $data
     * @return bool
     */ 
    public function createInstallments(array $data): bool
    {
        $date                   = new DateTime($data['due_date']);
        $total                  = $data['value'];
        $installments_number    = $data['installments_number'];
        $installment_value      = number_format($total / $installments_number, 2);
        $description_default    = $data['description'];

        $data['value'] = $installment_value;

        for ($i = 1; $i <= $installments_number; $i++) {
            $data['due_date']       = $date->format('Y-m-d');
            $data['description']    = $description_default . " {$i}/{$installments_number}" ;           

            $entry = $this->account_scheduling->create($data);

            if (! $entry) {
                return false;
            }

            $date = $date->modify('+1 month');
        }

        return true;
    }

    public function update(AccountsScheduling $account_scheduling, $data)
    {
        $data['monthly'] = isset($data['monthly']) ? true : false;
        
        return $this->repository->update($account_scheduling, $data);
    }

    public function delete(AccountsScheduling $account_scheduling)
    {
        return $this->repository->delete($account_scheduling);
    }

    public function findById($id)
    {
        return $this->repository->findById($id);
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
        $range_date = [
            'from'  => date('Y-m-01'),
            'to'    => date('Y-m-t')
        ];

        if ($filter && isset($filter['from']) && isset($filter['to'])) {
            $filter['from'] = $range_date['from'];
            $filter['to']   = $range_date['to'];
        }

        return $this->repository->getAccountsSchedulingsByType($categoryType, $range_date);
    }

    /**
     * Makes the bill payment
     *
     * @param Account $account
     * @param array $data
     * @return void
     */
    public function payment(Account $account, AccountsScheduling $account_scheduling, array $data): void
    {
        $account_scheduling->paid_date = $data['paid_date'];
        $account_scheduling->paid = true;
        
        $entryData = [
            'date'          => $account_scheduling->paid_date,
            'description'   => $account_scheduling->description,
            'value'         => $account_scheduling->value,
            'category_id'   => $account_scheduling->category_id,
        ];

        $accountEntryRepository = app(AccountEntryRepositoryInterface::class);
        
        $entry = $accountEntryRepository->create($account, $entryData);        
        $entry->accountScheduling()->associate($account_scheduling);

        if ($account_scheduling->invoice) {
            $this->updateInvoice($account_scheduling);
        }

        if ($account_scheduling->monthly) {
            $this->createMonthlyPayment($account_scheduling);
        }

        $this->accountEntryRepository->save($entry);
        $this->repository->save($account_scheduling);

        $this->updateAccountBalance($account, $entry->date);
    }

    /**
     * Creates the account payables to next month
     *
     * @param AccountsScheduling $payable
     * @return void
     */
    public function createMonthlyPayment(AccountsScheduling $payable)
    {
        $date = new DateTime($payable->due_date);
        $next_month = $date->modify('+1 month');

        $this->account_scheduling->create([
            'due_date'      => $next_month->format('Y-m-d'),
            'description'   => $payable->description,
            'value'         => $payable->value,
            'category_id'   => $payable->category_id,
            'monthly'       => $payable->monthly
        ]);
    }

    /**
     * Cancels the payment and delete the account entry
     *
     * @param AccountsScheduling $payable
     * @return bool
     * @throws AccountIsNotPaidException
     */
    public function cancelPayment($account_scheduling): bool
    {
        $account        = $account_scheduling->accountEntry->account;
        $payment_date   = $account_scheduling->paid_date;

        $this->repository->deleteAccountEntry($account_scheduling);

        $new_data = [
            'paid_date' => null,
            'paid' => false
        ];

        $this->repository->update($account_scheduling, $new_data);

        if ($account_scheduling->invoice) {
            $this->updateInvoice($account_scheduling, false);
        } 

        $this->updateAccountBalance($account, $payment_date);
               
        return true;
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
     * Returns accounts scheduling for a given category type and a given user
     * for the current date
     * 
     * @param User $user
     * @param int $categoryType
     * @return Illuminate\Database\Eloquent\Collection
     */
    /* public function getAccountsByUserForCron(User $user, $categoryType)
    {
        return $this->account_scheduling::select('accounts_schedulings.*')
            ->withoutGlobalScopes()
            ->join('categories', 'categories.id', '=', 'accounts_schedulings.category_id')
            ->where('categories.type', $categoryType)
            ->where('due_date', now()->format('Y-m-d'))
            ->where('paid', false)
            ->where('accounts_schedulings.user_id', $user->id)
            ->get();
    } */
}
