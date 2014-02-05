<?php

namespace Gush\Tests\Template\Pats;

use Gush\Template\Pats\PatTemplate;

class PatTemplateTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Gush\Template\Pats\PatTemplate */
    protected $template;

    public function setUp()
    {
        $this->template = new PatTemplate();
    }

    /**
     * @test
     */
    public function it_renders_string_with_placeholders_replaced()
    {
        $requirements = $this->template->getRequirements();

        foreach ($requirements as $key => $reqs) {
            list ($prompt, $default) = $reqs;
            if (!isset($params[$key])) {
                $params[$key] = $default;
            }
        }

        $params['description'] = 'This is a description';

        $this->template->bind($params);
        $res = $this->template->render();
        $this->assertEquals($expected, $res);
    }
}