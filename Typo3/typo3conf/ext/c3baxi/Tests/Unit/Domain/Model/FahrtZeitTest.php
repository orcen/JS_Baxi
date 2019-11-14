<?php
namespace C3\C3baxi\Tests\Unit\Domain\Model;

/**
 * Test case.
 */
class FahrtZeitTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \C3\C3baxi\Domain\Model\FahrtZeit
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \C3\C3baxi\Domain\Model\FahrtZeit();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getZeitReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getZeit()
        );
    }

    /**
     * @test
     */
    public function setZeitForIntSetsZeit()
    {
        $this->subject->setZeit(12);

        self::assertAttributeEquals(
            12,
            'zeit',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getFahrtReturnsInitialValueForFahrt()
    {
        self::assertEquals(
            null,
            $this->subject->getFahrt()
        );
    }

    /**
     * @test
     */
    public function setFahrtForFahrtSetsFahrt()
    {
        $fahrtFixture = new \C3\C3baxi\Domain\Model\Fahrt();
        $this->subject->setFahrt($fahrtFixture);

        self::assertAttributeEquals(
            $fahrtFixture,
            'fahrt',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getZoneReturnsInitialValueForZone()
    {
        self::assertEquals(
            null,
            $this->subject->getZone()
        );
    }

    /**
     * @test
     */
    public function setZoneForZoneSetsZone()
    {
        $zoneFixture = new \C3\C3baxi\Domain\Model\Zone();
        $this->subject->setZone($zoneFixture);

        self::assertAttributeEquals(
            $zoneFixture,
            'zone',
            $this->subject
        );
    }
}
