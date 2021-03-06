<?php

/**
 * This file is part of Gush.
 *
 * (c) Luis Cordova <cordoval@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gush\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Gush\Feature\GitHubFeature;
use Gush\Feature\TableFeature;

/**
 * Lists the labels for the issues
 *
 * @author Daniel Gomes <me@danielcsgomes.com>
 */
class IssueLabelListCommand extends BaseCommand implements TableFeature, GitHubFeature
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('issue:label:list')
            ->setDescription('Lists the issue\'s labels')
            ->setHelp(
                <<<EOF
The <info>%command.name%</info> command lists the issue's labels for either the current or the given organization
and repository:

    <info>$ gush %command.full_name%</info>
EOF
            )
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getTableDefaultLayout()
    {
        return 'compact';
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $org = $input->getOption('org');
        $repo = $input->getOption('repo');

        $client = $this->getGithubClient();

        $labels = $client->api('issue')->labels()->all($org, $repo);

        $table = $this->getHelper('table');
        $table->formatRows($labels, function ($label) {
            return [$label['name']];
        });
        $table->render($output, $table);

        return $labels;
    }
}
