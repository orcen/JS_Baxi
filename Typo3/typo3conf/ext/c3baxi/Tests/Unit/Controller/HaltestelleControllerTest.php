<?php
namespace C3\C3baxi\Tests\Unit\Controller;

/**
 * Test case.
 */
class HaltestelleControllerTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \C3\C3baxi\Controller\HaltestelleController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\C3\C3baxi\Controller\HaltestelleController::class)
            ->setMethods(['redirect', 'forward', 'addFlashMessage'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function listActionFetchesAllHaltestellesFromRepositoryAndAssignsThemToView()
    {

        $allHaltestelles = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $haltestelleRepository = $this->getMockBuilder(\::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $haltestelleRepository->expects(self::once())->method('findAll')->will(self::returnValue($allHaltestelles));
        $this->inject($this->subject, 'haltestelleRepository', $haltestelleRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('haltestelles', $allHaltestelles);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenHaltestelleToView()
    {
        $haltestelle = new \C3\C3baxi\Domain\Model\Haltestelle();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('haltestelle', $haltestelle);

        $this->subject->showAction($haltestelle);
    }
}
