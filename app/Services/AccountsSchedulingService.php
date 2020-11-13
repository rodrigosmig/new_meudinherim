<?php

namespace App\Services;

use DateTime;
use App\Models\User;
use App\Models\Account;
use App\Models\AccountsScheduling;
use App\Services\AccountEntryService;
use App\Exceptions\AccountsPayableIsNotPaidException;
use App\Exceptions\AccountsPayableIsAlreadyPaidException;

class AccountsSchedulingService
{
    protected $account_scheduling;

    public function __construct(AccountsScheduling $account_scheduling)
    {
        $this->account_scheduling = $account_scheduling;
    }

    public function store(array $data)
    {
        $data['monthly'] = isset($data['monthly']) ? true : false;

        if (isset($data['installment']) && isset($data['installments_number']) && $data['installments_number'] > 1) {
            return $this->createInstallments($data);
        } 

        return $this->account_scheduling->create($data);
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

    public function update($id, array $data)
    {
        $account_scheduling = $this->findById($id);

        if (! $account_scheduling) {
            return false;
        }

        $data['monthly'] = isset($data['monthly']) ? true : false;
        
        $account_scheduling->update($data);
        
        return $account_scheduling;
    }

    public function delete($id)
    {
        $account_scheduling = $this->findById($id);

        if (! $account_scheduling) {
            return false;
        }

        return $account_scheduling->delete();
    }

    public function findById($id)
    {
        return $this->account_scheduling->find($id);
    }

    /**
     * Get accounts payable/receivable by category type
     *
     * @param string $categoryType
     * @param array $filter
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getAccountsSchedulingsByType($categoryType, array $filter = null)
    {
        $from = date('Y-m-1');
        $to = date('Y-m-t');

        if ($filter && isset($filter['from']) && isset($filter['to'])) {
            $from = $filter['from'];
            $to = $filter['to'];
        }

        $result = $this->account_scheduling::select('accounts_schedulings.*')
            ->join('categories', 'categories.id', '=', 'accounts_schedulings.category_id')
            ->where('categories.type', $categoryType)
            ->where('due_date', '>=', $from)
            ->where('due_date', '<=', $to)
            ->orderBy('due_date');

        
        if (isset($filter['status']) && in_array($filter['status'], ['open', 'paid'])) {
            $status = $filter['status'] == 'open' ? false : true;
            $result->where('paid', $status);
        }
       
        return $result->get();
    }

    /**
     * Makes the bill payment
     *
     * @param Account $account
     * @param array $data
     * @return bool
     * @throws AccountsPayableIsAlreadyPaidException
     */
    public function payment(Account $account, array $data): bool
    {
        $account_scheduling = $this->findById($data['id']);

        if (! $account_scheduling) {
            return false;
        }

        if ($account_scheduling->isPaid()) {
            throw new AccountsPayableIsAlreadyPaidException(__('messages.account_scheduling.payable_is_paid'));
        }

        $account_scheduling->paid_date = $data['paid_date'];
        $account_scheduling->paid = true;
        
        $entryData = [
            'date'          => $account_scheduling->paid_date,
            'description'   => $account_scheduling->description,
            'value'         => $account_scheduling->value,
            'category_id'   => $account_scheduling->category_id,
        ];

        $accountEntryService = app(AccountEntryService::class);
        
        $entry = $accountEntryService->make($account, $entryData);        
        $entry->accountScheduling()->associate($account_scheduling);

        if ($account_scheduling->invoice) {
            $account_scheduling->invoice()->update([
                'paid' => true
            ]);
        }

        if ($account_scheduling->monthly) {
            $this->createMonthlyPayment($account_scheduling);
        }

        $entry->save();
        $account_scheduling->save();
        
        return true;
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
     * @throws AccountsPayableIsNotPaidException
     */
    public function cancelPayment($payable): bool
    {
        if (! $payable->isPaid()) {
            throw new AccountsPayableIsNotPaidException(__('messages.account_scheduling.payable_is_not_paid'));
        }

        $account        = $payable->accountEntry->account;
        $payment_date   = $payable->paid_date;

        $payable->accountEntry()->delete();
        $payable->paid_date = null;
        $payable->paid = false;

        if ($payable->invoice) {
            $payable->invoice()->update([
                'paid' => false
            ]);
        } 

        $payable->save();

        $this->updateAccountBalance($account, $payment_date);
               
        return true;
    }

    private function updateAccountBalance($account, $date) 
    {
        $service = app(AccountService::class);
        $service->updateBalance($account, $date);
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
    public function getAccountsByUserForCron(User $user, $categoryType)
    {
        return $this->account_scheduling::select('accounts_schedulings.*')
            ->withoutGlobalScopes()
            ->join('categories', 'categories.id', '=', 'accounts_schedulings.category_id')
            ->where('categories.type', $categoryType)
            ->where('due_date', now()->format('Y-m-d'))
            ->where('paid', false)
            ->where('accounts_schedulings.user_id', $user->id)
            ->get();
    }
}
