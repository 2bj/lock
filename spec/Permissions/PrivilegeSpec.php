<?php
namespace spec\BeatSwitch\Lock\Permissions;

require __DIR__ . '/../Stubs/ResourceStub.php';

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use spec\BeatSwitch\Lock\Stubs\ResourceStub;

class PrivilegeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('edit', 'events', 1);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('BeatSwitch\Lock\Permissions\Privilege');
        $this->shouldImplement('BeatSwitch\Lock\Contracts\Permission');
    }

    function it_can_validate_itself_against_parameters()
    {
        $this->isAllowed('edit', 'events', 1)->shouldReturn(true);
        $this->isAllowed('edit', new ResourceStub('events', 1))->shouldReturn(true);
        $this->isAllowed('edit', 'events', 2)->shouldReturn(false);
        $this->isAllowed('delete', 'comments', 1)->shouldReturn(false);
    }

    function it_can_match_an_equal_permission()
    {
        $this->matchesPermission($this)->shouldReturn(true);
    }
}
