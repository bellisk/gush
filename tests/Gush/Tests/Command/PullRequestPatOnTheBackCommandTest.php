<?php

/**
 * This file is part of Gush.
 *
 * (c) Luis Cordova <cordoval@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gush\Tests\Command;

use Gush\Command\PullRequestPatOnTheBackCommand;
use Gush\Tests\Fixtures\OutputFixtures;

/**
 * @author Luis Cordova <cordoval@gmail.com>
 */
class PullRequestPatOnTheBackCommandTest extends BaseTestCase
{
    public function testCommand()
    {
        $this->httpClient->whenGet('repos/cordoval/gush/pulls/40')
            ->thenReturn(
                [
                    'number' => 60,
                    'state' => "open",
                    'user' => ['login' => 'weaverryan'],
                    'assignee' => ['login' => 'cordoval'],
                    'pull_request' => [],
                    'milestone' => ['title' => "Conquer the world"],
                    'labels' => [['name' => 'actionable'], ['name' => 'easy pick']],
                    'title' => 'Write a behat test to launch strategy',
                    'body' => 'Help me conquer the world. Teach them to use gush.',
                    'base' => ['label' => 'master']
                ]
            )
        ;

        $this->httpClient->whenPost(
                'repos/cordoval/gush/issues/40/comments',
                "{'org':'cordoval','repo':'gush','number':'40'}"
            )->thenReturn(
                [
                    'number' => 60,
                    'state' => "open",
                ]
            )
        ;

        $tester = $this->getCommandTester($command = new PullRequestPatOnTheBackCommand());
        $tester->execute([]);

        $this->assertEquals(OutputFixtures::PULL_REQUEST_PAT_ON_THE_BACK, trim($tester->getDisplay()));
    }
}
