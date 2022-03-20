<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\Invoice;
use App\Models\Category;
use App\Models\AccountEntry;
use App\Models\InvoiceEntry;
use Laravel\Sanctum\Sanctum;
use App\Models\AccountsScheduling;

class ReportTest extends TestCase {
    protected $user;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }
    
    public function testAccountReportWhenUnauthenticatedUser()
    {
        $response = $this->getJson('/api/reports/accounts');

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testAccountReportWithoutParameter()
    {
        Sanctum::actingAs(
            $this->user
        );
        
        $response = $this->getJson('/api/reports/accounts');

        $response->assertStatus(200)
            ->assertJsonCount(0);            
    }

    public function testGetOpenAccountPayablesReport()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = Category::factory()->create(['type' => Category::EXPENSE]);

        AccountsScheduling::factory()->count(4)->create([
            'paid'          => false,
            'category_id'   => $category->id,
            'due_date'      => now()->modify('+4 days')->format('Y-m-d')
        ]);

        $from = now()->format('Y-m-d');
        $to = now()->modify('+5 days')->format('Y-m-d');
        $status = 'open';
        
        $response = $this->getJson("/api/reports/accounts?from=$from&to=$to&status=$status");

        $response->assertStatus(200)
            ->assertJsonCount(4, 'payables.items')
            ->assertJsonCount(0, 'receivables.items');           
    }

    public function testGetOpenAccountReceivablesReport()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = Category::factory()->create(['type' => Category::INCOME]);

        AccountsScheduling::factory()->count(4)->create([
            'paid'          => false,
            'category_id'   => $category->id,
            'due_date'      => now()->modify('+4 days')->format('Y-m-d')
        ]);

        $from = now()->format('Y-m-d');
        $to = now()->modify('+5 days')->format('Y-m-d');
        $status = 'open';
        
        $response = $this->getJson("/api/reports/accounts?from=$from&to=$to&status=$status");

        $response->assertStatus(200)
            ->assertJsonCount(0, 'payables.items')
            ->assertJsonCount(4, 'receivables.items')
            ->assertJsonPath('invoices.total', 0);
    }

    public function testReportAccountWithoutAnyData ()
    {
        Sanctum::actingAs(
            $this->user
        );

        $from = now()->format('Y-m-d');
        $to = now()->modify('+5 days')->format('Y-m-d');
        $status = 'open';
        
        $response = $this->getJson("/api/reports/accounts?from=$from&to=$to&status=$status");

        $response->assertStatus(200)
            ->assertJsonCount(0, 'payables.items')
            ->assertJsonCount(0, 'receivables.items')
            ->assertJsonPath('invoices.total', 0);
    }

    public function testTotalInvoiceReport ()
    {
        Sanctum::actingAs(
            $this->user
        );

        Invoice::factory()->create([
            'due_date'      => now()->modify('+15 days')->format('Y-m-d'),
            'closing_date'  => now()->modify('+7 days')->format('Y-m-d'),
            'amount'        => 151.50
        ]);

        $from = now()->format('Y-m-d');
        $to = now()->modify('+20 days')->format('Y-m-d');
        $status = 'open';

        $response = $this->getJson("/api/reports/accounts?from=$from&to=$to&status=$status");

        $response->assertStatus(200)
            ->assertJsonCount(0, 'payables.items')
            ->assertJsonCount(0, 'receivables.items')
            ->assertJsonPath('invoices.total', 151.50);
    }

    public function testGetOpenAccountReceivablesReports()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = Category::factory()->create(['type' => Category::INCOME]);

        AccountsScheduling::factory()->count(4)->create([
            'paid'          => false,
            'category_id'   => $category->id,
            'due_date'      => now()->modify('+4 days')->format('Y-m-d')
        ]);

        $from = now()->format('Y-m-d');
        $to = now()->modify('+5 days')->format('Y-m-d');
        $status = 'open';
        
        $response = $this->getJson("/api/reports/accounts?from=$from&to=$to&status=$status");

        $response->assertStatus(200)
            ->assertJsonCount(0, 'payables.items')
            ->assertJsonCount(4, 'receivables.items');           
    }

    public function testGetPaidAccountReceivablesReports()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = Category::factory()->create(['type' => Category::INCOME]);

        AccountsScheduling::factory()->count(4)->create([
            'paid'          => true,
            'category_id'   => $category->id,
            'due_date'      => now()->modify('+4 days')->format('Y-m-d')
        ]);

        AccountsScheduling::factory()->count(4)->create([
            'paid'          => false,
            'category_id'   => $category->id,
            'due_date'      => now()->modify('+4 days')->format('Y-m-d')
        ]);

        $from = now()->format('Y-m-d');
        $to = now()->modify('+5 days')->format('Y-m-d');
        $status = 'paid';
        
        $response = $this->getJson("/api/reports/accounts?from=$from&to=$to&status=$status");

        $response->assertStatus(200)
            ->assertJsonCount(0, 'payables.items')
            ->assertJsonCount(4, 'receivables.items');           
    }

    public function testTotalAccountByCategoryReportWhenUnauthenticatedUser()
    {
        $response = $this->getJson('/api/reports/total-account-by-category');

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testTotalByAccountCategoryReportWithoutParameter()
    {
        Sanctum::actingAs(
            $this->user
        );
        
        $response = $this->getJson('/api/reports/total-account-by-category');

        $response->assertStatus(200)
            ->assertJsonCount(0);            
    }

    public function testGetExpenseCategoriesOnTotalAccountByCategoryReport()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category1 = Category::factory()->create(['type' => Category::EXPENSE]);
        $category2 = Category::factory()->create(['type' => Category::EXPENSE]);

        AccountEntry::factory()->count(2)->create([
            'date'        => now()->modify('+4 days')->format('Y-m-d'),
            'category_id' => $category1->id,
            'value'       => 15  
        ]);
        AccountEntry::factory()->count(2)->create([
            'date'        => now()->modify('+4 days')->format('Y-m-d'),
            'category_id' => $category2->id,
            'value'       => 15  
        ]);

        $from = now()->format('Y-m-d');
        $to = now()->modify('+5 days')->format('Y-m-d');
        $status = 'open';
        
        $response = $this->getJson("/api/reports/total-account-by-category?from=$from&to=$to&status=$status");

        $response->assertStatus(200)
            ->assertJsonCount(0, 'incomes')
            ->assertJsonCount(2, 'expenses');
    }

    public function testGetIncomeCategoriesOnTotalAccountByCategoryReport()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category1 = Category::factory()->create(['type' => Category::INCOME]);
        $category2 = Category::factory()->create(['type' => Category::INCOME]);

        AccountEntry::factory()->count(2)->create([
            'date'        => now()->modify('+4 days')->format('Y-m-d'),
            'category_id' => $category1->id,
            'value'       => 15  
        ]);
        AccountEntry::factory()->count(2)->create([
            'date'        => now()->modify('+4 days')->format('Y-m-d'),
            'category_id' => $category2->id,
            'value'       => 15  
        ]);

        $from = now()->format('Y-m-d');
        $to = now()->modify('+5 days')->format('Y-m-d');
        $status = 'open';
        
        $response = $this->getJson("/api/reports/total-account-by-category?from=$from&to=$to&status=$status");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'incomes')
            ->assertJsonCount(0, 'expenses');
    }

    public function testGetCategoriesFromASelectedAccount()
    {
        Sanctum::actingAs(
            $this->user
        );

        $account1 = Account::factory()->create();
        $account2 = Account::factory()->create();

        $category1 = Category::factory()->create(['type' => Category::INCOME]);
        $category2 = Category::factory()->create(['type' => Category::EXPENSE]);

        AccountEntry::factory()->count(2)->create([
            'date'        => now()->modify('+4 days')->format('Y-m-d'),
            'category_id' => $category1->id,
            'value'       => 15,
            'account_id'  => $account1->id
        ]);
        AccountEntry::factory()->count(2)->create([
            'date'        => now()->modify('+4 days')->format('Y-m-d'),
            'category_id' => $category2->id,
            'value'       => 15,
            'account_id'  => $account2->id
        ]);

        $from = now()->format('Y-m-d');
        $to = now()->modify('+5 days')->format('Y-m-d');
        $status = 'open';
        
        $response = $this->getJson("/api/reports/total-account-by-category?from=$from&to=$to&status=$status&account_id=$account1->id");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'incomes')
            ->assertJsonCount(0, 'expenses');
    }

    public function testTotalCreditByCategoryReportWhenUnauthenticatedUser()
    {
        $response = $this->getJson('/api/reports/total-credit-by-category');

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testTotalCreditByAccountCategoryReportWithoutParameter()
    {
        Sanctum::actingAs(
            $this->user
        );
        
        $response = $this->getJson('/api/reports/total-credit-by-category');

        $response->assertStatus(200)
            ->assertJsonCount(0);            
    }



    public function testGetCreditCardCategoriesOnTotalCreditByCategoryReport()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category1 = Category::factory()->create(['type' => Category::EXPENSE]);
        $category2 = Category::factory()->create(['type' => Category::EXPENSE]);

        InvoiceEntry::factory()->count(2)->create([
            'date'        => now()->modify('+4 days')->format('Y-m-d'),
            'category_id' => $category1->id,
            'value'       => 15  
        ]);
        InvoiceEntry::factory()->count(2)->create([
            'date'        => now()->modify('+4 days')->format('Y-m-d'),
            'category_id' => $category2->id,
            'value'       => 15  
        ]);

        $from = now()->format('Y-m-d');
        $to = now()->modify('+5 days')->format('Y-m-d');
        $status = 'open';
        
        $response = $this->getJson("/api/reports/total-credit-by-category?from=$from&to=$to&status=$status");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function testTotalByCategoryDetailsWhenUnauthenticatedUser()
    {
        $response = $this->getJson('/api/reports/total-by-category/details');

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testTotalByCategoryDetailedWithoutParameter()
    {
        Sanctum::actingAs(
            $this->user
        );
        
        $response = $this->getJson('/api/reports/total-by-category/details');

        $response->assertStatus(404);          
    }

    public function testGetTotalByCategoryDetailedSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = Category::factory()->create(['type' => Category::EXPENSE]);

        AccountEntry::factory()->count(5)->create([
            'date'        => now()->modify('+4 days')->format('Y-m-d'),
            'category_id' => $category->id,
        ]);

        $from = now()->format('Y-m-d');
        $to = now()->modify('+5 days')->format('Y-m-d');
        $type = 'account';
        
        $response = $this->getJson("/api/reports/total-by-category/details?category_id=$category->id&from=$from&to=$to&type=$type");

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    public function testGetTotalByCategoryDetailedFromAGivenAccountId()
    {
        Sanctum::actingAs(
            $this->user
        );


        $account1 = Account::factory()->create();
        $account2 = Account::factory()->create();

        $category = Category::factory()->create(['type' => Category::EXPENSE]);

        AccountEntry::factory()->create([
            'date'        => now()->modify('+4 days')->format('Y-m-d'),
            'category_id' => $category->id,
            'value'       => 15,
            'account_id'  => $account1->id
        ]);
        AccountEntry::factory()->create([
            'date'        => now()->modify('+4 days')->format('Y-m-d'),
            'category_id' => $category->id,
            'value'       => 15,
            'account_id'  => $account2->id
        ]);

        $from = now()->format('Y-m-d');
        $to = now()->modify('+5 days')->format('Y-m-d');
        $type = 'account';
        
        $response = $this->getJson("/api/reports/total-by-category/details?category_id=$category->id&from=$from&to=$to&type=$type&account_id=$account1->id");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }
}