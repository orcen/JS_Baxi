<?php
namespace C3\C3baxi\Tests\Unit\Domain\Model;

/**
 * Test case.
 */
class LinieTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \C3\C3baxi\Domain\Model\Linie
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \C3\C3baxi\Domain\Model\Linie();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getNrReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getNr()
        );
    }

    /**
     * @test
     */
    public function setNrForIntSetsNr()
    {
        $this->subject->setNr(12);

        self::assertAttributeEquals(
            12,
            'nr',
            $this->subject
        );
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
}
