<?php

namespace Database\Seeders;

use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Goal;
use App\Models\Language;
use App\Models\Merchant;
use App\Models\Profile;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserPreference;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class ExpenseAiSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles & Permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // 2. Currencies
        Currency::create(['code' => 'USD', 'symbol' => '$', 'name' => 'US Dollar', 'exchange_rate' => 1.0, 'is_default' => true]);
        Currency::create(['code' => 'EUR', 'symbol' => '€', 'name' => 'Euro', 'exchange_rate' => 0.92]);
        Currency::create(['code' => 'GBP', 'symbol' => '£', 'name' => 'British Pound', 'exchange_rate' => 0.78]);
        Currency::create(['code' => 'INR', 'symbol' => '₹', 'name' => 'Indian Rupee', 'exchange_rate' => 83.5]);

        // 3. Countries
        Country::create(['name' => 'United States', 'code' => 'US', 'phone_code' => '+1', 'flag' => '🇺🇸']);
        Country::create(['name' => 'United Kingdom', 'code' => 'GB', 'phone_code' => '+44', 'flag' => '🇬🇧']);
        Country::create(['name' => 'India', 'code' => 'IN', 'phone_code' => '+91', 'flag' => '🇮🇳']);

        // 4. Languages
        Language::create(['code' => 'en', 'name' => 'English', 'flag' => '🇺🇸', 'is_default' => true]);
        Language::create(['code' => 'es', 'name' => 'Spanish', 'flag' => '🇪🇸']);
        Language::create(['code' => 'hi', 'name' => 'Hindi', 'flag' => '🇮🇳']);

        // 5. Banks
        $chase = Bank::create(['name' => 'Chase Bank', 'code' => 'CHASE', 'logo' => 'chase.png', 'country_code' => 'US']);
        $hdbc = Bank::create(['name' => 'HDFC Bank', 'code' => 'HDFC', 'logo' => 'hdfc.png', 'country_code' => 'IN']);
        $revolut = Bank::create(['name' => 'Revolut Digital', 'code' => 'REVOLUT', 'logo' => 'revolut.png', 'country_code' => 'GB']);

        // 6. Default Categories
        $foodCat = Category::create(['name' => 'Food & Dining', 'slug' => 'food-dining', 'type' => 'expense', 'icon' => 'cake', 'color' => '#ef4444', 'is_system' => true]);
        $shoppingCat = Category::create(['name' => 'Shopping', 'slug' => 'shopping', 'type' => 'expense', 'icon' => 'shopping-bag', 'color' => '#ec4899', 'is_system' => true]);
        $travelCat = Category::create(['name' => 'Travel & Transport', 'slug' => 'travel-transport', 'type' => 'expense', 'icon' => 'paper-airplane', 'color' => '#3b82f6', 'is_system' => true]);
        $utilityCat = Category::create(['name' => 'Utilities & Bills', 'slug' => 'utilities-bills', 'type' => 'expense', 'icon' => 'bolt', 'color' => '#f59e0b', 'is_system' => true]);
        $salaryCat = Category::create(['name' => 'Salary & Earnings', 'slug' => 'salary-earnings', 'type' => 'income', 'icon' => 'currency-dollar', 'color' => '#10b981', 'is_system' => true]);
        $investCat = Category::create(['name' => 'Investments', 'slug' => 'investments', 'type' => 'investment', 'icon' => 'chart-bar', 'color' => '#8b5cf6', 'is_system' => true]);

        // 7. Merchants
        $starbucks = Merchant::create(['name' => 'Starbucks Coffee', 'category_id' => $foodCat->id, 'is_verified' => true]);
        $amazon = Merchant::create(['name' => 'Amazon Store', 'category_id' => $shoppingCat->id, 'is_verified' => true]);
        $uber = Merchant::create(['name' => 'Uber Rides', 'category_id' => $travelCat->id, 'is_verified' => true]);
        $netflix = Merchant::create(['name' => 'Netflix Premium', 'category_id' => $utilityCat->id, 'is_verified' => true]);

        // 8. Demo Users
        // Admin User
        $admin = User::create([
            'name' => 'Enterprise Admin',
            'email' => 'admin@expenseai.test',
            'phone' => '+18005550199',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        $admin->assignRole($adminRole);
        Profile::create(['user_id' => $admin->id, 'monthly_income_target' => 12000]);
        UserPreference::create(['user_id' => $admin->id, 'theme' => 'dark']);

        // Demo User 1 (Alex Morgan)
        $user = User::create([
            'name' => 'Alex Morgan',
            'email' => 'alex@expenseai.test',
            'phone' => '+18005550122',
            'password' => Hash::make('password'),
            'role' => 'user',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        $user->assignRole($userRole);
        Profile::create(['user_id' => $user->id, 'employment_type' => 'employed', 'monthly_income_target' => 7500]);
        UserPreference::create(['user_id' => $user->id, 'theme' => 'dark']);

        // Demo Client 2 (Sarah Jenkins)
        $client2 = User::create([
            'name' => 'Sarah Jenkins',
            'email' => 'sarah@expenseai.test',
            'phone' => '+19876543210',
            'password' => Hash::make('password'),
            'role' => 'user',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        $client2->assignRole($userRole);
        Profile::create(['user_id' => $client2->id, 'employment_type' => 'freelancer', 'monthly_income_target' => 9200]);
        UserPreference::create(['user_id' => $client2->id, 'theme' => 'dark']);

        // Demo Client 3 (Rahul Sharma)
        $client3 = User::create([
            'name' => 'Rahul Sharma',
            'email' => 'rahul@expenseai.test',
            'phone' => '+919876543210',
            'password' => Hash::make('password'),
            'role' => 'user',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        $client3->assignRole($userRole);
        Profile::create(['user_id' => $client3->id, 'employment_type' => 'business', 'monthly_income_target' => 15000]);
        UserPreference::create(['user_id' => $client3->id, 'theme' => 'dark']);

        // User Accounts & Wallets
        $chaseAccount = BankAccount::create([
            'user_id' => $user->id,
            'bank_id' => $chase->id,
            'account_number' => '•••• 4892',
            'account_name' => 'Chase Premier Checking',
            'account_type' => 'savings',
            'balance' => 14850.50,
            'currency' => 'USD',
            'color' => '#6366f1',
        ]);

        $cryptoWallet = Wallet::create([
            'user_id' => $user->id,
            'name' => 'Digital Crypto Vault',
            'type' => 'crypto',
            'balance' => 3200.00,
            'currency' => 'USD',
            'color' => '#10b981',
        ]);

        // Client 2 Accounts & Transactions
        $sarahAccount = BankAccount::create([
            'user_id' => $client2->id,
            'bank_id' => $revolut->id,
            'account_number' => '•••• 7712',
            'account_name' => 'Revolut Freelance Account',
            'account_type' => 'checking',
            'balance' => 28400.00,
            'currency' => 'USD',
            'color' => '#a855f7',
        ]);

        Transaction::create([
            'user_id' => $client2->id,
            'bank_account_id' => $sarahAccount->id,
            'category_id' => $salaryCat->id,
            'type' => 'income',
            'amount' => 4500.00,
            'net_amount' => 4500.00,
            'currency' => 'USD',
            'status' => 'completed',
            'transaction_date' => Carbon::now()->subDays(2),
            'notes' => 'Client Invoice Payment - Design Project',
            'payment_method' => 'Stripe Direct',
        ]);

        Transaction::create([
            'user_id' => $client2->id,
            'bank_account_id' => $sarahAccount->id,
            'category_id' => $foodCat->id,
            'merchant_id' => $starbucks->id,
            'type' => 'expense',
            'amount' => 28.50,
            'net_amount' => 28.50,
            'currency' => 'USD',
            'status' => 'completed',
            'transaction_date' => Carbon::now(),
            'notes' => 'Team Lunch & Coffee',
            'payment_method' => 'Credit Card',
        ]);

        // Client 3 Accounts & Transactions
        $rahulAccount = BankAccount::create([
            'user_id' => $client3->id,
            'bank_id' => $hdbc->id,
            'account_number' => '•••• 9931',
            'account_name' => 'HDFC Corporate Account',
            'account_type' => 'checking',
            'balance' => 64500.00,
            'currency' => 'USD',
            'color' => '#10b981',
        ]);

        Transaction::create([
            'user_id' => $client3->id,
            'bank_account_id' => $rahulAccount->id,
            'category_id' => $travelCat->id,
            'merchant_id' => $uber->id,
            'type' => 'expense',
            'amount' => 85.00,
            'net_amount' => 85.00,
            'currency' => 'USD',
            'status' => 'completed',
            'transaction_date' => Carbon::now(),
            'notes' => 'Client Meeting Travel',
            'payment_method' => 'UPI',
        ]);

        // 9. Demo Transactions
        // Salary
        Transaction::create([
            'user_id' => $user->id,
            'bank_account_id' => $chaseAccount->id,
            'category_id' => $salaryCat->id,
            'type' => 'salary',
            'amount' => 7500.00,
            'net_amount' => 7500.00,
            'currency' => 'USD',
            'status' => 'completed',
            'transaction_date' => Carbon::now()->startOfMonth(),
            'notes' => 'Monthly Payroll Credit',
            'payment_method' => 'NEFT Direct Deposit',
        ]);

        // Expenses
        Transaction::create([
            'user_id' => $user->id,
            'bank_account_id' => $chaseAccount->id,
            'category_id' => $foodCat->id,
            'merchant_id' => $starbucks->id,
            'type' => 'expense',
            'amount' => 14.50,
            'net_amount' => 14.50,
            'currency' => 'USD',
            'status' => 'completed',
            'transaction_date' => Carbon::now()->subDays(1),
            'notes' => 'Morning Espresso & Croissant',
            'payment_method' => 'Credit Card',
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'bank_account_id' => $chaseAccount->id,
            'category_id' => $shoppingCat->id,
            'merchant_id' => $amazon->id,
            'type' => 'expense',
            'amount' => 129.99,
            'net_amount' => 129.99,
            'currency' => 'USD',
            'status' => 'completed',
            'transaction_date' => Carbon::now()->subDays(3),
            'notes' => 'Ergonomic Desk Monitor Stand',
            'payment_method' => 'Credit Card',
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'bank_account_id' => $chaseAccount->id,
            'category_id' => $travelCat->id,
            'merchant_id' => $uber->id,
            'type' => 'expense',
            'amount' => 42.80,
            'net_amount' => 42.80,
            'currency' => 'USD',
            'status' => 'completed',
            'transaction_date' => Carbon::now()->subDays(5),
            'notes' => 'Airport Express Shuttle',
            'payment_method' => 'UPI',
        ]);

        // 10. Demo Budgets
        Budget::create([
            'user_id' => $user->id,
            'category_id' => $foodCat->id,
            'period' => 'monthly',
            'amount' => 500.00,
            'spent' => 240.50,
            'threshold_percentage' => 80,
            'is_alert_enabled' => true,
        ]);

        Budget::create([
            'user_id' => $user->id,
            'category_id' => $shoppingCat->id,
            'period' => 'monthly',
            'amount' => 800.00,
            'spent' => 450.00,
            'threshold_percentage' => 80,
            'is_alert_enabled' => true,
        ]);

        // 11. Demo Goals
        Goal::create([
            'user_id' => $user->id,
            'title' => 'Japan Vacation 2027',
            'target_amount' => 5000.00,
            'current_amount' => 2450.00,
            'deadline' => Carbon::now()->addMonths(8),
            'category' => 'vacation',
            'status' => 'active',
            'icon' => 'paper-airplane',
            'color' => '#3b82f6',
        ]);

        Goal::create([
            'user_id' => $user->id,
            'title' => 'Emergency Fund',
            'target_amount' => 10000.00,
            'current_amount' => 7200.00,
            'deadline' => Carbon::now()->addMonths(12),
            'category' => 'emergency',
            'status' => 'active',
            'icon' => 'shield-check',
            'color' => '#10b981',
        ]);

        // 12. Subscriptions
        Subscription::create([
            'user_id' => $user->id,
            'merchant_id' => $netflix->id,
            'category_id' => $utilityCat->id,
            'name' => 'Netflix 4K Ultra HD',
            'amount' => 19.99,
            'billing_cycle' => 'monthly',
            'next_billing_date' => Carbon::now()->addDays(12),
            'auto_renew' => true,
            'status' => 'active',
        ]);
    }
}
