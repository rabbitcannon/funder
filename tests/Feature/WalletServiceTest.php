<?php

namespace Tests\Feature;

use Tests\TestCase;
use Eos\Common\WalletService;

class WalletServiceTest extends TestCase
{

    static $player = ['registrar_id'=> '1',
        'firstname' => 'Joe',
        'lastname' => 'Blow',
        'email' => 'joe@blow.com',
        'phone' => '2223334444'];
    static $agent = ['agent_id' => '3',
        'email' => 'secret@agentman.com'];
    static $account_id = 0;
    static $winnings_balance = 0;
    static $wagering_balance = 0;

    public function testCreateMissingWalletAccounts()
    {
        print( PHP_EOL."test_we_can_create_any_missing_wallet_accounts".PHP_EOL );
        $svc = new WalletService();
        $response = $svc->createPlayerAccounts( self::$player, self::$agent );
        self::assertGreaterThanOrEqual(2,count($response) );
    }

    public function testWalletGetAccounts()
    {
        print( PHP_EOL."test_we_can_retrieve_accounts".PHP_EOL );
        $svc = new WalletService();
        $response = $svc->getAccounts( self::$player );
        self::assertGreaterThanOrEqual(1, count($response));
        self::assertEquals($response[0]->player_id, 1);
        self::assertGreaterThanOrEqual(1, $response[0]->id);
        self::$account_id = $response[0]->id;
        $winnings_amount_found = false;
        $wagering_amount_found = false;
        foreach( $response as $account )
        {
            if( $account->name == 'winnings' )
            {
                self::$winnings_balance = $account->balance;
                $winnings_amount_found = true;
            }
            if( $account->name == 'wagering' )
            {
                self::$wagering_balance = $account->balance;
                $wagering_amount_found = true;
            }
        }

        self::assertTrue( $winnings_amount_found && $wagering_amount_found);
    }

    public function testWalletGetAccountTransactions()
    {
        print( PHP_EOL."test_we_can_retrieve_transactions_on_an_account".PHP_EOL );
        $svc = new WalletService();
        $response = $svc->getAccountHistory( self::$account_id, [], [], self::$player );
        self::assertGreaterThanOrEqual(1,count($response->Transactions) );
        foreach( $response->Transactions as $transaction)
        {
            self::assertArrayHasKey('wallet_event_id',$transaction );
            self::assertArrayHasKey('transaction_id',$transaction );
            self::assertArrayHasKey('wallet_event_id',$transaction );
            self::assertArrayHasKey('event_type',$transaction );
            self::assertArrayHasKey('event_state',$transaction );
        }
    }

    /**
     * we do four administrative adjustments here: add $4.50 to winnings, then remove it; then same to wagering.
     * at the end, there should be no effect on balances.
     */

    public function testDoAdministrativeDepositToWinnings()
    {
        print( PHP_EOL."test_we_can_administrative_deposit_to_winnings".PHP_EOL );
        $svc = new WalletService();
        $response = $svc->agentAdministrativeDeposit(450, 'winnings', 'service testing', null, self::$player, self::$agent );


        if ($response->name == 'winnings') {
            self::assertTrue(self::$winnings_balance + 450 == $response->balance);
        }

    }

    public function testDoAdministrativeWithdrawFromWinnings()
    {
        print( PHP_EOL."test_we_can_administrative_withdraw_from_winnings".PHP_EOL );
        $svc = new WalletService();
        $response = $svc->agentAdministrativeWithdrawal(450, 'winnings', 'service testing', null, self::$player, self::$agent );

        if ($response->name == 'winnings') {
            self::assertTrue(self::$winnings_balance == $response->balance);
        }

    }


    public function testDoAdministrativeDepositToWagering()
    {
        print( PHP_EOL."test_we_can_administrative_deposit_to_wagering".PHP_EOL );
        $svc = new WalletService();
        $response = $svc->agentAdministrativeDeposit(450, 'wagering', 'service testing', null, self::$player, self::$agent );

        if ($response->name == 'wagering') {
            self::assertTrue(self::$wagering_balance + 450 == $response->balance);
        }

    }

    public function testDoAdministrativeWithdrawFromWagering()
    {
        print( PHP_EOL."test_we_can_administrative_withdraw_from_wagering".PHP_EOL );
        $svc = new WalletService();
        $response = $svc->agentAdministrativeWithdrawal(450, 'wagering', 'service testing', null, self::$player, self::$agent );

        if ($response->name == 'wagering') {
            self::assertTrue(self::$wagering_balance == $response->balance);
        }

    }

    /**
     * check request testing. We drain the account by half, then drain the rest, then use an admin adjust to restore original winnings balance.
     * this WILL cause some bogus check requests to be generated
     */
    public function testDoCheckRequestPartial()
    {
        print( PHP_EOL."test_we_can_request_check_partial_amount".PHP_EOL );
        $svc = new WalletService();
        $amount =  intval(self::$winnings_balance / 2);
        $response = $svc->requestCheck($amount, 'winnings', 'service testing', self::$player );
        print("Check Req: ". json_encode($response));
        if ($response->name == 'winnings') {
            self::assertTrue(self::$winnings_balance == $response->balance + $amount);
        }
    }

    public function testDoCheckRequestFull()
    {
        print( PHP_EOL."test_we_can_request_check_full_amount".PHP_EOL );
        $svc = new WalletService();
        $response = $svc->requestCheck(0, 'winnings', 'service testing', self::$player );
        print("Check Req: ". json_encode($response));
        if ($response->name == 'winnings') {
            self::assertTrue($response->balance == 0);
        }

        // now let's put back the whole amount of the player's winnings
        $response = $svc->agentAdministrativeDeposit(self::$winnings_balance, 'winnings', 'service testing', null, self::$player, self::$agent );

        if ($response->name == 'winnings') {
            self::assertTrue(self::$winnings_balance == $response->balance);
        }

    }

}