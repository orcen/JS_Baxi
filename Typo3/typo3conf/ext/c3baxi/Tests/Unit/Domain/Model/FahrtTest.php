<?php
namespace C3\C3baxi\Tests\Unit\Domain\Model;

/**
 * Test case.
 */
class FahrtTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \C3\C3baxi\Domain\Model\Fahrt
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \C3\C3baxi\Domain\Model\Fahrt();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getNameReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getName()
        );
    }

    /**
     * @test
     */
    public function setNameForStringSetsName()
    {
        $this->subject->setName('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'name',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getLinieReturnsInitialValueForLinie()
    {
        self::assertEquals(
            null,
            $this->subject->getLinie()
        );
    }

    /**
     * @test
     */
    public function setLinieForLinieSetsLinie()
    {
        $linieFixture = new \C3\C3baxi\Domain\Model\Linie();
        $this->subject->setLinie($linieFixture);

        self::assertAttributeEquals(
            $linieFixture,
            'linie',
            $this->subject
        );
    }
}
