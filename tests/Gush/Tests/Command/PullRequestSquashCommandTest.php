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

use Gush\Command\PullRequestSquashCommand;

/**
 * @author Luis Cordova <cordoval@gmail.com>
 */
class PullRequestSquashCommandTest extends BaseTestCase
{
    public function testCommand()
    {
        $this->expectShowPullRequest();
        $processHelper = $this->expectProcessHelper();
        $tester = $this->getCommandTester($command = new PullRequestSquashCommand());
        $command->getHelperSet()->set($processHelper, 'process');
        $tester->execute(['--org' => 'cordoval', 'pr_number' => 40, '--no-comments' => true]);

        $this->assertEquals("Pull Request successfully merged.", trim($tester->getDisplay()));
    }

    protected function expectShowPullRequest()
    {
        $this->httpClient->whenGet('repos/cordoval/gush/pulls/40')
            ->thenReturn(
                [
                    'number' => 40,
                    'state' => "open",
                    'user' => ['login' => 'weaverryan'],
                    'assignee' => ['login' => 'cordoval'],
                    'pull_request' => [],
                    'milestone' => ['title' => "Conquer the world"],
                    'labels' => [['name' => 'actionable'], ['name' => 'easy pick']],
                    'title' => 'Write a behat test to launch strategy',
                    'body' => 'Help me conquer the world. Teach them to use gush.',
                    'base' => ['label' => 'master', 'ref' => '1234234'],
                    'head' => ['ref' => '43210987']
                 ]
            )
        ;
    }

    private function expectProcessHelper()
    {
        $processHelper = $this->getMock(
            'Gush\Helper\ProcessHelper',
            ['runCommands', 'probePhpCsFixer']
        );
        $processHelper->expects($this->once())
            ->method('probePhpCsFixer')
        ;
        $processHelper->expects($this->once())
            ->method('runCommands')
            ->with(
                [
                    'line' => 'git remote update',
                    'allow_failures' => true
                ],
                [
                    'line' => 'git checkout '.$head,
                    'allow_failures' => true
                ],
                [
                    'line' => 'git reset --soft '.$base,
                    'allow_failures' => true
                ],
                [
                    'line' => 'git commit -am '.$head,
                    'allow_failures' => true
                ],
                [
                    'line' => sprintf('git push -u %s %s -f', $username, $head),
                    'allow_failures' => true
                ],
            )
        ;

        return $processHelper;
    }
}
