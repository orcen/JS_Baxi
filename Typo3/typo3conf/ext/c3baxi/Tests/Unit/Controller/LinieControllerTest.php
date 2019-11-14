<?php
namespace C3\C3baxi\Tests\Unit\Controller;

/**
 * Test case.
 */
class LinieControllerTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \C3\C3baxi\Controller\LinieController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\C3\C3baxi\Controller\LinieController::class)
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
    public function listActionFetchesAllLiniesFromRepositoryAndAssignsThemToView()
    {

        $allLinies = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $linieRepository = $this->getMockBuilder(\::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $linieRepository->expects(self::once())->method('findAll')->will(self::returnValue($allLinies));
        $this->inject($this->subject, 'linieRepository', $linieRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('linies', $allLinies);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }
}
