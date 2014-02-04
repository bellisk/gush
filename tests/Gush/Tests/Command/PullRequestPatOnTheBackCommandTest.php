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
        $this->httpClient->whenGet('repos/cordoval/gush/pulls/7')
            ->thenReturn(
                [
                    'number' => 7,
                    'user' => ['login' => 'weaverryan'],
                ]
            )
        ;

        $this->httpClient->whenPost(
                'repos/cordoval/gush/issues/7/comments',
                json_encode(['body' => "Good catch @weaverryan, thanks for the patch."])
            )->thenReturn(
                [
                    'number' => 7,
                ]
            )
        ;

        $template = $this->expectTemplateHelper();
        $tester = $this->getCommandTester($command = new PullRequestPatOnTheBackCommand());
        $command->getHelperSet()->set($template, 'template');
        $tester->execute(['--org' => 'cordoval', '--repo' => 'gush', 'pr_number' => 7]);

        $this->assertEquals(OutputFixtures::PULL_REQUEST_PAT_ON_THE_BACK, trim($tester->getDisplay()));
    }

    private function expectTemplateHelper()
    {
        $template = $this->getMock(
            'Gush\Helper\TemplateHelper',
            ['bindAndRender']
        );
        $template->expects($this->once())
            ->method('bindAndRender')
            ->with(['author' => 'weaverryan'])
            ->will('x')
        ;

        return $template;
    }
}
