<?php

namespace BitWasp\Bitcoin\Tests;

use BitWasp\Bitcoin\Bitcoin;
use BitWasp\Bitcoin\Script\ConsensusFactory;
use BitWasp\Bitcoin\Flags;
use BitWasp\Bitcoin\Script\Interpreter\InterpreterInterface;
use BitWasp\Bitcoin\Script\ScriptFactory;

class FlagsTest extends AbstractTestCase
{

    public function testBasicChecks()
    {
        $flags = new Flags(0);
        $this->assertFalse($flags->checkFlags(InterpreterInterface::VERIFY_P2SH));
        $this->assertFalse($flags->checkFlags(InterpreterInterface::VERIFY_STRICTENC));

        $flags = new Flags(InterpreterInterface::VERIFY_STRICTENC, false);
        $this->assertFalse($flags->checkFlags(InterpreterInterface::VERIFY_P2SH));
        $this->assertTrue($flags->checkFlags(InterpreterInterface::VERIFY_STRICTENC));
        $this->assertFalse($flags->checkFlags(InterpreterInterface::VERIFY_DERSIG));

        $flags = new Flags(InterpreterInterface::VERIFY_MINIMALDATA);
        $this->assertFalse($flags->checkFlags(InterpreterInterface::VERIFY_SIGPUSHONLY));
        $this->assertTrue($flags->checkFlags(InterpreterInterface::VERIFY_MINIMALDATA));
        $this->assertFalse($flags->checkFlags(InterpreterInterface::VERIFY_DISCOURAGE_UPGRADABLE_NOPS));
    }

    public function testDefaults()
    {
        $flags = ScriptFactory::defaultFlags();

        $this->assertTrue($flags->checkFlags(InterpreterInterface::VERIFY_P2SH));
        $this->assertTrue($flags->checkFlags(InterpreterInterface::VERIFY_STRICTENC));
        $this->assertTrue($flags->checkFlags(InterpreterInterface::VERIFY_DERSIG));
        $this->assertTrue($flags->checkFlags(InterpreterInterface::VERIFY_LOW_S));
        $this->assertTrue($flags->checkFlags(InterpreterInterface::VERIFY_NULL_DUMMY));
        $this->assertTrue($flags->checkFlags(InterpreterInterface::VERIFY_SIGPUSHONLY));
        $this->assertTrue($flags->checkFlags(InterpreterInterface::VERIFY_DISCOURAGE_UPGRADABLE_NOPS));
        $this->assertTrue($flags->checkFlags(InterpreterInterface::VERIFY_CLEAN_STACK));
    }
}
