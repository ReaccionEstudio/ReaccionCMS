<?php

	namespace App\ReaccionEstudio\ReaccionCMSBundle\Services\Menu;

	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\Cache\Adapter\ApcuAdapter;
	use App\ReaccionEstudio\ReaccionCMSBundle\Entity\Menu;
	use App\ReaccionEstudio\ReaccionCMSAdminBundle\Services\Menu\MenuContentService;
	use App\ReaccionEstudio\ReaccionCMSBundle\Services\Themes\ThemeService;

	/**
	 * Menu service.
	 *
	 * @author Alberto Vian <alberto@reaccionestudio.com>
	 */
	class MenuService
	{
		/**
		 * @var ApcuAdapter
		 *
		 * APCu adapter
		 */
		private $cache;

		/**
		 * @var String
		 *
		 * Menu cache key
		 */
		private $cacheKey;

		/**
		 * @var EntityManagerInterface
		 *
		 * EntityManager
		 */
		private $em;

		/**
		 * @var MenuContentService
		 *
		 * EntityManager
		 */
		private $menuContentService;

		/**
		 * @var Twig
		 *
		 * Twig
		 */
		private $twig;

		/**
		 * @var ThemeService
		 *
		 * Theme service
		 */
		private $theme;

		/**
		 * Constructor
		 */
		public function __construct(EntityManagerInterface $em, MenuContentService $menuContentService, $twig, ThemeService $theme)
		{
			$this->em = $em;
			$this->twig = $twig;
			$this->theme = $theme;
			$this->menuContentService = $menuContentService;

			// cache
			$this->cache = new ApcuAdapter();
			$this->cacheKey = "navigation.menu";
		}

		/**
		 * Get menu HTML
		 *
		 * @return String 	$menuHtml 	Menu HTML
		 */
		public function getMenuHtml() : String
		{
			$menuHtml   = "";
			$cacheItem = $this->cache->getItem($this->cacheKey);

			if($cacheItem->isHit())
			{
				// key is cached
				$menuHtml = $cacheItem->get();
			}
			else
			{
				// update menu html cache
				$menuHtml = $this->updateMenuHtmlCache($cacheItem);
			}

			return $menuHtml;
		}

		/**
		 * Update menu html value for cache
		 *
		 * @param  CacheItem 	$cacheItem 		Cache item object
		 * @return String 		$menuHtml 		Menu HTML value
		 */
		public function updateMenuHtmlCache($cacheItem=null) : String
		{
			if($cacheItem == null)
			{
				$cacheItem = $this->cache->getItem($this->cacheKey);
			}

			// get menu html
			$menuHtml = $this->buildMenuHtml();

			// Save config value in cache
			$cacheItem->set($menuHtml);
			$this->cache->save($cacheItem);

			return $menuHtml;
		}

		/**
		 * Build menu html
		 *
		 * @return String 	[type] 		Menu Html
		 */
		private function buildMenuHtml() : String
		{
			// get menu items as nested array
			$nestedArray = $this->menuContentService->buildNestedArray(true);

			// get current theme views path
			$currentThemePath = $this->theme->generateRelativeTwigViewPath("layout/menu.html.twig");

			// get menu html
			return $this->twig->render($currentThemePath, [ 'menu' => $nestedArray ]);
		}
	}