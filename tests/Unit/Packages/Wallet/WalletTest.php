<?php

/*
 * +----------------------------------------------------------------------+
 * |                          ThinkSNS Plus                               |
 * +----------------------------------------------------------------------+
 * | Copyright (c) 2017 Chengdu ZhiYiChuangXiang Technology Co., Ltd.     |
 * +----------------------------------------------------------------------+
 * | This source file is subject to version 2.0 of the Apache license,    |
 * | that is bundled with this package in the file LICENSE, and is        |
 * | available through the world-wide-web at the following url:           |
 * | http://www.apache.org/licenses/LICENSE-2.0.html                      |
 * +----------------------------------------------------------------------+
 * | Author: Slim Kit Group <master@zhiyicx.com>                          |
 * | Homepage: www.thinksns.com                                           |
 * +----------------------------------------------------------------------+
 */

namespace Zhiyi\Plus\Tests\Unit\Packages\Wallet;

use Zhiyi\Plus\Tests\TestCase;
use Zhiyi\Plus\Packages\Wallet\Wallet;
use Zhiyi\Plus\Models\User as UserModel;
use Zhiyi\Plus\Models\NewWallet as WalletModel;

class WalletTest extends TestCase
{
    /**
     * Test setUser method.
     *
     * @expectedException \Exception
     * @expectedExceptionMessage 传递的不是一个用户
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function testSetUser()
    {
        $user = factory(UserModel::class)->make();
        $user->id = 1;

        // Create a wallet mock.
        $wallet = $this->getMockBuilder(TestWalletSetUser::class)
                       ->setMethods(['userFindOrFail', 'resolveWallet'])
                       ->getMock();
        $wallet->expects($this->exactly(1))
               ->method('userFindOrFail')
               ->will($this->returnValue($user));
        $wallet->expects($this->exactly(2))
               ->method('resolveWallet')
               ->will($this->returnArgument(0));

        $wallet->setUser($user->id);
        $this->assertInstanceOf(UserModel::class, $wallet->getUser());
        $this->assertSame($user->id, $wallet->getUser()->id);

        $wallet->setUser($user);
        $this->assertSame($user->id, $wallet->getUser()->id);

        // @expectedException \Exception
        $wallet->setUser([]);
    }

    /**
     * Test getWalletModel method.
     *
     * @expectedException \Exception
     * @expectedExceptionMessage 没有设置钱包用户
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function testGetWalletModel()
    {
        $user = factory(UserModel::class)->make();
        $user->id = 1;

        // Create a wallet mock.
        $wallet = $this->getMockBuilder(Wallet::class)
                       ->setMethods(['walletFind'])
                       ->getMock();
        $wallet->expects($this->exactly(1))
               ->method('walletFind')
               ->willReturn(null);

        $wallet->setUser($user);
        $this->assertInstanceOf(WalletModel::class, $wallet->getWalletModel());

        // test exception.
        $wallet = new Wallet();
        $wallet->getWalletModel();
    }

    /**
     * Test increment method.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function testIncrement()
    {
        // Create a wallet model mock.
        $model = $this->getMockBuilder(WalletModel::class)
                      ->setMethods(['save'])
                      ->getMock();
        $model->expects($this->once())
              ->method('save')
              ->willReturn($model);

        // Create a Wallet mock.
        $wallet = $this->getMockBuilder(Wallet::class)
                       ->setMethods(['getWalletModel'])
                       ->getMock();
        $wallet->expects($this->exactly(3))
               ->method('getWalletModel')
               ->willReturn($model);

        $amount = 100;
        $wallet->increment($amount);

        $this->assertSame($amount, $wallet->getWalletModel()->balance);
        $this->assertSame($amount, $wallet->getWalletModel()->total_income);
    }

    /**
     * Test decrement method.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function testDecrement()
    {
        // Create a wallet model mock.
        $model = $this->getMockBuilder(WalletModel::class)
                      ->setMethods(['save'])
                      ->getMock();
        $model->expects($this->once())
              ->method('save')
              ->willReturn($model);

        // Create a Wallet mock.
        $wallet = $this->getMockBuilder(Wallet::class)
                       ->setMethods(['getWalletModel'])
                       ->getMock();
        $wallet->expects($this->exactly(3))
               ->method('getWalletModel')
               ->willReturn($model);

        $amount = 100;
        $wallet->decrement($amount);

        $this->assertSame(-$amount, $wallet->getWalletModel()->balance);
        $this->assertSame($amount, $wallet->getWalletModel()->total_expenses);
    }
}

class TestWalletSetUser extends Wallet
{
    public function getUser()
    {
        return $this->user;
    }
}
