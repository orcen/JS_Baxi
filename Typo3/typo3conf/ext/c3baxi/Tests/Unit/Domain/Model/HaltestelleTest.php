<?php
namespace C3\C3baxi\Tests\Unit\Domain\Model;

/**
 * Test case.
 */
class HaltestelleTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \C3\C3baxi\Domain\Model\Haltestelle
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \C3\C3baxi\Domain\Model\Haltestelle();
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
    public function getLatitudeReturnsInitialValueForFloat()
    {
        self::assertSame(
            0.0,
            $this->subject->getLatitude()
        );
    }

    /**
     * @test
     */
    public function setLatitudeForFloatSetsLatitude()
    {
        $this->subject->setLatitude(3.14159265);

        self::assertAttributeEquals(
            3.14159265,
            'latitude',
            $this->subject,
            '',
            0.000000001
        );
    }

    /**
     * @test
     */
    public function getLongitudeReturnsInitialValueForFloat()
    {
        self::assertSame(
            0.0,
            $this->subject->getLongitude()
        );
    }

    /**
     * @test
     */
    public function setLongitudeForFloatSetsLongitude()
    {
        $this->subject->setLongitude(3.14159265);

        self::assertAttributeEquals(
            3.14159265,
            'longitude',
            $this->subject,
            '',
            0.000000001
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
