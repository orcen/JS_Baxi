<?php
namespace C3\C3baxi\Tests\Unit\Controller;

/**
 * Test case.
 */
class FahrtZeitControllerTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \C3\C3baxi\Controller\FahrtZeitController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\C3\C3baxi\Controller\FahrtZeitController::class)
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
    public function listActionFetchesAllFahrtZeitsFromRepositoryAndAssignsThemToView()
    {

        $allFahrtZeits = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $fahrtZeitRepository = $this->getMockBuilder(\::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $fahrtZeitRepository->expects(self::once())->method('findAll')->will(self::returnValue($allFahrtZeits));
        $this->inject($this->subject, 'fahrtZeitRepository', $fahrtZeitRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('fahrtZeits', $allFahrtZeits);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenFahrtZeitToView()
    {
        $fahrtZeit = new \C3\C3baxi\Domain\Model\FahrtZeit();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('fahrtZeit', $fahrtZeit);

        $this->subject->showAction($fahrtZeit);
    }
}
